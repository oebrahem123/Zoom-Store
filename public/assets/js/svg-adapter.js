/*
 * ZoomStore.SVGAdapter
 *
 * Purpose: Adapter for SVG assets that implements the AssetManager adapter interface.
 *   Internally wraps two SVG sources:
 *     - Legacy: inline SVG strings from DesignArtLib (backward compatible)
 *     - File:   file-based SVGs loaded via SVGLoader from /assets/design-assets/svg/
 *   AssetManager never knows about these internal sources.
 *
 * Responsibilities:
 *   - Provide unified categories from legacy + file sources
 *   - Load category items (inline strings or fetched files)
 *   - Add SVGs to a Fabric.js canvas (both legacy and file sources)
 *   - Search across all SVG sources
 *   - Fail gracefully if one source is unavailable
 *
 * Public API (adapter interface):
 *   type                    — 'svg'
 *   name                    — 'SVG Library'
 *   version                 — 1
 *   init()                  → Promise
 *   getCategories()         → Category[]
 *   getCategoryItems(catId) → Promise<Item[]>
 *   addToCanvas(catId, itemId, canvas) → Promise<fabricObject|null>
 *   search(query)           → Promise<Result[]>
 *
 * Dependencies: ZoomStore.SVGLoader, window.DesignArtLib
 *
 * Extension Points:
 *   - Add new internal sources (e.g. 'ai', 'cloud') without changing the adapter interface
 *   - Add thumbnail generation for file-based SVGs
 *   - Support multi-color SVG flag for color picker behavior
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var SVGLoader = ns.SVGLoader;
    if (!SVGLoader) {
        console.error("[SVGAdapter] SVGLoader not found");
        return;
    }

    var categoriesCache = null;

    /*
     * Build a merged category list from DesignArtLib (legacy) + catalog (file).
     */
    function buildCategories() {
        var map = {};
        var order = 0;

        // ── Legacy categories from DesignArtLib ──
        if (window.DesignArtLib && DesignArtLib.artCategories) {
            DesignArtLib.artCategories.forEach(function (cat) {
                var id = cat.id || ("legacy-" + order);
                map[id] = {
                    id: id,
                    name: cat.name || cat.nameAr || id,
                    nameAr: cat.nameAr || cat.name || "",
                    source: "legacy",
                    displayOrder: cat.displayOrder != null ? cat.displayOrder : order,
                    icon: cat.icon || ""
                };
                order++;
            });
        }

        // ── File categories from catalog ──
        var catalog = SVGLoader.getCatalog();
        if (catalog && catalog.categories) {
            catalog.categories.forEach(function (cat) {
                var id = cat.id;
                if (map[id]) {
                    // Category exists in both — prefer file source but keep legacy as fallback
                    map[id].source = "both";
                    map[id].displayOrder = Math.min(map[id].displayOrder, cat.displayOrder || 99);
                } else {
                    map[id] = {
                        id: id,
                        name: cat.name || cat.nameAr || id,
                        nameAr: cat.nameAr || cat.name || "",
                        source: "file",
                        displayOrder: cat.displayOrder || 99,
                        icon: cat.icon || ""
                    };
                }
            });
        }

        var categories = [];
        for (var key in map) {
            if (map.hasOwnProperty(key)) {
                categories.push(map[key]);
            }
        }

        categories.sort(function (a, b) {
            return (a.displayOrder || 0) - (b.displayOrder || 0);
        });

        return categories;
    }

    function defaultCapabilities() {
        return { supportsColor: true, supportsRecolor: true, supportsStroke: true, supportsShadow: true };
    }

    function lookupCapabilities(catId, itemId, filename) {
        var catalog = SVGLoader.getCatalog();
        if (catalog && catalog.categories) {
            for (var ci = 0; ci < catalog.categories.length; ci++) {
                if (catalog.categories[ci].id === catId && catalog.categories[ci].items) {
                    for (var ii = 0; ii < catalog.categories[ci].items.length; ii++) {
                        var item = catalog.categories[ci].items[ii];
                        if (item.filename === filename || item.id === itemId) {
                            return item.capabilities || defaultCapabilities();
                        }
                    }
                    break;
                }
            }
        }
        return defaultCapabilities();
    }

    var SVGAdapter = {
        type: "svg",
        name: "SVG Library",
        version: 1,

        init: function () {
            categoriesCache = null;
            return SVGLoader.init().then(function () {
                categoriesCache = buildCategories();
            }).catch(function (err) {
                console.warn("[SVGAdapter] Init failed, using legacy-only categories:", err);
                categoriesCache = buildCategories();
            });
        },

        getCategories: function () {
            if (categoriesCache) return categoriesCache;
            var cats = buildCategories();
            // Only cache when catalog data is available — prevents stale
            // legacy-only cache from race condition on first render
            var catalog = SVGLoader.getCatalog();
            if (catalog && catalog.categories && catalog.categories.length > 0) {
                categoriesCache = cats;
            }
            return cats;
        },

        getCategoryItems: function (catId) {
            if (!catId) return Promise.resolve([]);

            var cat = null;
            var cats = this.getCategories();
            for (var i = 0; i < cats.length; i++) {
                if (cats[i].id === catId) { cat = cats[i]; break; }
            }
            if (!cat) return Promise.resolve([]);

            var source = cat.source;

            // ── Legacy items ──
            if (source === "legacy" || source === "both") {
                if (window.DesignArtLib && DesignArtLib.artCategories) {
                    for (var j = 0; j < DesignArtLib.artCategories.length; j++) {
                        var legacyCat = DesignArtLib.artCategories[j];
                        if ((legacyCat.id === catId || legacyCat.name === cat.name) && legacyCat.items) {
                            var items = legacyCat.items.map(function (itemKey) {
                                return {
                                    id: itemKey,
                                    title: itemKey,
                                    titleAr: "",
                                    category: catId,
                                    source: "legacy",
                                    thumbnail: "",
                                    svgContent: DesignArtLib.svgIcons[itemKey] || "",
                                    capabilities: defaultCapabilities()
                                };
                            });

                            // If also has file items, merge
                            if (source === "both") {
                                return SVGLoader.loadCategory(catId).then(function (fileItems) {
                                    var fileMapped = (fileItems || []).map(function (fi) {
                                        return {
                                            id: fi.id || fi.filename.replace(/\.svg$/, ""),
                                            title: fi.title || "",
                                            titleAr: fi.titleAr || "",
                                            category: catId,
                                            source: "file",
                                            thumbnail: fi.thumbnail || "",
                                            svgContent: null,
                                            filename: fi.filename,
                                            capabilities: lookupCapabilities(catId, fi.id, fi.filename)
                                        };
                                    });
                                    return items.concat(fileMapped);
                                });
                            }

                            return Promise.resolve(items);
                        }
                    }
                }
            }

            // ── File items ──
            return SVGLoader.loadCategory(catId).then(function (fileItems) {
                return (fileItems || []).map(function (fi) {
                    return {
                        id: fi.id || fi.filename.replace(/\.svg$/, ""),
                        title: fi.title || "",
                        titleAr: fi.titleAr || "",
                        category: catId,
                        source: "file",
                        thumbnail: fi.thumbnail || "",
                        svgContent: null,
                        filename: fi.filename,
                        capabilities: lookupCapabilities(catId, fi.id, fi.filename)
                    };
                });
            });
        },

        addToCanvas: function (catId, itemId, canvas, options) {
            if (!canvas || !itemId) return Promise.resolve(null);

            var self = this;
            var cat = null;
            var cats = this.getCategories();
            for (var i = 0; i < cats.length; i++) {
                if (cats[i].id === catId) { cat = cats[i]; break; }
            }
            options = options || {};

            function buildAssetMeta(catId, itemId) {
                return {
                    version: 1,
                    adapter: "svg",
                    category: catId,
                    assetId: itemId
                };
            }

            function applyAssetProperties(obj, catId, itemId, filename) {
                var caps = lookupCapabilities(catId, itemId, filename);
                var color = options.color || "#ffffff";
                obj.set({
                    left: options.left || 150,
                    top: options.top || 150,
                    originX: options.originX || "center",
                    originY: options.originY || "center",
                    scaleX: 1,
                    scaleY: 1,
                    stroke: caps.supportsColor ? color : "",
                    fill: "",
                    hasControls: true,
                    hasBorders: true
                });
                obj._assetMeta = buildAssetMeta(catId, itemId);
                obj._capabilities = caps;
                obj._artColor = caps.supportsColor ? color : "";
                obj._embossLevel = options.emboss || 0;
                if (window.ZoomStore && ZoomStore.ColorManager) {
                    ZoomStore.ColorManager.fixChildren(obj, color);
                }
            }

            // ── Try legacy first ──
            if (window.DesignArtLib && DesignArtLib.svgIcons && DesignArtLib.svgIcons[itemId]) {
                return new Promise(function (resolve) {
                    var svgStr = DesignArtLib.svgIcons[itemId];
                    if (!svgStr) { resolve(null); return; }

                    fabric.loadSVGFromString(svgStr, function (objects, options) {
                        try {
                            // ── SVG geometry measurement ──
                            var vbMatch = svgStr.match(/viewBox\s*=\s*["']([^"']*)["']/i);
                            console.log('[SVG_MEASURE] itemId:', itemId);
                            console.log('[SVG_MEASURE] Raw SVG viewBox:', vbMatch ? vbMatch[1] : '(none)');
                            console.log('[SVG_MEASURE] Parsed options (width/height from SVG):', JSON.parse(JSON.stringify(options)));
                            console.log('[SVG_MEASURE] Object count from loadSVGFromString:', objects.length);
                            objects.forEach(function(o, idx) {
                                var br = o.getBoundingRect ? o.getBoundingRect() : null;
                                console.log('[SVG_MEASURE] Object[' + idx + '] type:', o.type, 'left:', o.left, 'top:', o.top, 'width:', o.width, 'height:', o.height, 'scaleX:', o.scaleX, 'scaleY:', o.scaleY, 'boundingRect:', br ? JSON.stringify(br) : 'N/A');
                            });

                            var obj = fabric.util.groupSVGElements(objects, {});
                            console.log('[SVG_MEASURE] Final group — left:', obj.left, 'top:', obj.top, 'width:', obj.width, 'height:', obj.height, 'scaleX:', obj.scaleX, 'scaleY:', obj.scaleY);
                            var gbr = obj.getBoundingRect ? obj.getBoundingRect() : null;
                            console.log('[SVG_MEASURE] Final group boundingRect:', gbr ? JSON.stringify(gbr) : 'N/A');
                            console.log('[SVG_MEASURE] SVG viewBox area:', options.width && options.height ? (options.width * options.height) : 'N/A', 'Group area:', (obj.width * obj.height));

                            applyAssetProperties(obj, catId, itemId, itemId + ".svg");

                            if (window.applyCustomControls) {
                                window.applyCustomControls(obj);
                            }

                            resolve(obj);
                        } catch (err) {
                            console.warn("[SVGAdapter] Failed to create fabric object from legacy SVG:", itemId, err);
                            resolve(null);
                        }
                    });
                });
            }

            // ── File-based SVG ──
            var filename = itemId;
            if (!filename.endsWith(".svg")) filename = filename + ".svg";

            return SVGLoader.getSvgText(catId, { filename: filename }).then(function (svgText) {
                if (!svgText) return null;

                return new Promise(function (resolve) {
                    fabric.loadSVGFromString(svgText, function (objects, options) {
                        try {
                            // ── SVG geometry measurement ──
                            var vbMatch = svgText.match(/viewBox\s*=\s*["']([^"']*)["']/i);
                            console.log('[SVG_MEASURE] itemId:', itemId, 'filename:', filename);
                            console.log('[SVG_MEASURE] Raw SVG viewBox:', vbMatch ? vbMatch[1] : '(none)');
                            console.log('[SVG_MEASURE] Parsed options:', JSON.parse(JSON.stringify(options)));
                            console.log('[SVG_MEASURE] Object count:', objects.length);
                            objects.forEach(function(o, idx) {
                                var br = o.getBoundingRect ? o.getBoundingRect() : null;
                                console.log('[SVG_MEASURE] Object[' + idx + '] type:', o.type, 'left:', o.left, 'top:', o.top, 'width:', o.width, 'height:', o.height, 'scaleX:', o.scaleX, 'scaleY:', o.scaleY, 'br:', br ? JSON.stringify(br) : 'N/A');
                            });

                            var obj = fabric.util.groupSVGElements(objects, {});
                            console.log('[SVG_MEASURE] Final group — left:', obj.left, 'top:', obj.top, 'width:', obj.width, 'height:', obj.height, 'scaleX:', obj.scaleX, 'scaleY:', obj.scaleY);
                            var gbr = obj.getBoundingRect ? obj.getBoundingRect() : null;
                            console.log('[SVG_MEASURE] Final group boundingRect:', gbr ? JSON.stringify(gbr) : 'N/A');

                            applyAssetProperties(obj, catId, itemId, filename);

                            if (window.applyCustomControls) {
                                window.applyCustomControls(obj);
                            }

                            resolve(obj);
                        } catch (err) {
                            console.warn("[SVGAdapter] Failed to create fabric object from file SVG:", filename, err);
                            resolve(null);
                        }
                    });
                });
            });
        },

        search: function (query) {
            if (!query || query.trim() === "") return Promise.resolve([]);
            var q = query.toLowerCase().trim();
            var results = [];
            var seen = {};

            // ── Search legacy ──
            if (window.DesignArtLib && DesignArtLib.svgIcons) {
                Object.keys(DesignArtLib.svgIcons).forEach(function (key) {
                    if (key.toLowerCase().indexOf(q) !== -1 && !seen[key]) {
                        seen[key] = true;
                        results.push({
                            id: key,
                            source: "svg",
                            adapter: "svg",
                            category: "Legacy",
                            title: key,
                            titleAr: "",
                            thumbnail: "",
                            score: 2,
                            metadata: {},
                            _catId: "svg:legacy"
                        });
                    }
                });
            }

            // ── Search file catalog ──
            var catalog = SVGLoader.getCatalog();
            if (catalog && catalog.categories) {
                catalog.categories.forEach(function (cat) {
                    (cat.items || []).forEach(function (item) {
                        var id = item.id || item.filename.replace(/\.svg$/, "");
                        if (seen[id]) return;

                        var titleMatch = (item.title || "").toLowerCase().indexOf(q) !== -1;
                        var titleArMatch = (item.titleAr || "").toLowerCase().indexOf(q) !== -1;
                        var keywords = item.keywords || item.keywordsAr || [];
                        var keywordMatch = Array.isArray(keywords) && keywords.some(function (k) {
                            return k.toLowerCase().indexOf(q) !== -1;
                        });

                        if (titleMatch || titleArMatch || keywordMatch) {
                            seen[id] = true;
                            results.push({
                                id: id,
                                source: "svg",
                                adapter: "svg",
                                category: cat.name || cat.id,
                                title: item.title || id,
                                titleAr: item.titleAr || "",
                                thumbnail: item.thumbnail || "",
                                score: titleMatch ? 3 : (titleArMatch ? 2 : 1),
                                metadata: item,
                                _catId: "svg:" + cat.id
                            });
                        }
                    });
                });
            }

            results.sort(function (a, b) { return (b.score || 0) - (a.score || 0); });
            return Promise.resolve(results);
        }
    };

    ns.SVGAdapter = SVGAdapter;
})(window.ZoomStore);
