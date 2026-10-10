/* Bizmax – accessibility panel: native modal dialog, display modes and text size.
   Settings live in localStorage ("bzA11y"), so cached pages stay identical for everyone. */
(function () {
	'use strict';

	var root = document.getElementById('bz-a11y');
	if (!root) {
		return;
	}

	var html = document.documentElement;
	var KEY = 'bzA11y';
	var SIZES = [1, 1.1, 1.25, 1.5, 1.75, 2];
	var EXCLUSIVE = { contrast: 'light', light: 'contrast' };
	var SKIP = /^(SCRIPT|STYLE|NOSCRIPT|TEMPLATE|BR|HEAD|META|LINK|TITLE)$/;

	var toggle = root.querySelector('[data-bz-a11y-open]');
	var dialog = root.querySelector('dialog');
	var status = root.querySelector('[data-bz-a11y-status]');
	var sizeValue = root.querySelector('[data-bz-a11y-size-value]');
	var stepDown = root.querySelector('[data-bz-a11y-size="-1"]');
	var stepUp = root.querySelector('[data-bz-a11y-size="1"]');
	var options = root.querySelectorAll('[data-bz-a11y-opt]');

	/* ---- Saved state ---- */
	var state = {};
	try {
		state = JSON.parse(window.localStorage.getItem(KEY) || '{}') || {};
	} catch (e) {
		state = {};
	}
	var save = function () {
		try {
			window.localStorage.setItem(KEY, JSON.stringify(state));
		} catch (e) { /* Private mode or storage disabled: settings last for this page only. */ }
	};
	var sizeIndex = Math.max(0, SIZES.indexOf(Number(state.size) || 1));

	var announce = function (text) {
		if (!status) {
			return;
		}
		status.textContent = '';
		window.setTimeout(function () { status.textContent = text; }, 60);
	};
	var msg = function (name, value) {
		return (root.getAttribute('data-msg-' + name) || '%s').replace('%s', value);
	};

	/* ---- Text size: scale every element's computed size (works on Elementor content too) ---- */
	var touched = [];
	var observer = null;
	var pending = 0;

	var restoreText = function () {
		for (var i = touched.length - 1; i >= 0; i--) {
			var t = touched[i];
			t.el.style.removeProperty('font-size');
			t.el.style.removeProperty('line-height');
			if (t.fs) { t.el.style.setProperty('font-size', t.fs, t.fsp); }
			if (t.lh) { t.el.style.setProperty('line-height', t.lh, t.lhp); }
		}
		touched = [];
	};

	var scaleText = function () {
		var factor = SIZES[sizeIndex];
		if (observer) { observer.disconnect(); }
		restoreText();
		if (factor === 1) {
			return;
		}
		var keepLineHeight = html.classList.contains('bz-a11y-spacing');
		var all = document.body.getElementsByTagName('*');
		var reads = [];
		var i, el, cs;
		// Read everything first, then write, to avoid layout thrashing.
		for (i = 0; i < all.length; i++) {
			el = all[i];
			if (SKIP.test(el.tagName) || el instanceof SVGElement || root.contains(el)) {
				continue;
			}
			cs = window.getComputedStyle(el);
			reads.push([el, parseFloat(cs.fontSize), cs.lineHeight]);
		}
		for (i = 0; i < reads.length; i++) {
			el = reads[i][0];
			touched.push({
				el: el,
				fs: el.style.getPropertyValue('font-size'),
				fsp: el.style.getPropertyPriority('font-size'),
				lh: el.style.getPropertyValue('line-height'),
				lhp: el.style.getPropertyPriority('line-height')
			});
			if (reads[i][1]) {
				el.style.setProperty('font-size', (reads[i][1] * factor).toFixed(2) + 'px', 'important');
			}
			if (!keepLineHeight && /px$/.test(reads[i][2])) {
				el.style.setProperty('line-height', (parseFloat(reads[i][2]) * factor).toFixed(2) + 'px', 'important');
			}
		}
		// Content added later (popups, AJAX, sliders) is rescaled once things settle.
		if ('MutationObserver' in window) {
			observer = observer || new MutationObserver(function (records) {
				for (var r = 0; r < records.length; r++) {
					if (!root.contains(records[r].target)) {
						window.clearTimeout(pending);
						pending = window.setTimeout(scaleText, 400);
						return;
					}
				}
			});
			observer.observe(document.body, { childList: true, subtree: true });
		}
	};

	var renderSize = function () {
		var pct = Math.round(SIZES[sizeIndex] * 100) + '%';
		if (sizeValue) { sizeValue.textContent = pct; }
		if (stepDown) { stepDown.setAttribute('aria-disabled', sizeIndex === 0 ? 'true' : 'false'); }
		if (stepUp) { stepUp.setAttribute('aria-disabled', sizeIndex === SIZES.length - 1 ? 'true' : 'false'); }
		return pct;
	};

	var setSize = function (index) {
		index = Math.max(0, Math.min(SIZES.length - 1, index));
		if (index === sizeIndex) {
			return;
		}
		sizeIndex = index;
		state.size = SIZES[sizeIndex];
		if (state.size === 1) { delete state.size; }
		save();
		scaleText();
		announce(msg('size', renderSize()));
	};

	/* ---- On/off display modes ---- */
	var labelOf = function (button) {
		var label = button.querySelector('.bz-a11y__opt-label');
		return label ? label.textContent : '';
	};

	var stillMedia = function (on) {
		document.querySelectorAll('video').forEach(function (video) {
			if (on && !video.paused) {
				video.pause();
			}
		});
		document.querySelectorAll('.swiper, .swiper-container').forEach(function (el) {
			var autoplay = el.swiper && el.swiper.autoplay;
			if (autoplay) {
				if (on) { autoplay.stop(); } else { autoplay.start(); }
			}
		});
	};

	var setOption = function (key, on) {
		html.classList.toggle('bz-a11y-' + key, on);
		if (on) { state[key] = true; } else { delete state[key]; }
		options.forEach(function (button) {
			if (button.getAttribute('data-bz-a11y-opt') === key) {
				button.setAttribute('aria-pressed', on ? 'true' : 'false');
			}
		});
		if ('still' === key) { stillMedia(on); }
	};

	options.forEach(function (button) {
		var key = button.getAttribute('data-bz-a11y-opt');
		button.setAttribute('aria-pressed', state[key] === true ? 'true' : 'false');
		button.addEventListener('click', function () {
			var on = button.getAttribute('aria-pressed') !== 'true';
			if (on && EXCLUSIVE[key] && state[EXCLUSIVE[key]]) {
				setOption(EXCLUSIVE[key], false);
			}
			setOption(key, on);
			save();
			if ('spacing' === key && sizeIndex) {
				scaleText();
			}
			announce(msg(on ? 'on' : 'off', labelOf(button)));
		});
	});

	[stepDown, stepUp].forEach(function (button) {
		if (!button) {
			return;
		}
		button.addEventListener('click', function () {
			if (button.getAttribute('aria-disabled') === 'true') {
				return;
			}
			setSize(sizeIndex + parseInt(button.getAttribute('data-bz-a11y-size'), 10));
		});
	});

	var reset = root.querySelector('[data-bz-a11y-reset]');
	if (reset) {
		reset.addEventListener('click', function () {
			Object.keys(state).forEach(function (key) {
				if ('size' !== key) { setOption(key, false); }
			});
			state = {};
			save();
			sizeIndex = 0;
			scaleText();
			renderSize();
			announce(root.getAttribute('data-msg-reset') || '');
		});
	}

	/* ---- Dialog: native modal (focus trap, ESC, inert page); focus returns to the button ---- */
	var isOpen = function () { return dialog.hasAttribute('open'); };
	var open = function () {
		if (typeof dialog.showModal === 'function') {
			dialog.showModal();
		} else {
			dialog.setAttribute('open', '');
		}
		toggle.setAttribute('aria-expanded', 'true');
		var first = dialog.querySelector('[data-bz-a11y-close]');
		if (first) { first.focus(); }
	};
	var close = function () {
		if (!isOpen()) {
			return;
		}
		if (typeof dialog.close === 'function') {
			dialog.close();
		} else {
			dialog.removeAttribute('open');
			onClosed();
		}
	};
	var onClosed = function () {
		toggle.setAttribute('aria-expanded', 'false');
		toggle.focus();
	};

	toggle.addEventListener('click', function () {
		if (isOpen()) { close(); } else { open(); }
	});
	dialog.addEventListener('close', onClosed);
	root.querySelectorAll('[data-bz-a11y-close]').forEach(function (button) {
		button.addEventListener('click', close);
	});
	// A click on the backdrop (outside the dialog box) closes it.
	dialog.addEventListener('click', function (event) {
		if (event.target !== dialog) {
			return;
		}
		var box = dialog.getBoundingClientRect();
		if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) {
			close();
		}
	});
	document.addEventListener('keydown', function (event) {
		if ('Escape' === event.key && isOpen() && typeof dialog.showModal !== 'function') {
			close();
		}
	});

	/* ---- Initial state ---- */
	renderSize();
	if (state.still) { stillMedia(true); }
	if (sizeIndex) {
		scaleText();
	}
	// Breakpoints change the base sizes, so a scaled page is recomputed after resizing.
	var resizeTimer = 0;
	window.addEventListener('resize', function () {
		if (!sizeIndex) {
			return;
		}
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(scaleText, 250);
	});
}());
