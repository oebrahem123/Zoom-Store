/*
 * ZoomStore.AssetCache
 *
 * Purpose: Generic cache abstraction for any asset type.
 *   Provides a uniform get/set/has/delete/clear interface decoupled
 *   from the internal storage mechanism.
 *
 * Responsibilities:
 *   - Store and retrieve cached data by key
 *   - Track cache size
 *   - Expose stable API that can be backed by Map, LRU, IndexedDB,
 *     ServiceWorker Cache, or remote cache without consumer changes
 *
 * Public API:
 *   get(key)        → value | undefined
 *   set(key, value) → void
 *   has(key)        → boolean
 *   delete(key)     → boolean
 *   clear()         → void
 *   keys()          → string[]
 *   size            → number (read-only)
 *
 * Dependencies: None
 *
 * Extension Points:
 *   Replace the factory body to swap the internal store (e.g. Map → LRU → IndexedDB).
 *   The returned interface never changes.
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    function AssetCache() {
        var store = {};
        var _size = 0;

        return {
            get: function (key) {
                return store[key];
            },

            set: function (key, value) {
                if (!(key in store)) {
                    _size++;
                }
                store[key] = value;
            },

            has: function (key) {
                return key in store;
            },

            delete: function (key) {
                if (key in store) {
                    delete store[key];
                    _size--;
                    return true;
                }
                return false;
            },

            clear: function () {
                store = {};
                _size = 0;
            },

            keys: function () {
                return Object.keys(store);
            },

            get size() {
                return _size;
            }
        };
    }

    ns.Cache = ns.Cache || {};
    ns.Cache.AssetCache = AssetCache;
})(window.ZoomStore);
