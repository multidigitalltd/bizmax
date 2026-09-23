/* Bizmax – global script: accessible hamburger drawer (no dependencies). */
(function () {
	'use strict';

	var drawer = document.querySelector('[data-bz-drawer]');
	var opener = document.querySelector('[data-bz-drawer-open]');
	if (!drawer || !opener) {
		return;
	}

	var panel = drawer.querySelector('.bz-drawer__panel');
	var lastFocus = null;
	var siblings = ['masthead', 'main', 'colophon'].map(function (id) { return document.getElementById(id); }).filter(Boolean);
	var FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select,textarea,[tabindex]:not([tabindex="-1"])';

	function setInert(on) {
		siblings.forEach(function (el) {
			if ('inert' in el) {
				el.inert = on;
			}
		});
	}

	function open() {
		lastFocus = document.activeElement;
		drawer.hidden = false;
		opener.setAttribute('aria-expanded', 'true');
		document.body.classList.add('bz-drawer-open');
		setInert(true);
		var first = panel.querySelector(FOCUSABLE);
		(first || panel).focus();
		document.addEventListener('keydown', onKey);
	}

	function close() {
		drawer.hidden = true;
		opener.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('bz-drawer-open');
		setInert(false);
		document.removeEventListener('keydown', onKey);
		if (lastFocus && typeof lastFocus.focus === 'function') {
			lastFocus.focus();
		}
	}

	function onKey(e) {
		if (e.key === 'Escape') {
			e.preventDefault();
			close();
			return;
		}
		if (e.key !== 'Tab') {
			return;
		}
		var items = Array.prototype.slice.call(panel.querySelectorAll(FOCUSABLE));
		if (!items.length) {
			e.preventDefault();
			return;
		}
		var first = items[0];
		var last = items[items.length - 1];
		if (e.shiftKey && (document.activeElement === first || document.activeElement === panel)) {
			e.preventDefault();
			last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault();
			first.focus();
		}
	}

	opener.addEventListener('click', open);
	drawer.querySelectorAll('[data-bz-drawer-close]').forEach(function (el) {
		el.addEventListener('click', close);
	});
	// Close after choosing an in-page anchor so the target is visible.
	drawer.addEventListener('click', function (e) {
		var link = e.target.closest('a[href]');
		if (link && link.getAttribute('href').indexOf('#') > -1) {
			close();
		}
	});
})();
