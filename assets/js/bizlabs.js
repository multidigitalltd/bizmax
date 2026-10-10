/**
 * Bizmax – BizLabs page: "read more", accessible tabs and chart animation/hover.
 * Scroll reveal, flower spin, counters and the sign-up form come from the shared page script (home.js).
 * Everything renders complete without JS; this file only adds behaviour.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches || document.documentElement.classList.contains('bz-a11y-still');

	/* ---- About: read more / show less ---- */
	document.querySelectorAll('[data-bz-more]').forEach(function (btn) {
		var target = document.getElementById(btn.getAttribute('aria-controls'));
		var label = btn.querySelector('[data-bz-more-label]');
		if (!target || !label) {
			return;
		}
		btn.addEventListener('click', function () {
			var open = btn.getAttribute('aria-expanded') !== 'true';
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			target.hidden = !open;
			label.textContent = btn.getAttribute(open ? 'data-less' : 'data-more');
		});
	});

	/* ---- Data section: tabs + charts ---- */
	var section = document.querySelector('[data-bz-data]');
	if (!section) {
		return;
	}
	var tabs = Array.prototype.slice.call(section.querySelectorAll('[role="tab"]'));
	var donuts = Array.prototype.slice.call(section.querySelectorAll('[data-bz-donut]'));

	// Donut segments: remember their final length so they can grow from zero.
	var C = 2 * Math.PI * 70;
	var setDonut = function (svg, grown) {
		svg.querySelectorAll('circle').forEach(function (c) {
			var len = parseFloat(c.getAttribute('data-len')) || 0;
			c.setAttribute('stroke-dasharray', grown ? len + ' ' + (C - len) : '0 ' + C);
		});
	};

	var grow = function () {
		section.classList.add('is-grown');
		donuts.forEach(function (svg) { setDonut(svg, true); });
	};
	var replay = function () {
		if (reduceMotion) {
			return;
		}
		section.classList.remove('is-grown');
		donuts.forEach(function (svg) { setDonut(svg, false); });
		// Let the browser paint the empty state, then grow again (matches the design's 60ms restart).
		window.setTimeout(grow, 60);
	};

	if (!reduceMotion && 'IntersectionObserver' in window) {
		section.classList.add('is-armed');
		donuts.forEach(function (svg) { setDonut(svg, false); });
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					// Small delay so the section's own reveal has started.
					window.setTimeout(grow, 200);
					io.disconnect();
				}
			});
		}, { rootMargin: '0px 0px -120px 0px' });
		io.observe(section);
	}

	// Tabs (WAI-ARIA tabs pattern: arrows, Home/End, automatic activation).
	var select = function (tab, focus) {
		tabs.forEach(function (t) {
			var on = t === tab;
			t.setAttribute('aria-selected', on ? 'true' : 'false');
			t.setAttribute('tabindex', on ? '0' : '-1');
			var panel = document.getElementById(t.getAttribute('aria-controls'));
			if (panel) {
				panel.hidden = !on;
			}
		});
		if (focus) {
			tab.focus();
		}
		if (section.classList.contains('is-grown')) {
			replay();
		}
	};
	tabs.forEach(function (tab, i) {
		tab.addEventListener('click', function () { select(tab, false); });
		tab.addEventListener('keydown', function (e) {
			var rtl = document.documentElement.dir === 'rtl';
			var next = null;
			if (e.key === (rtl ? 'ArrowLeft' : 'ArrowRight')) {
				next = tabs[(i + 1) % tabs.length];
			} else if (e.key === (rtl ? 'ArrowRight' : 'ArrowLeft')) {
				next = tabs[(i - 1 + tabs.length) % tabs.length];
			} else if (e.key === 'Home') {
				next = tabs[0];
			} else if (e.key === 'End') {
				next = tabs[tabs.length - 1];
			}
			if (next) {
				e.preventDefault();
				select(next, true);
			}
		});
	});

	// Donut hover: thicken the segment, fade the rest, show label + percent in the centre.
	donuts.forEach(function (svg) {
		var label = svg.querySelector('.bz-donut__label');
		var pct = svg.querySelector('.bz-donut__pct');
		var panel = svg.closest('[role="tabpanel"]');
		var legend = panel ? panel.querySelector('.bz-legend') : null;
		var clear = function () {
			svg.classList.remove('is-hover');
			svg.querySelectorAll('circle.is-on').forEach(function (c) { c.classList.remove('is-on'); });
			if (legend) {
				legend.classList.remove('is-hover');
				legend.querySelectorAll('.is-on').forEach(function (li) { li.classList.remove('is-on'); });
			}
			label.textContent = '';
			pct.textContent = '';
		};
		svg.querySelectorAll('circle').forEach(function (c) {
			c.addEventListener('mouseenter', function () {
				clear();
				svg.classList.add('is-hover');
				c.classList.add('is-on');
				label.textContent = c.getAttribute('data-label');
				pct.textContent = c.getAttribute('data-pct');
				if (legend) {
					var li = legend.querySelector('[data-i="' + c.getAttribute('data-i') + '"]');
					legend.classList.add('is-hover');
					if (li) {
						li.classList.add('is-on');
					}
				}
			});
			c.addEventListener('mouseleave', clear);
		});
	});

	// Bar hover: highlight one bar, fade the others.
	section.querySelectorAll('[data-bz-bars]').forEach(function (chart) {
		chart.querySelectorAll('.bz-bars-chart__item').forEach(function (item) {
			item.addEventListener('mouseenter', function () {
				chart.classList.add('is-hover');
				item.classList.add('is-on');
			});
			item.addEventListener('mouseleave', function () {
				chart.classList.remove('is-hover');
				item.classList.remove('is-on');
			});
		});
	});
})();
