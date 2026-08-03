/*
 * ZoomStore.PreviewManager
 *
 * Purpose: Generate and cache thumbnails for asset previews.
 *   Decouples thumbnail generation from the rendering pipeline.
 *
 * Responsibilities:
 *   - Convert SVG text to inline SVG thumbnails or data/blob URLs
 *   - Lazy-load thumbnails via IntersectionObserver
 *   - Cache generated thumbnails to avoid re-rendering
 *   - Provide fallback for failed thumbnails
 *
 * Public API:
 *   getThumbnail(svgText, options)     → Promise<string> — inline SVG or data URL
 *   preloadCategory(containerEl, items) → void — lazy-load thumbnails in viewport
 *   setDefaultThumbnail(url)            → void
 *   clearCache()                        → void
 *   getCacheSize()                      → number
 *
 * Dependencies: None
 *
 * Extension Points:
 *   - Add raster thumbnail generation using offscreen canvas
 *   - Add webp/avif thumbnail format support
 *   - Add thumbnail sizing options (width, height, crop)
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var thumbnailCache = {};
    var defaultThumbnail = "";
    var observer = null;

    function renderItemDiv(item) {
        var div = document.createElement('div');
        div.className = 'art-item-card';
        if (item.svgContent) {
            div.innerHTML = item.svgContent;
        } else {
            div.innerHTML = '<div class="org" style="background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:32px;color:#999;">🖼</div>';
        }
        div.setAttribute('data-thumb-id', item.id || '');
        return div;
    }

    var PreviewManager = {
        /*
         * Generate a thumbnail from SVG text.
         * Options: { width, height, format }
         * Currently returns an inline SVG data URL.
         */
        getThumbnail: function (svgText, options) {
            options = options || {};

            if (!svgText || svgText.trim() === "") {
                return Promise.resolve(defaultThumbnail || "");
            }

            var cacheKey = "thumb_" + svgText.substring(0, 80) + "_" + (options.width || 120) + "x" + (options.height || 120);
            if (thumbnailCache[cacheKey]) {
                return Promise.resolve(thumbnailCache[cacheKey]);
            }

            var thumb = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(
                svgText
                    .replace(/width="[^"]*"/, "")
                    .replace(/height="[^"]*"/, "")
                    .replace(/<svg/, '<svg width="' + (options.width || 120) + '" height="' + (options.height || 120) + '"')
            );

            thumbnailCache[cacheKey] = thumb;
            return Promise.resolve(thumb);
        },

        /*
         * Single rendering authority for asset item grids.
         * Clears the container, renders all items, and sets up lazy thumbnail loading.
         *
         * Each item: { id, title, source, svgContent, filename, capabilities, ... }
         * catId: the original category id (used for cache lookup)
         * onClick: function(item, div) called when an item is clicked
         */
        render: function (containerEl, items, catId, onClick) {
            if (!containerEl) return;
            containerEl.innerHTML = '';

            if (!items || items.length === 0) {
                containerEl.innerHTML = '<p class="text-muted text-center p-4">لا توجد رسومات متاحة</p>';
                return;
            }

            var self = this;

            items.forEach(function (item) {
                var div = renderItemDiv(item);
                div.onclick = function () { if (onClick) onClick(item, div); };
                containerEl.appendChild(div);

                // If file-based item with no SVG content, try to load from cache
                if (item.source !== 'legacy' && !item.svgContent && item.filename && window.ZoomStore && ZoomStore.SVGLoader) {
                    ZoomStore.SVGLoader.getCachedSvgText(catId, item.filename).then(function (svgText) {
                        if (svgText) {
                            item.svgContent = svgText;
                            div.innerHTML = svgText;
                            // Generate thumbnail now that content is loaded
                            self.getThumbnail(svgText, { width: 120, height: 120 }).then(function (url) {
                                if (url) {
                                    div.style.backgroundImage = "url(" + url + ")";
                                    div.classList.add("thumb-loaded");
                                }
                            });
                        } else {
                            // Fallback if SVG failed to load
                            div.innerHTML = '<div class="org" style="background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#ccc;">⚠</div>';
                        }
                    });
                }
            });

            // Set up lazy thumbnail loading
            self.preloadCategory(containerEl, items);
        },

        /*
         * Lazy-load thumbnails in a container using IntersectionObserver.
         * Each item in the items array should have { id, svgContent, thumbnail }.
         * The container should contain child elements with data-thumb-id attributes.
         */
        preloadCategory: function (containerEl, items) {
            if (!containerEl || !items || items.length === 0) return;

            var self = this;

            function loadVisible(itemsToLoad) {
                itemsToLoad.forEach(function (item) {
                    var el = containerEl.querySelector('[data-thumb-id="' + (item.id || "") + '"]');
                    if (!el) return;

                    var thumbUrl = item.thumbnail || "";
                    if (thumbUrl) {
                        el.style.backgroundImage = "url(" + thumbUrl + ")";
                        el.classList.add("thumb-loaded");
                        return;
                    }

                    if (item.svgContent) {
                        self.getThumbnail(item.svgContent, { width: 120, height: 120 }).then(function (url) {
                            if (url) {
                                el.style.backgroundImage = "url(" + url + ")";
                                el.classList.add("thumb-loaded");
                            }
                        });
                    }
                });
            }

            if (typeof IntersectionObserver === "undefined") {
                loadVisible(items);
                return;
            }

            if (observer) {
                observer.disconnect();
            }

            observer = new IntersectionObserver(function (entries) {
                var toLoad = [];
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var id = entry.target.getAttribute("data-thumb-id");
                        if (id) {
                            var item = null;
                            for (var i = 0; i < items.length; i++) {
                                if (items[i].id === id) { item = items[i]; break; }
                            }
                            if (item) toLoad.push(item);
                        }
                        observer.unobserve(entry.target);
                    }
                });
                if (toLoad.length > 0) loadVisible(toLoad);
            }, { rootMargin: "100px" });

            var els = containerEl.querySelectorAll("[data-thumb-id]");
            for (var i = 0; i < els.length; i++) {
                observer.observe(els[i]);
            }
        },

        setDefaultThumbnail: function (url) {
            defaultThumbnail = url;
        },

        clearCache: function () {
            thumbnailCache = {};
        },

        getCacheSize: function () {
            return Object.keys(thumbnailCache).length;
        }
    };

    ns.PreviewManager = PreviewManager;
})(window.ZoomStore);
