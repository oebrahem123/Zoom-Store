/*
 * ZoomStore.FontCatalog
 *
 * Purpose: Versioned configuration data for the font system.
 *   Adding or updating fonts requires editing this file only —
 *   no application code changes needed.
 *
 * Responsibilities:
 *   - Define font categories with localized names
 *   - Define individual font entries with provider metadata
 *   - Define provider loader implementations
 *
 * Public API (config object exposed on ZoomStore):
 *   FontCatalog.version           — catalog schema version
 *   FontCatalog.updatedAt         — last update timestamp
 *   FontCatalog.providerVersion   — provider API version
 *   FontCatalog.categories[]      — category definitions
 *   FontCatalog.fonts[]           — individual font entries
 *   FontCatalog.providers{}       — loader functions keyed by provider name
 *
 * Dependencies: None (pure data + loader functions)
 *
 * Extension Points:
 *   - Add new categories to the categories array
 *   - Add new fonts to the fonts array with existing providers
 *   - Add new provider to providers map (e.g. 'local', 'self-hosted', 'premium')
 *     without touching FontManager
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    var catalog = {
        version: 1,
        updatedAt: "2026-07-06",
        providerVersion: 1,

        categories: [
            { id: "sans-serif", name: "Sans Serif", nameAr: "سانس سيريف", displayOrder: 1 },
            { id: "serif", name: "Serif", nameAr: "سيريف", displayOrder: 2 },
            { id: "display", name: "Display", nameAr: "عرضي", displayOrder: 3 },
            { id: "handwriting", name: "Handwriting", nameAr: "خط يدوي", displayOrder: 4 },
            { id: "monospace", name: "Monospace", nameAr: "أحادي المسافة", displayOrder: 5 },
            { id: "condensed", name: "Condensed", nameAr: "مضغوط", displayOrder: 6 },
            { id: "slab-serif", name: "Slab Serif", nameAr: "سيريف سميك", displayOrder: 7 },
            { id: "grotesk", name: "Grotesk", nameAr: "غروتيسك", displayOrder: 8 },
            { id: "humanist", name: "Humanist", nameAr: "إنساني", displayOrder: 9 },
            { id: "geometric", name: "Geometric", nameAr: "هندسي", displayOrder: 10 },
            { id: "rounded", name: "Rounded", nameAr: "مدور", displayOrder: 11 },
            { id: "script", name: "Script", nameAr: "سكريبت", displayOrder: 12 },
            { id: "decorative", name: "Decorative", nameAr: "زخرفي", displayOrder: 13 },
            { id: "grunge", name: "Grunge", nameAr: "جرانج", displayOrder: 14 },
            { id: "comic", name: "Comic", nameAr: "كوميدي", displayOrder: 15 }
        ],

        fonts: [
            // ── Sans Serif ──
            { id: "open-sans", family: "Open Sans", category: "sans-serif", provider: "local", variants: ["300", "400", "600", "700", "800"] },
            { id: "roboto", family: "Roboto", category: "sans-serif", provider: "local", variants: ["100", "300", "400", "500", "700", "900"] },
            { id: "lato", family: "Lato", category: "sans-serif", provider: "local", variants: ["100", "300", "400", "700", "900"] },
            { id: "montserrat", family: "Montserrat", category: "sans-serif", provider: "local", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "source-sans-3", family: "Source Sans 3", category: "sans-serif", provider: "google", variants: ["200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "nunito", family: "Nunito", category: "sans-serif", provider: "google", variants: ["200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "cairo", family: "Cairo", category: "sans-serif", provider: "local", variants: ["200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "tajawal", family: "Tajawal", category: "sans-serif", provider: "local", variants: ["200", "300", "400", "500", "700", "800", "900"] },

            // ── Serif ──
            { id: "playfair-display", family: "Playfair Display", category: "serif", provider: "local", variants: ["400", "500", "600", "700", "800", "900"] },
            { id: "merriweather", family: "Merriweather", category: "serif", provider: "google", variants: ["300", "400", "700", "900"] },
            { id: "lora", family: "Lora", category: "serif", provider: "google", variants: ["400", "500", "600", "700"] },
            { id: "pt-serif", family: "PT Serif", category: "serif", provider: "google", variants: ["400", "700"] },
            { id: "crimson-text", family: "Crimson Text", category: "serif", provider: "google", variants: ["400", "600", "700"] },

            // ── Display ──
            { id: "josefin-sans", family: "Josefin Sans", category: "display", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700"] },
            { id: "lobster", family: "Lobster", category: "display", provider: "google", variants: ["400"] },
            { id: "bebas-neue", family: "Bebas Neue", category: "display", provider: "google", variants: ["400"] },
            { id: "anton", family: "Anton", category: "display", provider: "google", variants: ["400"] },
            { id: "impact", family: "Impact", category: "display", provider: "", variants: ["400"] },

            // ── Handwriting ──
            { id: "pacifico", family: "Pacifico", category: "handwriting", provider: "google", variants: ["400"] },
            { id: "dancing-script", family: "Dancing Script", category: "handwriting", provider: "google", variants: ["400", "500", "600", "700"] },
            { id: "caveat", family: "Caveat", category: "handwriting", provider: "google", variants: ["400", "500", "600", "700"] },
            { id: "indie-flower", family: "Indie Flower", category: "handwriting", provider: "google", variants: ["400"] },

            // ── Monospace ──
            { id: "fira-code", family: "Fira Code", category: "monospace", provider: "google", variants: ["300", "400", "500", "600", "700"] },
            { id: "jetbrains-mono", family: "JetBrains Mono", category: "monospace", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800"] },
            { id: "source-code-pro", family: "Source Code Pro", category: "monospace", provider: "google", variants: ["200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "space-mono", family: "Space Mono", category: "monospace", provider: "google", variants: ["400", "700"] },

            // ── Condensed ──
            { id: "barlow-condensed", family: "Barlow Condensed", category: "condensed", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "roboto-condensed", family: "Roboto Condensed", category: "condensed", provider: "google", variants: ["300", "400", "500", "600", "700"] },
            { id: "fira-sans-condensed", family: "Fira Sans Condensed", category: "condensed", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "oswald", family: "Oswald", category: "condensed", provider: "google", variants: ["200", "300", "400", "500", "600", "700"] },

            // ── Slab Serif ──
            { id: "roboto-slab", family: "Roboto Slab", category: "slab-serif", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "arvo", family: "Arvo", category: "slab-serif", provider: "google", variants: ["400", "700"] },
            { id: "zilla-slab", family: "Zilla Slab", category: "slab-serif", provider: "google", variants: ["300", "400", "500", "600", "700"] },

            // ── Grotesk ──
            { id: "inter", family: "Inter", category: "grotesk", provider: "local", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "public-sans", family: "Public Sans", category: "grotesk", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "dm-sans", family: "DM Sans", category: "grotesk", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "outfit", family: "Outfit", category: "grotesk", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },

            // ── Humanist ──
            { id: "noto-sans", family: "Noto Sans", category: "humanist", provider: "local", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "fira-sans", family: "Fira Sans", category: "humanist", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "ibm-plex-sans", family: "IBM Plex Sans", category: "humanist", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700"] },

            // ── Geometric ──
            { id: "poppins", family: "Poppins", category: "geometric", provider: "local", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },
            { id: "quicksand", family: "Quicksand", category: "geometric", provider: "google", variants: ["300", "400", "500", "600", "700"] },
            { id: "jost", family: "Jost", category: "geometric", provider: "google", variants: ["100", "200", "300", "400", "500", "600", "700", "800", "900"] },

            // ── Rounded ──
            { id: "varela-round", family: "Varela Round", category: "rounded", provider: "google", variants: ["400"] },
            { id: "m-plus-rounded-1c", family: "M PLUS Rounded 1c", category: "rounded", provider: "google", variants: ["100", "300", "400", "500", "700", "800", "900"] },
            { id: "baloo-2", family: "Baloo 2", category: "rounded", provider: "google", variants: ["400", "500", "600", "700", "800"] },

            // ── Script ──
            { id: "great-vibes", family: "Great Vibes", category: "script", provider: "google", variants: ["400"] },
            { id: "alex-brush", family: "Alex Brush", category: "script", provider: "google", variants: ["400"] },
            { id: "parisienne", family: "Parisienne", category: "script", provider: "google", variants: ["400"] },
            { id: "allura", family: "Allura", category: "script", provider: "google", variants: ["400"] },

            // ── Decorative ──
            { id: "creepster", family: "Creepster", category: "decorative", provider: "google", variants: ["400"] },
            { id: "rubik-glitch", family: "Rubik Glitch", category: "decorative", provider: "google", variants: ["400"] },
            { id: "monoton", family: "Monoton", category: "decorative", provider: "google", variants: ["400"] },

            // ── Grunge ──
            { id: "permanent-marker", family: "Permanent Marker", category: "grunge", provider: "google", variants: ["400"] },
            { id: "rock-salt", family: "Rock Salt", category: "grunge", provider: "google", variants: ["400"] },
            { id: "caveat-brush", family: "Caveat Brush", category: "grunge", provider: "google", variants: ["400"] },

            // ── Comic ──
            { id: "fredoka-one", family: "Fredoka One", category: "comic", provider: "google", variants: ["400"] },
            { id: "bangers", family: "Bangers", category: "comic", provider: "google", variants: ["400"] },
            { id: "comic-neue", family: "Comic Neue", category: "comic", provider: "google", variants: ["300", "400", "700"] }
        ],

        providers: {
            local: {
                name: "Local Fonts",
                load: function (families) {
                    return new Promise(function (resolve) {
                        if (typeof fetch === "undefined" || typeof FontFace === "undefined") {
                            if (catalog.providers.google) { catalog.providers.google.load(families).then(resolve).catch(resolve); return; }
                            resolve(); return;
                        }

                        function localAssetUrl(path) {
                            var base = ns.baseUrl || '';
                            if (base.endsWith('/') && path.startsWith('/')) return base + path.substring(1);
                            return base + path;
                        }

                        fetch(localAssetUrl("/assets/fonts/manifest.json"))
                            .then(function (r) { if (!r.ok) throw new Error("HTTP " + r.status); return r.json(); })
                            .then(function (manifest) {
                                var fontPromises = [];
                                families.forEach(function (str) {
                                    var parts = str.split(":");
                                    var familyName = parts[0];
                                    var requestedVariants = parts[1] ? parts[1].split(",") : ["400"];

                                    var fontId = null;
                                    var fontData = null;
                                    for (var key in manifest.fonts) {
                                        if (manifest.fonts[key].family === familyName) {
                                            fontId = key;
                                            fontData = manifest.fonts[key];
                                            break;
                                        }
                                    }

                                    if (!fontData) {
                                        if (catalog.providers.google) { fontPromises.push(catalog.providers.google.load([str])); }
                                        return;
                                    }

                                    requestedVariants.forEach(function (variantKey) {
                                        var vk = fontData.variants[variantKey];
                                        if (!vk) {
                                            var weightNum = parseInt(variantKey, 10);
                                            for (var v in fontData.variants) {
                                                if (fontData.variants[v].weight === weightNum) { vk = fontData.variants[v]; break; }
                                            }
                                        }
                                        if (!vk || !vk.files) return;

                                        vk.files.forEach(function (fileEntry) {
                                            var fontUrl = localAssetUrl("/assets/fonts/" + fontId + "/" + fileEntry.file);
                                            var ff = new FontFace(familyName, "url(" + fontUrl + ")", {
                                                weight: (vk.weight || 400).toString(),
                                                style: vk.style || "normal",
                                                unicodeRange: fileEntry.range || undefined,
                                                display: "swap"
                                            });
                                            fontPromises.push(ff.load().then(function (f) { document.fonts.add(f); }).catch(function () {
                                                if (catalog.providers.google) { return catalog.providers.google.load([familyName + ":" + (vk.weight || 400)]); }
                                            }));
                                        });
                                    });
                                });
                                return Promise.all(fontPromises);
                            })
                            .catch(function () {
                                if (catalog.providers.google) { return catalog.providers.google.load(families); }
                            })
                            .then(function () { resolve(); })
                            .catch(function () { resolve(); });
                    });
                }
            },
            google: {
                name: "Google Fonts",
                load: function (families) {
                    return new Promise(function (resolve) {
                        var fontTimeout = setTimeout(function () {
                            console.warn("[FontCatalog] WebFont loading timed out for:", JSON.stringify(families));
                            resolve();
                        }, 10000);

                        function done() {
                            clearTimeout(fontTimeout);
                            resolve();
                        }

                        function doLoad() {
                            if (typeof WebFont === "undefined") {
                                done();
                                return;
                            }
                            WebFont.load({
                                google: { families: families },
                                active: done,
                                inactive: done
                            });
                        }

                        if (typeof WebFont === "undefined") {
                            var script = document.createElement("script");
                            script.src = "https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js";
                            script.onload = doLoad;
                            script.onerror = function () {
                                console.warn("[FontCatalog] Failed to load WebFont Loader");
                                done();
                            };
                            document.head.appendChild(script);
                        } else {
                            doLoad();
                        }
                    });
                }
            }
        }
    };

    ns.FontCatalog = catalog;
})(window.ZoomStore);
