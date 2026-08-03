/*
 * ZoomStore.AssetManager
 *
 * Purpose: Generic coordinator for all asset types.
 *   AssetManager never knows about SVGs, images, frames, or any specific asset.
 *   Every asset type is encapsulated behind an adapter that implements a standard interface.
 *   Future adapters (Video, Pattern, Template, Mockup) are added without modifying this module.
 *
 * Responsibilities:
 *   - Register and manage adapters
 *   - Merge categories across all adapters (with adapter-prefixed unique IDs)
 *   - Delegate item loading to the correct adapter
 *   - Delegate add-to-canvas to the correct adapter
 *   - Provide adapter-agnostic search by delegating to adapters
 *   - Never check adapter.type, never use switch/case on asset type
 *
 * Adapter Interface:
 *   type          — string (unique, e.g. 'svg', 'image')
 *   name          — human-readable label
 *   version       — number
 *   init()        — Promise (called on registration)
 *   destroy()     — void (optional cleanup)
 *   getCategories() — Promise<Category[]> | Category[]
 *   getCategoryItems(catId) — Promise<Item[]>
 *   addToCanvas(catId, itemId, canvas) — Promise<fabricObject|null>
 *   search(query) — Promise<Result[]> (optional)
 *   getThumbnail(item) — Promise<string> (optional)
 *
 * Category format (with unique IDs):
 *   { id: 'svg:sports', originalId: 'sports', name, nameAr, source: 'file', adapter: 'svg', displayOrder, icon }
 *
 * Item format:
 *   { id: 'svg:lion', title, titleAr, category: 'svg:animals', source, adapter, thumbnail, metadata }
 *
 * Dependencies: None
 *
 * Extension Points:
 *   - registerAdapter(adapter) to add any new asset type
 *   - Adapter interface remains stable; adapters evolve independently
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var adapters = {};
    var initPromise = null;

    var AssetManager = {
        /*
         * Register an adapter. Calls adapter.init() and stores it.
         * Can be called before or after AssetManager.init().
         */
        registerAdapter: function (adapter) {
            if (!adapter || !adapter.type) {
                console.warn("[AssetManager] Invalid adapter (missing type)");
                return;
            }
            adapters[adapter.type] = adapter;

            if (adapter.init) {
                var result = adapter.init();
                if (result && typeof result.then === "function") {
                    result.catch(function (err) {
                        console.warn("[AssetManager] Adapter " + adapter.type + " init failed:", err);
                    });
                }
            }
        },

        unregisterAdapter: function (type) {
            var adapter = adapters[type];
            if (adapter && adapter.destroy) adapter.destroy();
            delete adapters[type];
        },

        /*
         * Initialize all registered adapters.
         */
        init: function () {
            if (initPromise) return initPromise;

            var typeNames = Object.keys(adapters);
            var inits = typeNames.map(function (type) {
                var adapter = adapters[type];
                if (adapter.init) {
                    var result = adapter.init();
                    if (result && typeof result.then === "function") {
                        return result.catch(function (err) {
                            console.warn("[AssetManager] Adapter " + type + " init failed:", err);
                        });
                    }
                }
                return Promise.resolve();
            });

            initPromise = Promise.all(inits).then(function () {
                return true;
            });

            return initPromise;
        },

        /*
         * Return merged categories from all adapters.
         * Multiple adapters may have categories with the same display name
         * (e.g. 'sports'), so each category gets a globally unique ID prefixed
         * with the adapter type.
         */
        getCategories: function () {
            var all = [];
            var typeNames = Object.keys(adapters);

            typeNames.forEach(function (type) {
                var adapter = adapters[type];
                if (!adapter.getCategories) return;

                var cats = adapter.getCategories();
                if (!cats) return;

                // Handle both sync and async
                if (cats.then && typeof cats.then === "function") {
                    // Queue async resolution
                    cats.then(function (resolved) {
                        // Will be collected on next call after async resolves
                    });
                    return;
                }

                for (var i = 0; i < cats.length; i++) {
                    var cat = cats[i];
                    all.push({
                        id: type + ":" + cat.id,
                        originalId: cat.id,
                        name: cat.name || "",
                        nameAr: cat.nameAr || "",
                        source: cat.source || type,
                        adapter: type,
                        displayOrder: cat.displayOrder || 0,
                        icon: cat.icon || ""
                    });
                }
            });

            all.sort(function (a, b) { return (a.displayOrder || 0) - (b.displayOrder || 0); });
            return all;
        },

        /*
         * Get items for a specific category.
         * catId format: 'adapterType:originalCatId'
         * Dispatches to the correct adapter based on the prefix.
         */
        getCategoryItems: function (catId) {
            if (!catId) return Promise.resolve([]);

            var parts = catId.split(":");
            var adapterType = parts[0];
            var originalCatId = parts.slice(1).join(":");

            // Also try matching the full catId as-is (for categories without prefix)
            var adapter = adapters[adapterType];
            if (!adapter || !adapter.getCategoryItems) {
                // Fallback: try to find adapter by scanning
                var typeNames = Object.keys(adapters);
                for (var i = 0; i < typeNames.length; i++) {
                    var alt = adapters[typeNames[i]];
                    if (alt.getCategoryItems) {
                        try {
                            var result = alt.getCategoryItems(catId);
                            if (result && typeof result.then === "function") return result;
                        } catch (e) {
                            /* skip */
                        }
                    }
                }
                return Promise.resolve([]);
            }

            try {
                var result = adapter.getCategoryItems(originalCatId || catId);
                if (result && typeof result.then === "function") return result;
                return Promise.resolve(result || []);
            } catch (err) {
                console.warn("[AssetManager] getCategoryItems error for " + catId, err);
                return Promise.resolve([]);
            }
        },

        /*
         * Add an asset to the canvas.
         * catId format: 'adapterType:originalCatId'
         * itemId format: 'adapterType:originalItemId'
         */
        addToCanvas: function (catId, itemId, canvas, options) {
            if (!canvas) return Promise.resolve(null);

            var parts = catId.split(":");
            var adapterType = parts[0];
            var originalCatId = parts.slice(1).join(":");

            var adapter = adapters[adapterType];
            if (!adapter || !adapter.addToCanvas) return Promise.resolve(null);

            // Extract original item ID (strip prefix if present)
            var originalItemId = itemId;
            var itemParts = itemId.split(":");
            if (itemParts[0] === adapterType) {
                originalItemId = itemParts.slice(1).join(":");
            }

            try {
                var result = adapter.addToCanvas(originalCatId || catId, originalItemId, canvas, options);
                if (result && typeof result.then === "function") return result;
                return Promise.resolve(result || null);
            } catch (err) {
                console.warn("[AssetManager] addToCanvas error for " + catId + "/" + itemId, err);
                return Promise.resolve(null);
            }
        },

        /*
         * Search across all adapters that implement search().
         */
        searchAll: function (query) {
            if (!query || query.trim() === "") {
                return Promise.resolve([]);
            }

            var q = query.trim();
            var typeNames = Object.keys(adapters);
            var searchPromises = [];

            typeNames.forEach(function (type) {
                var adapter = adapters[type];
                if (typeof adapter.search !== "function") return;

                try {
                    var p = adapter.search(q);
                    if (p && typeof p.then === "function") {
                        searchPromises.push(p.then(function (items) {
                            return items || [];
                        }));
                    }
                } catch (err) {
                    console.warn("[AssetManager] Search error for adapter " + type, err);
                }
            });

            if (searchPromises.length === 0) return Promise.resolve([]);

            var allSettled = typeof Promise.allSettled === "function"
                ? Promise.allSettled(searchPromises)
                : Promise.all(searchPromises.map(function (p) {
                    return p.catch(function () { return []; });
                }));

            return allSettled.then(function (results) {
                var all = [];
                (results || []).forEach(function (r) {
                    var items = r.value || r;
                    if (Array.isArray(items)) {
                        all = all.concat(items);
                    }
                });
                return all;
            });
        },

        getAdapter: function (type) {
            return adapters[type] || null;
        },

        getAdapters: function () {
            return Object.keys(adapters).map(function (type) { return adapters[type]; });
        }
    };

    ns.AssetManager = AssetManager;
})(window.ZoomStore);
