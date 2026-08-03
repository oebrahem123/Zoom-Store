/*
 * ZoomStore.FontManager
 *
 * Purpose: Central font system manager.
 *   The editor never communicates directly with any font provider.
 *   Only FontManager knows how fonts are loaded.
 *
 * Responsibilities:
 *   - Load core font families on app startup
 *   - Load individual fonts on demand via the configured provider
 *   - Track loaded / loading state per family
 *   - Populate a <select> element from catalog + user preferences
 *   - Manage recently-used fonts (localStorage)
 *   - Manage favorite fonts (localStorage)
 *   - Filter / search fonts by name or category
 *
 * Public API:
 *   init(coreFamilies)         → Promise  — load core families, bootstrap
 *   loadFont(family)           → Promise  — load a single family on demand
 *   isLoaded(family)           → boolean
 *   isLoading(family)          → boolean
 *   getFontById(id)            → font entry | undefined
 *   getFontByFamily(family)    → font entry | undefined
 *   getFontsByCategory(catId)  → font[]
 *   getCategoryCounts()        → { catId: count }
 *   getCategories()            → category[]
 *   populateSelect(selectEl)   → void     — fill <select> with options
 *   syncSelect(selectEl, family) → void   — select correct option
 *   getRecent()                → string[] — families from localStorage
 *   addRecent(family)          → void
 *   getFavs()                  → string[] — families from localStorage
 *   toggleFav(family)          → boolean  — new state (true = favorited)
 *   isFav(family)              → boolean
 *   search(query)              → font[]
 *   onFontReady(family)        → callback — called after a font finishes loading
 *
 * Dependencies: ZoomStore.FontCatalog
 *
 * Extension Points:
 *   - Add new provider in FontCatalog.providers (code-free FontManager change)
 *   - onFontReady callback allows UI to react after async font load
 *   - Preferences storage can be swapped from localStorage to IndexedDB
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var catalog = ns.FontCatalog;
    if (!catalog) {
        console.error("[FontManager] FontCatalog not found");
        return;
    }

    var loaded = {};
    var loading = {};

    var STORAGE_PREFIX = "zf_";
    var STORAGE_RECENT = STORAGE_PREFIX + "recent";
    var STORAGE_FAVS = STORAGE_PREFIX + "favs";

    // ── internal helpers ──

    function readArray(key) {
        try {
            var raw = localStorage.getItem(key);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function writeArray(key, arr) {
        try {
            localStorage.setItem(key, JSON.stringify(arr));
        } catch (e) {
            /* quota exceeded — silently ignore */
        }
    }

    function buildVariantString(variants) {
        if (!variants || variants.length === 0) return "400";
        return variants.map(function (v) {
            return v === "400" ? "400" : v;
        }).join(",");
    }

    // ── public API ──

    var FontManager = {
        init: function (coreFamilies) {
            if (!coreFamilies || coreFamilies.length === 0) return Promise.resolve();

            var providerKeys = {};
            coreFamilies.forEach(function (family) {
                var entry = FontManager.getFontByFamily(family);
                if (entry && entry.provider) {
                    if (!providerKeys[entry.provider]) providerKeys[entry.provider] = [];
                    var variants = entry.variants || ["400"];
                    providerKeys[entry.provider].push(family + ":" + buildVariantString(variants));
                }
            });

            var promises = Object.keys(providerKeys).map(function (providerName) {
                var provider = catalog.providers[providerName];
                if (!provider || !provider.load) return Promise.resolve();
                return provider.load(providerKeys[providerName]).then(function () {
                    var readyPromises = [];
                    providerKeys[providerName].forEach(function (str) {
                        var family = str.split(":")[0];
                        if (typeof document !== 'undefined' && document.fonts && document.fonts.load) {
                            readyPromises.push(
                                document.fonts.load('1em ' + family).catch(function () {})
                            );
                        }
                    });
                    return Promise.all(readyPromises).then(function () {
                        providerKeys[providerName].forEach(function (str) {
                            var family = str.split(":")[0];
                            loaded[family] = true;
                            if (FontManager.onFontReady) FontManager.onFontReady(family);
                        });
                    });
                });
            });

            var self = this;
            return Promise.all(promises).then(function () {
                coreFamilies.forEach(function (family) {
                    self.addRecent(family);
                });
            });
        },

        loadFont: function (family) {
            console.log('[J6] loadFont() ENTER — family:', family, 'isLoaded:', !!loaded[family], 'isLoading:', !!loading[family]);
            if (loaded[family]) {
                console.log('[J6] loadFont() — CACHED, family:', family);
                if (FontManager.onFontReady) {
                    console.log('[J6] loadFont() — calling onFontReady for cached family:', family);
                    FontManager.onFontReady(family);
                }
                return Promise.resolve();
            }
            if (loading[family]) {
                console.log('[J6] loadFont() — ALREADY LOADING, returning existing promise for:', family);
                return loading[family];
            }

            var entry = FontManager.getFontByFamily(family);
            if (!entry || !entry.provider) {
                loaded[family] = true;
                return Promise.resolve();
            }

            var provider = catalog.providers[entry.provider];
            if (!provider || !provider.load) {
                loaded[family] = true;
                return Promise.resolve();
            }

            var variants = entry.variants || ["400"];
            var familyStr = family + ":" + buildVariantString(variants);
            var self = this;

            console.log('[J6] loadFont() — calling provider.load for:', familyStr);
            loading[family] = provider.load([familyStr]).then(function () {
                if (typeof document !== 'undefined' && document.fonts && document.fonts.load) {
                    return document.fonts.load('1em ' + family).then(function () {
                        loaded[family] = true;
                        delete loading[family];
                        console.log('[J6] loadFont() — SUCCESS (fonts.load), family:', family, 'onFontReady:', typeof self.onFontReady);
                        if (self.onFontReady) self.onFontReady(family);
                    }).catch(function () {
                        loaded[family] = true;
                        delete loading[family];
                        console.log('[J6] loadFont() — fonts.load FAILED, marking loaded anyway, family:', family);
                        if (self.onFontReady) self.onFontReady(family);
                    });
                }
                loaded[family] = true;
                delete loading[family];
                console.log('[J6] loadFont() — SUCCESS (no fonts.load API), family:', family);
                if (self.onFontReady) self.onFontReady(family);
            });

            return loading[family];
        },

        isLoaded: function (family) {
            return !!loaded[family];
        },

        isLoading: function (family) {
            return !!loading[family];
        },

        getFontById: function (id) {
            for (var i = 0; i < catalog.fonts.length; i++) {
                if (catalog.fonts[i].id === id) return catalog.fonts[i];
            }
            return undefined;
        },

        getFontByFamily: function (family) {
            for (var i = 0; i < catalog.fonts.length; i++) {
                if (catalog.fonts[i].family === family) return catalog.fonts[i];
            }
            return undefined;
        },

        getFontsByCategory: function (catId) {
            return catalog.fonts.filter(function (f) { return f.category === catId; });
        },

        getCategoryCounts: function () {
            var counts = {};
            catalog.fonts.forEach(function (f) {
                counts[f.category] = (counts[f.category] || 0) + 1;
            });
            return counts;
        },

        getCategories: function () {
            return catalog.categories;
        },

        populateSelect: function (selectEl) {
            if (!selectEl) return;

            var html = "";

            // ── Recently used ──
            var recent = FontManager.getRecent();
            if (recent.length > 0) {
                html += '<optgroup label="—— Recently Used ——">';
                recent.forEach(function (family) {
                    var entry = FontManager.getFontByFamily(family);
                    var label = entry ? family : family;
                    var selected = selectEl.value === family ? " selected" : "";
                    var style = "font-family: '" + family.replace(/'/g, "") + "'";
                    html += '<option value="' + family.replace(/"/g, "&quot;") + '"' + selected + ' style="' + style + '">' + label + "</option>";
                });
                html += "</optgroup>";
            }

            // ── Favorites ──
            var favs = FontManager.getFavs();
            if (favs.length > 0) {
                html += '<optgroup label="—— Favorites ——">';
                favs.forEach(function (family) {
                    var selected = selectEl.value === family ? " selected" : "";
                    var style = "font-family: '" + family.replace(/'/g, "") + "'";
                    html += '<option value="' + family.replace(/"/g, "&quot;") + '"' + selected + ' style="' + style + '">' + family + "</option>";
                });
                html += "</optgroup>";
            }

            // ── Categories ──
            catalog.categories.forEach(function (cat) {
                var catFonts = FontManager.getFontsByCategory(cat.id);
                if (catFonts.length === 0) return;

                html += '<optgroup label="—— ' + cat.name + ' ——">';
                catFonts.forEach(function (f) {
                    var selected = selectEl.value === f.family ? " selected" : "";
                    var style = "font-family: '" + f.family.replace(/'/g, "") + "'";
                    html += '<option value="' + f.family.replace(/"/g, "&quot;") + '"' + selected + ' style="' + style + '">' + f.family + "</option>";
                });
                html += "</optgroup>";
            });

            selectEl.innerHTML = html;
        },

        syncSelect: function (selectEl, family) {
            if (!selectEl) return;

            var found = false;
            for (var i = 0; i < selectEl.options.length; i++) {
                if (selectEl.options[i].value === family) {
                    selectEl.value = family;
                    found = true;
                    break;
                }
            }

            if (!found && family) {
                var opt = document.createElement("option");
                opt.value = family;
                opt.textContent = family;
                opt.selected = true;
                selectEl.insertBefore(opt, selectEl.firstChild);
            }
        },

        getRecent: function () {
            return readArray(STORAGE_RECENT);
        },

        addRecent: function (family) {
            if (!family) return;
            var list = FontManager.getRecent();
            var idx = list.indexOf(family);
            if (idx !== -1) list.splice(idx, 1);
            list.unshift(family);
            if (list.length > 5) list.length = 5;
            writeArray(STORAGE_RECENT, list);
        },

        getFavs: function () {
            return readArray(STORAGE_FAVS);
        },

        toggleFav: function (family) {
            if (!family) return false;
            var list = FontManager.getFavs();
            var idx = list.indexOf(family);
            if (idx !== -1) {
                list.splice(idx, 1);
                writeArray(STORAGE_FAVS, list);
                return false;
            } else {
                list.push(family);
                writeArray(STORAGE_FAVS, list);
                return true;
            }
        },

        isFav: function (family) {
            return FontManager.getFavs().indexOf(family) !== -1;
        },

        search: function (query) {
            if (!query || query.trim() === "") {
                return catalog.fonts;
            }
            var q = query.toLowerCase().trim();
            return catalog.fonts.filter(function (f) {
                return f.family.toLowerCase().indexOf(q) !== -1 ||
                    f.category.toLowerCase().indexOf(q) !== -1 ||
                    f.id.indexOf(q) !== -1;
            });
        },

        onFontReady: null
    };

    ns.FontManager = FontManager;
})(window.ZoomStore);
