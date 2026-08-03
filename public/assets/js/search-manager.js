/*
 * ZoomStore.SearchManager
 *
 * Purpose: Unified search engine that aggregates results from multiple sources.
 *   The search engine never knows or cares where assets come from.
 *   Any provider can register: Legacy, File Library, Cloud, AI, Premium, etc.
 *
 * Responsibilities:
 *   - Accept dynamically registered search sources
 *   - Run parallel searches across all registered sources
 *   - Normalize results into a standard format
 *   - Aggregate errors gracefully (one failing source never blocks others)
 *   - Deduplicate results by id across sources
 *
 * Public API:
 *   registerSource(name, searchFn)         → void  — id + function(query) => Promise<Result[]>
 *   unregisterSource(name)                 → void
 *   searchAll(query)                       → Promise<{ query, totalResults, results[], errors[] }>
 *   clearSources()                         → void
 *   getSources()                           → string[]
 *
 * Result format (normalized):
 *   { id, source, adapter, category, title, titleAr, thumbnail, score, metadata }
 *
 * Dependencies: None
 *
 * Extension Points:
 *   - Add ranking / scoring strategies
 *   - Add full-text search (fuse.js, lunr.js)
 *   - Add debounced / throttled search
 *   - Add result caching
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var sources = {};

    var SearchManager = {
        /*
         * Register a search source.
         * name   — unique identifier for the source (e.g. 'svg', 'fonts', 'ai')
         * searchFn — function(query: string) => Promise<Result[]>
         *   Each result must be an object with at minimum: { id, title }
         *   Recommended: { id, source, adapter, category, title, titleAr, thumbnail, score, metadata }
         */
        registerSource: function (name, searchFn) {
            if (!name || typeof searchFn !== "function") {
                console.warn("[SearchManager] Invalid source registration:", name);
                return;
            }
            sources[name] = searchFn;
        },

        unregisterSource: function (name) {
            delete sources[name];
        },

        /*
         * Search all registered sources in parallel.
         * Returns a normalized response with results and any errors.
         * One source failing never blocks other results.
         */
        searchAll: function (query) {
            if (!query || query.trim() === "") {
                return Promise.resolve({ query: "", totalResults: 0, results: [], errors: [] });
            }

            var q = query.trim();
            var sourceNames = Object.keys(sources);
            if (sourceNames.length === 0) {
                return Promise.resolve({ query: q, totalResults: 0, results: [], errors: [] });
            }

            var promises = sourceNames.map(function (name) {
                try {
                    var result = sources[name](q);
                    return Promise.resolve(result).then(function (items) {
                        return { source: name, items: items || [], error: null };
                    });
                } catch (err) {
                    return Promise.resolve({ source: name, items: [], error: err });
                }
            });

            var allSettled = typeof Promise.allSettled === "function"
                ? Promise.allSettled(promises)
                : Promise.all(promises.map(function (p) {
                    return p.catch(function (err) { return { source: "unknown", items: [], error: err }; });
                }));

            return allSettled.then(function (results) {
                var allResults = [];
                var errors = [];

                (results || []).forEach(function (r) {
                    var data = r.value || r;
                    if (data.error) {
                        errors.push({ source: data.source, error: data.error });
                        return;
                    }
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(function (item) {
                            allResults.push({
                                id: item.id || "",
                                source: data.source,
                                adapter: item.adapter || data.source,
                                category: item.category || "",
                                title: item.title || "",
                                titleAr: item.titleAr || "",
                                thumbnail: item.thumbnail || "",
                                score: item.score || 0,
                                metadata: item.metadata || {}
                            });
                        });
                    }
                });

                // Deduplicate by id
                var seen = {};
                var deduped = [];
                for (var i = 0; i < allResults.length; i++) {
                    var key = allResults[i].source + ":" + allResults[i].id;
                    if (!seen[key]) {
                        seen[key] = true;
                        deduped.push(allResults[i]);
                    }
                }

                deduped.sort(function (a, b) { return (b.score || 0) - (a.score || 0); });

                return {
                    query: q,
                    totalResults: deduped.length,
                    results: deduped.slice(0, 50),
                    errors: errors
                };
            });
        },

        clearSources: function () {
            sources = {};
        },

        getSources: function () {
            return Object.keys(sources);
        }
    };

    ns.SearchManager = SearchManager;
})(window.ZoomStore);
