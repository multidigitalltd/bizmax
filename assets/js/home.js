/* Bizmax – home page: scroll reveal, flower spin, counters, testimonials, contact form. */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var cfg = window.bizmaxHome || { restUrl: '/wp-json/bizmax/v1/', messages: {} };
	var msg = cfg.messages || {};

	/* ---- Scroll reveal (one-shot) ---- */
	var reveals = document.querySelectorAll('.rv');
	if (!('IntersectionObserver' in window) || reduceMotion) {
		reveals.forEach(function (el) { el.classList.add('rv-in'); });
	} else {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('rv-in');
					io.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -60px 0px' });
		reveals.forEach(function (el) { io.observe(el); });
	}

	/* ---- Flower rotation on scroll (rAF-throttled) ---- */
	var flowers = document.querySelectorAll('.bz-flower');
	if (flowers.length && !reduceMotion) {
		var ticking = false;
		var spin = function () {
			var deg = (window.scrollY * 0.15) % 360;
			flowers.forEach(function (el) { el.style.transform = 'rotate(' + deg + 'deg)'; });
			ticking = false;
		};
		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(spin);
			}
		}, { passive: true });
		spin();
	}

	/* ---- Count-up ---- */
	var counters = document.querySelectorAll('.count[data-count]');
	if (counters.length && !reduceMotion && 'IntersectionObserver' in window) {
		var animate = function (el) {
			var target = parseInt(el.getAttribute('data-count'), 10) || 0;
			var suffix = el.getAttribute('data-suffix') || '';
			var t0 = performance.now();
			var dur = 1600;
			var tick = function (t) {
				var p = Math.min(1, (t - t0) / dur);
				var eased = 1 - Math.pow(1 - p, 3);
				el.textContent = Math.round(target * eased).toLocaleString('en-US') + suffix;
				if (p < 1) {
					window.requestAnimationFrame(tick);
				}
			};
			window.requestAnimationFrame(tick);
		};
		var cio = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					animate(entry.target);
					cio.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -80px 0px' });
		counters.forEach(function (el) { cio.observe(el); });
	}

	/* ---- Testimonials rotation ---- */
	var alumni = document.querySelector('[data-bz-alumni]');
	if (alumni) {
		var dataEl = alumni.querySelector('[data-bz-quotes]');
		var quotes = [];
		try {
			quotes = JSON.parse(dataEl.textContent) || [];
		} catch (e) { quotes = []; }

		var slots = alumni.querySelectorAll('[data-bz-slot]');
		var status = alumni.querySelector('[data-bz-status]');
		var idx = 0;
		var n = quotes.length;

		var render = function () {
			slots.forEach(function (slot) {
				var offset = parseInt(slot.getAttribute('data-bz-slot'), 10);
				var q = quotes[(idx + offset) % n];
				if (!q) {
					return;
				}
				slot.classList.add('is-switching');
				window.requestAnimationFrame(function () {
					slot.querySelector('[data-bz-text]').textContent = offset === 0 ? q.text : q.short;
					slot.querySelector('[data-bz-name]').textContent = q.name;
					slot.querySelector('[data-bz-role]').textContent = q.role;
					var img = slot.querySelector('[data-bz-img]');
					if (img && img.getAttribute('src') !== q.img) {
						img.setAttribute('src', q.img);
					}
					slot.classList.remove('is-switching');
				});
			});
			if (status && msg.quote) {
				status.textContent = msg.quote.replace('%1$d', idx + 1).replace('%2$d', n);
			}
		};

		var next = alumni.querySelector('[data-bz-next]');
		var prev = alumni.querySelector('[data-bz-prev]');
		if (n > 1 && next && prev) {
			next.addEventListener('click', function () { idx = (idx + 1) % n; render(); });
			prev.addEventListener('click', function () { idx = (idx + n - 1) % n; render(); });
		}
	}

	/* ---- Contact form ---- */
	var form = document.querySelector('[data-bz-contact]');
	if (form && window.fetch) {
		var token = null;
		var tokenPromise = null;
		var submit = form.querySelector('.bz-form__submit');
		var statusEl = form.querySelector('[data-bz-status]');
		var submitLabel = submit.textContent;

		var fetchToken = function () {
			if (token) {
				return Promise.resolve(token);
			}
			if (!tokenPromise) {
				tokenPromise = fetch(cfg.restUrl + 'contact/token', { credentials: 'same-origin', cache: 'no-store' })
					.then(function (r) { return r.json(); })
					.then(function (data) { token = data; return data; })
					.catch(function () { tokenPromise = null; return null; });
			}
			return tokenPromise;
		};
		// Fetch lazily on first interaction so cached pages never embed a stale nonce.
		['focusin', 'pointerdown'].forEach(function (ev) {
			form.addEventListener(ev, fetchToken, { once: true });
		});

		var setError = function (name, text) {
			var field = form.querySelector('[name="' + name + '"]');
			var err = field ? document.getElementById(field.id + '-err') : null;
			if (!field || !err) {
				return;
			}
			err.textContent = text || '';
			err.hidden = !text;
			if (text) {
				field.setAttribute('aria-invalid', 'true');
			} else {
				field.removeAttribute('aria-invalid');
			}
		};

		var setStatus = function (text, isError) {
			if (!statusEl) {
				return;
			}
			statusEl.textContent = text || '';
			statusEl.classList.toggle('is-error', !!isError);
		};

		var validate = function () {
			var ok = true;
			var name = form.elements.name.value.trim();
			var email = form.elements.email.value.trim();
			var phone = form.elements.phone.value.replace(/\D+/g, '');
			if (phone.indexOf('972') === 0) {
				phone = '0' + phone.slice(3);
			}
			var checks = [
				['name', name.length >= 2, form.elements.name.getAttribute('data-msg') || 'נא להזין שם מלא.'],
				['email', /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email), 'נא להזין כתובת מייל תקינה.'],
				['phone', /^0\d{8,9}$/.test(phone), 'נא להזין מספר טלפון ישראלי תקין.'],
				['privacy', form.elements.privacy.checked, 'יש לאשר את מדיניות הפרטיות.']
			];
			var firstBad = null;
			checks.forEach(function (c) {
				setError(c[0], c[1] ? '' : c[2]);
				if (!c[1]) {
					ok = false;
					firstBad = firstBad || form.querySelector('[name="' + c[0] + '"]');
				}
			});
			if (firstBad) {
				firstBad.focus();
			}
			return ok;
		};

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (form.classList.contains('is-success') || !validate()) {
				return;
			}
			submit.disabled = true;
			setStatus(msg.sending || '', false);

			fetchToken().then(function (tok) {
				if (!tok) {
					throw new Error('token');
				}
				return fetch(cfg.restUrl + 'contact', {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						name: form.elements.name.value.trim(),
						email: form.elements.email.value.trim(),
						phone: form.elements.phone.value.trim(),
						privacy: form.elements.privacy.checked,
						website: form.elements.website.value,
						token: tok.token,
						ts: tok.ts,
						page: parseInt(form.getAttribute('data-page'), 10) || 0
					})
				});
			}).then(function (r) {
				return r.json().then(function (data) { return { ok: r.ok, data: data }; });
			}).then(function (res) {
				if (res.ok) {
					form.classList.add('is-success');
					submit.textContent = msg.success || 'נשלח!';
					setStatus('', false);
					form.querySelectorAll('.bz-form__input').forEach(function (i) { i.readOnly = true; });
					return;
				}
				var fields = res.data && res.data.data && res.data.data.fields;
				if (fields) {
					Object.keys(fields).forEach(function (k) { setError(k, fields[k]); });
				}
				if (res.data && res.data.code === 'bizmax_bad_token') {
					token = null;
					tokenPromise = null;
				}
				setStatus((res.data && res.data.message) || msg.error || '', true);
				submit.disabled = false;
			}).catch(function () {
				setStatus(msg.error || '', true);
				submit.disabled = false;
				submit.textContent = submitLabel;
			});
		});
	}
})();
