window.ZoomStore = window.ZoomStore || {};

(function (ns) {
  'use strict';

  var instance = null;
  var overlay = null;
  var _visible = false;
  var _animFrameId = null;
  var _currentProgress = 0;

  var TEMPLATE =
    '<div class="zoom-loading-overlay" id="zoom-loading-overlay" role="alert" aria-live="polite">' +
      '<div class="zoom-loading-card">' +
        '<button class="zoom-loading-close" id="zoom-loading-close" aria-label="إغلاق">&times;</button>' +
        '<div class="zoom-loading-title" id="zoom-loading-title">Zoom Store</div>' +
        '<div class="zoom-loading-subtitle" id="zoom-loading-subtitle"></div>' +
        '<div class="zoom-loading-image-wrap" id="zoom-loading-image-wrap">' +
          '<img class="zoom-loading-image" id="zoom-loading-image" src="" alt="">' +
        '</div>' +
        '<div class="zoom-loading-variant" id="zoom-loading-variant"></div>' +
        '<div class="zoom-loading-spinner" id="zoom-loading-spinner"></div>' +
        '<div class="zoom-loading-message" id="zoom-loading-message">جاري التحميل...</div>' +
        '<div class="zoom-loading-progress-wrap">' +
          '<div class="zoom-loading-progress-bar" id="zoom-loading-progress-bar"></div>' +
        '</div>' +
        '<div class="zoom-loading-progress-text" id="zoom-loading-progress-text">0%</div>' +
      '</div>' +
    '</div>';

  function getEl(id) {
    return document.getElementById(id);
  }

  function ensureOverlay() {
    if (overlay) return overlay;
    var existing = document.getElementById('zoom-loading-overlay');
    if (existing) {
      overlay = existing;
      return overlay;
    }
    var div = document.createElement('div');
    div.innerHTML = TEMPLATE;
    overlay = div.firstElementChild;
    document.body.appendChild(overlay);

    getEl('zoom-loading-close').addEventListener('click', function () {
      ns.ZoomLoading.hide();
    });

    return overlay;
  }

  function setElementText(id, text) {
    var el = getEl(id);
    if (el) el.textContent = text != null ? text : '';
  }

  function updateProgressUI(value) {
    var bar = getEl('zoom-loading-progress-bar');
    var text = getEl('zoom-loading-progress-text');
    if (bar) bar.style.width = Math.min(100, Math.max(0, value)) + '%';
    if (text) text.textContent = Math.round(Math.min(100, Math.max(0, value))) + '%';
  }

  function stopAnimation() {
    if (_animFrameId) {
      cancelAnimationFrame(_animFrameId);
      _animFrameId = null;
    }
  }

  function animateProgress(from, to, duration, callback) {
    stopAnimation();
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var elapsed = timestamp - startTime;
      var progress = Math.min(elapsed / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = from + (to - from) * eased;
      updateProgressUI(current);
      _currentProgress = current;
      if (progress < 1) {
        _animFrameId = requestAnimationFrame(step);
      } else {
        _animFrameId = null;
        if (callback) callback();
      }
    }

    _animFrameId = requestAnimationFrame(step);
  }

  var ZoomLoading = {
    show: function (options) {
      options = options || {};
      ensureOverlay();

      var title = getEl('zoom-loading-title');
      if (title) title.textContent = options.title || 'Zoom Store';

      var subtitle = getEl('zoom-loading-subtitle');
      if (subtitle) {
        subtitle.textContent = options.subtitle || options.productName || '';
        subtitle.style.display = (options.subtitle || options.productName) ? '' : 'none';
      }

      var imgWrap = getEl('zoom-loading-image-wrap');
      var img = getEl('zoom-loading-image');
      if (options.image) {
        img.src = options.image;
        img.alt = options.subtitle || '';
        imgWrap.style.display = '';
      } else {
        imgWrap.style.display = 'none';
      }

      var variant = getEl('zoom-loading-variant');
      if (variant) {
        variant.textContent = options.variant || '';
        variant.style.display = options.variant ? '' : 'none';
      }

      var spinner = getEl('zoom-loading-spinner');
      if (spinner) spinner.style.display = '';

      var msg = getEl('zoom-loading-message');
      if (msg) msg.textContent = options.message || 'جاري التحميل...';

      var initialProgress = options.progress != null ? options.progress : -1;
      if (initialProgress >= 0) {
        updateProgressUI(initialProgress);
        _currentProgress = initialProgress;
      } else {
        updateProgressUI(0);
        _currentProgress = 0;
        animateProgress(0, 85, 15000);
      }

      var closeBtn = getEl('zoom-loading-close');
      if (closeBtn) closeBtn.style.display = options.allowClose ? '' : 'none';

      overlay.classList.add('active');
      _visible = true;
    },

    setProgress: function (value) {
      if (!_visible) return;
      stopAnimation();
      var clamped = Math.min(100, Math.max(0, value));
      var from = _currentProgress;
      if (value >= from) {
        animateProgress(from, clamped, 500);
      } else {
        updateProgressUI(clamped);
        _currentProgress = clamped;
      }
    },

    setMessage: function (text) {
      if (!_visible) return;
      var msg = getEl('zoom-loading-message');
      if (msg) msg.textContent = text || '';
    },

    setSubtitle: function (text) {
      var el = getEl('zoom-loading-subtitle');
      if (el) {
        el.textContent = text || '';
        el.style.display = text ? '' : 'none';
      }
    },

    setImage: function (url, altText) {
      var img = getEl('zoom-loading-image');
      var wrap = getEl('zoom-loading-image-wrap');
      if (img && url) {
        img.src = url;
        img.alt = altText || '';
        wrap.style.display = '';
      }
    },

    progressTo: function (target, duration, callback) {
      if (!_visible) return;
      stopAnimation();
      var from = _currentProgress;
      var clamped = Math.min(100, Math.max(0, target));
      animateProgress(from, clamped, duration || 800, callback);
    },

    hide: function () {
      if (!overlay || !_visible) return;
      stopAnimation();
      updateProgressUI(100);
      _currentProgress = 100;

      var self = this;
      setTimeout(function () {
        overlay.classList.remove('active');
        _visible = false;
      }, 400);

      setTimeout(function () {
        var spinner = getEl('zoom-loading-spinner');
        if (spinner) spinner.style.display = 'none';
        var msg = getEl('zoom-loading-message');
        if (msg) msg.textContent = '';
      }, 300);
    },

    isVisible: function () {
      return _visible;
    }
  };

  ns.ZoomLoading = ZoomLoading;

})(window.ZoomStore);
