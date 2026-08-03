/*
 * ZoomStore.SVGLoader
 *
 * Purpose: Sole module responsible for loading, caching, and parsing SVG assets.
 *   No other module should fetch SVG files directly.
 *
 * Responsibilities:
 *   - Fetch and cache the SVG catalog (index.json)
 *   - Fetch and cache individual SVG file content
 *   - Parse SVG strings (pass-through — Fabric.js handles actual parsing)
 *   - Provide category-aware batch loading
 *   - Fail gracefully on missing / broken SVGs
 *
 * Public API:
 *   init()                     → Promise  — fetch catalog
 *   getCatalog()               → object | null
 *   loadCategory(catId)        → Promise<Item[]>  — batch-fetch SVGs in a category
 *   getSvgText(catId, filename) → Promise<string> — get single SVG content
 *   hasSvg(catId, filename)    → boolean
 *   clearCache()               → void
 *   getStats()                 → { cachedSvgs, loadedCategories }
 *
 * Dependencies: ZoomStore.Cache.AssetCache
 *
 * Extension Points:
 *   - Replace AssetCache implementation (e.g. LRU) without changing this module
 *   - Add new catalog sources by extending the fetch logic
 *   - Support gzip / brotli decoding transparently
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var AssetCache = ns.Cache && ns.Cache.AssetCache;
    if (!AssetCache) {
        console.error("[SVGLoader] AssetCache not found");
        return;
    }

    function assetUrl(path) {
        var base = ns.baseUrl || '';
        if (base.endsWith('/') && path.startsWith('/')) return base + path.substring(1);
        return base + path;
    }

    var CATALOG_URL = assetUrl("/assets/design-assets/metadata/index.json");

    var catalog = null;
    var catalogPromise = null;
    var textCache = new AssetCache();
    var loadedCategories = {};

    function prefixedKey(catId, filename) {
        return "svg:" + catId + "/" + filename;
    }

    var SVGLoader = {
        init: function () {
            if (catalogPromise) return catalogPromise;

            catalogPromise = fetch(CATALOG_URL)
                .then(function (res) {
                    if (!res.ok) throw new Error("HTTP " + res.status);
                    return res.json();
                })
                .then(function (data) {
                    catalog = data;
                    return catalog;
                })
                .catch(function (err) {
                    console.warn("[SVGLoader] Failed to load catalog:", err);
                    catalog = { version: 0, updatedAt: "", providerVersion: 0, categories: [] };
                    return catalog;
                });

            return catalogPromise;
        },

        getCatalog: function () {
            return catalog;
        },

        loadCategory: function (catId) {
            var self = this;

            return this.init().then(function () {
                if (!catalog || !catalog.categories) return [];

                var cat = null;
                for (var i = 0; i < catalog.categories.length; i++) {
                    if (catalog.categories[i].id === catId) {
                        cat = catalog.categories[i];
                        break;
                    }
                }
                if (!cat || !cat.items || cat.items.length === 0) return [];

                var fetches = cat.items.map(function (item) {
                    var key = prefixedKey(catId, item.filename);

                    if (textCache.has(key)) return Promise.resolve();

                    var url = assetUrl("/assets/design-assets/svg/" + catId + "/" + item.filename);

                    return fetch(url)
                        .then(function (res) {
                            if (!res.ok) throw new Error("HTTP " + res.status);
                            return res.text();
                        })
                        .then(function (text) {
                            textCache.set(key, text);
                        })
                        .catch(function (err) {
                            console.warn("[SVGLoader] Failed to load " + url, err);
                            textCache.set(key, "");
                        });
                });

                return Promise.allSettled
                    ? Promise.allSettled(fetches).then(function () { return cat.items; })
                    : Promise.all(fetches.map(function (p) {
                        return p.catch(function () {});
                    })).then(function () { return cat.items; });
            });
        },

        getSvgText: function (catId, item) {
            var filename = (typeof item === "string") ? item : (item.filename || "");
            if (!filename) return Promise.resolve("");

            var key = prefixedKey(catId, filename);

            if (textCache.has(key)) {
                return Promise.resolve(textCache.get(key));
            }

            var self = this;
            var url = assetUrl("/assets/design-assets/svg/" + catId + "/" + filename);

            return fetch(url)
                .then(function (res) {
                    if (!res.ok) throw new Error("HTTP " + res.status);
                    return res.text();
                })
                .then(function (text) {
                    textCache.set(key, text);
                    return text;
                })
                .catch(function (err) {
                    console.warn("[SVGLoader] getSvgText failed:", url, err);
                    textCache.set(key, "");
                    return "";
                });
        },

        getCachedSvgText: function (catId, filename) {
            var key = prefixedKey(catId, filename);
            if (textCache.has(key)) return Promise.resolve(textCache.get(key));
            return this.getSvgText(catId, { filename: filename });
        },

        hasSvg: function (catId, filename) {
            return textCache.has(prefixedKey(catId, filename));
        },

        clearCache: function () {
            textCache.clear();
            loadedCategories = {};
        },

        getStats: function () {
            return {
                cachedSvgs: textCache.size,
                loadedCategories: Object.keys(loadedCategories).length
            };
        }
    };

    ns.SVGLoader = SVGLoader;
})(window.ZoomStore);
