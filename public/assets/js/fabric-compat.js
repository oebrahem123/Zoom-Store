/*
 * ZoomStore.Compatibility
 *
 * Purpose: Central module for all third-party compatibility patches.
 *   Application logic never lives here — only workarounds for bugs in
 *   Fabric.js, browser inconsistencies, Canvas2D quirks, WebFont issues,
 *   or any other dependency outside this project's control.
 *
 *   Every patch is independently removable. When upgrading a dependency,
 *   check each patch's removal criteria. If all criteria are met, delete
 *   the patch block.
 *
 * API:
 *   ZoomStore.Compatibility.install()             — Apply all patches (safe to call multiple times)
 *   ZoomStore.Compatibility.installFabricPatch()  — Fabric.js workarounds only
 *   ZoomStore.Compatibility.installCanvasPatch()  — Canvas2D API workarounds only
 *   ZoomStore.Compatibility.installBrowserPatch() — Browser-specific workarounds only
 *   ZoomStore.Compatibility.isInstalled()         — Returns true if patches have been applied
 *   ZoomStore.Compatibility.version()             — Returns the module version
 *
 * Guidelines for adding patches:
 *   1. Write a private patch function below (e.g., patchFooBar).
 *   2. Wire it into the appropriate install* method (create a new one if needed).
 *   3. Document: affected versions, root cause, patched methods, removal criteria.
 *   4. Verify version constraints before patching.
 *   5. Wrap each patch call in try-catch so one failure never blocks others.
 *   6. No editor or business logic.
 */

(function () {
    "use strict";

    var MODULE_VERSION = "1.0.0";

    // ── Dev-mode logger (no-op in production) ──
    var DEBUG = typeof window !== "undefined" &&
        window.location &&
        (window.location.hostname === "localhost" ||
         window.location.hostname === "127.0.0.1" ||
         window.location.hostname.indexOf(".test") !== -1);

    function log(msg) {
        if (!DEBUG) return;
        if (typeof console !== "undefined" && console.log) {
            console.log("[Compat] " + msg);
        }
    }

    // ── Guards ──
    function hasFabric() {
        return typeof fabric !== "undefined" && fabric.version;
    }

    // ================================================================
    // Patch: "alphabetical" textBaseline
    // ================================================================
    // Affected Fabric versions: 5.3.0 (and possibly 5.3.1 CDN label)
    //   The CDN build at https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js
    //   reports fabric.version = "5.3.0".
    //
    // Root cause:
    //   fabric.Text.prototype._setTextStyles (offset 255842 in the minified file)
    //   hardcodes the literal string "alphabetical" directly to the canvas 2D context:
    //
    //     _setTextStyles:function(t,e,i){
    //       if(t.textBaseline="alphabetical",this.path)...
    //     }
    //
    //   "alphabetical" is not a valid CanvasTextBaseline enum value. The correct
    //   Canvas2D value is "alphabetic". Chrome throws:
    //     "The provided value 'alphabetical' is not a valid enum value of type
    //      CanvasTextBaseline."
    //
    //   Setting fabric.Text.prototype.textBaseline alone does NOT fix this because
    //   _setTextStyles never reads this.textBaseline — it always assigns the
    //   hardcoded string directly to ctx.textBaseline.
    //
    // Patched methods:
    //   fabric.Text.prototype._setTextStyles — replaced entirely
    //   fabric.Text.prototype.textBaseline  — set to "alphabetic"
    //   fabric.IText.prototype.textBaseline — set to "alphabetic"
    //   fabric.Textbox.prototype.textBaseline — set to "alphabetic"
    //
    // Upstream issue:
    //   None reported. The bug exists only in the minified CDN distribution;
    //   the source repo (github.com/fabricjs/fabric.js) does not contain
    //   the string "alphabetical".
    //
    // Removal criteria:
    //   - Upgrade to Fabric.js >= 5.3.2 or 6.x.
    //   - Verify: grep the loaded CDN file for "alphabetical" — zero matches.
    //   - Verify: fabric.version >= "5.3.2" at runtime.
    // ================================================================

    function patchTextBaseline() {
        if (!hasFabric()) return;
        if (fabric.version !== "5.3.0") return;

        var Text = fabric.Text;
        if (!Text) return;

        Text.prototype.textBaseline = "alphabetic";

        Text.prototype._setTextStyles = function (ctx, charStyle, i) {
            ctx.textBaseline = "alphabetic";
            if (this.path) {
                switch (this.pathAlign) {
                    case "center":
                        ctx.textBaseline = "middle";
                        break;
                    case "ascender":
                        ctx.textBaseline = "top";
                        break;
                    case "descender":
                        ctx.textBaseline = "bottom";
                        break;
                }
            }
            ctx.font = this._getFontDeclaration(charStyle, i);
        };

        if (fabric.IText) {
            fabric.IText.prototype.textBaseline = "alphabetic";
        }
        if (fabric.Textbox) {
            fabric.Textbox.prototype.textBaseline = "alphabetic";
        }

        log("Fabric compatibility patch applied.");
    }

    // ================================================================
    // Patch: Textbox width recalculation on font change
    // ================================================================
    // Affected Fabric versions: 5.3.0 (and 5.3.1 CDN label)
    //
    // Root cause:
    //   fabric.Textbox.prototype.initDimensions completely overrides
    //   fabric.Text.prototype.initDimensions. The Textbox version never
    //   calls _measureText(), so the internal _textWidth property is
    //   never set. Since calcTextWidth() reads _textWidth, it returns
    //   undefined (or stale fallback font values) after a font-family
    //   change. Furthermore, the lazy _measureCtx (created by
    //   _getGraphemeBox) does not reliably pick up web fonts loaded
    //   after the canvas was created, causing __charBounds to be
    //   populated with incorrect fallback widths.
    //
    //   The only reliable measurement source is a fresh canvas 2D
    //   context created after the web font has fully loaded.
    //
    // Patched methods:
    //   fabric.Textbox.prototype.initDimensions — wrapped to measure
    //   _textWidth using a fresh canvas context.
    //
    // Removal criteria:
    //   - Upgrade to Fabric.js >= 6.0.
    //   - Verify: fabric.Textbox.prototype.initDimensions calls
    //     Text.prototype._measureText() internally.
    //   - Verify: calcTextWidth() returns correct values after
    //     fontFamily changes.
    // ================================================================

    function patchTextboxWidth() {
        if (!hasFabric()) return;
        if (typeof fabric.Textbox === "undefined") return;

        var origInitDimensions = fabric.Textbox.prototype.initDimensions;

        fabric.Textbox.prototype.initDimensions = function () {
            origInitDimensions.call(this);

            // _textWidth is never set by Textbox.initDimensions.
            // Measuring via a reused _measureCtx gives stale fallback
            // widths when web fonts load after the context is created.
            // Always use a fresh canvas context for reliable results.
            if (this.text && this.text.length > 0 && this.fontSize > 0) {
                var fresh = fabric.util.createCanvasElement().getContext("2d");
                fresh.font = this._getFontDeclaration(this);
                this._textWidth = fresh.measureText(this.text).width;
            } else {
                this._textWidth = 0;
            }
        };

        // Fabric.js createAccessors may define a calcTextWidth getter that
        // returns something other than _textWidth. Override to ensure
        // it reads our patched _textWidth for charSpacing===0.
        var origCalcTextWidth = fabric.Text.prototype.calcTextWidth;
        if (typeof origCalcTextWidth === "function") {
            fabric.Text.prototype.calcTextWidth = function () {
                if (this.charSpacing) {
                    return this._getWidthPerChar();
                }
                return this._textWidth;
            };
        }

        log("Textbox width measurement patch applied.");
    }

    // ================================================================
    // Public API
    // ================================================================

    var Compatibility = {
        __installed: false,

        install: function () {
            if (this.__installed) return;
            if (typeof fabric === "undefined") {
                setTimeout(this.install.bind(this), 0);
                return;
            }
            this.installFabricPatch();
            this.installCanvasPatch();
            this.installBrowserPatch();
            this.__installed = true;
        },

        installFabricPatch: function () {
            try {
                patchTextBaseline();
            } catch (err) {
                if (DEBUG && typeof console !== "undefined") {
                    console.warn("[Compat] Fabric patch failed:", err);
                }
            }
            try {
                patchTextboxWidth();
            } catch (err) {
                if (DEBUG && typeof console !== "undefined") {
                    console.warn("[Compat] Textbox width patch failed:", err);
                }
            }
        },

        installCanvasPatch: function () {
            // Placeholder for future Canvas2D API workarounds.
        },

        installBrowserPatch: function () {
            // Placeholder for future browser-specific fixes (Safari, Firefox, etc.).
        },

        isInstalled: function () {
            return this.__installed;
        },

        version: function () {
            return MODULE_VERSION;
        }
    };

    window.ZoomStore = window.ZoomStore || {};
    window.ZoomStore.Compatibility = Compatibility;

    // ── Auto-install ──
    Compatibility.install();
})();
