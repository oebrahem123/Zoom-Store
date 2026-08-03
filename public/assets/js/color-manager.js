/*
 * ZoomStore.ColorManager
 *
 * Purpose: Centralize SVG color persistence, picker synchronization,
 *   and color utility functions. Decouples color logic from canvas code.
 *
 * Responsibilities:
 *   - Propagate stroke color to child path elements for SVG badge objects
 *   - Synchronize the art color picker UI with the selected object
 *   - Apply and persist color changes on SVG art objects
 *   - Convert RGB(A) to hex for UI display
 *
 * Public API:
 *   fixBadgeChildren(obj, color)  → void  — propagate stroke to group children (Part 1 fix)
 *   syncPicker(obj)               → void  — sync artColor input + hex span (Part 2 fix)
 *   applyColor(obj, color)        → void  — set color, fix children, update _artColor
 *   rgbToHex(color)               → string — normalize to #rrggbb
 *
 * Dependencies: None (pure functions)
 *
 * Extension Points:
 *   - Extend applyColor to handle multi-color SVG objects (isMultiColor flag)
 *   - Add palette / theme support
 *   - Add gradient support for SVG fills
 */

window.ZoomStore = window.ZoomStore || {};

(function (ns) {
    "use strict";

    function isFabricGroup(obj) {
        return obj && obj._objects && Array.isArray(obj._objects);
    }

    function getCapabilities(obj) {
        return obj._capabilities || (obj._assetMeta && obj._assetMeta.capabilities) || { supportsColor: true, supportsRecolor: true, supportsStroke: true, supportsShadow: true };
    }

    var ColorManager = {
        /*
         * Propagate the saved stroke color to every child element
         * of a Fabric group, respecting capabilities.
         */
        fixChildren: function (obj, color) {
            if (!isFabricGroup(obj)) return;
            var caps = getCapabilities(obj);
            if (!caps.supportsRecolor) return;
            var children = obj._objects;
            for (var i = 0; i < children.length; i++) {
                var child = children[i];
                if (child.set) {
                    child.set({
                        stroke: color,
                        fill: (child.fill && child.fill !== "none") ? color : ""
                    });
                }
            }
        },

        /*
         * Synchronize the art color picker with the currently selected object.
         * Updates both the <input type="color"> and the hex <span>.
         */
        syncPicker: function (obj) {
            if (!obj || (!obj._isArt && !obj._assetMeta)) return;
            var caps = getCapabilities(obj);
            var picker = document.getElementById("artColor");
            var hexSpan = document.getElementById("artColorHex");
            if (picker) {
                picker.disabled = !caps.supportsColor;
                picker.value = obj._artColor || "#ffffff";
            }
            if (hexSpan) {
                hexSpan.textContent = caps.supportsColor ? (obj._artColor || "#ffffff") : "—";
            }
        },

        /*
         * Apply a new color to an SVG art object, respecting capabilities.
         */
        applyColor: function (obj, color) {
            if (!obj || (!obj._isArt && !obj._assetMeta)) return;
            var caps = getCapabilities(obj);
            if (!caps.supportsColor) return;
            obj._artColor = color;
            obj.set("stroke", color);
            this.fixChildren(obj, color);
        },

        /*
         * Convert any CSS color value to #rrggbb hex format.
         * Handles: #rgb, #rrggbb, rgb(r,g,b), rgba(r,g,b,a), named colors.
         */
        rgbToHex: function (color) {
            if (!color) return "#ffffff";
            if (color.charAt(0) === "#") {
                if (color.length === 4) {
                    return "#" + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
                }
                return color;
            }
            var match = color.match(/\d+/g);
            if (!match) return "#ffffff";
            return "#" + match.slice(0, 3).map(function (x) {
                var h = parseInt(x, 10).toString(16);
                return h.length === 1 ? "0" + h : h;
            }).join("");
        }
    };

    ns.ColorManager = ColorManager;
})(window.ZoomStore);
