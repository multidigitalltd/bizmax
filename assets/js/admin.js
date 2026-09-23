/* Bizmax admin: tabs, media picker and repeaters for the home meta box (no jQuery). */
(function () {
	'use strict';

	var root = document.querySelector('.bz-admin');
	if (!root) {
		return;
	}

	/* ---- Tabs (remember the last tab per page) ---- */
	var tabs = Array.prototype.slice.call(root.querySelectorAll('.bz-admin__tab'));
	var storeKey = 'bzHomeTab:' + (document.getElementById('post_ID') || {}).value;

	function activate(tab, focus) {
		tabs.forEach(function (t) {
			var on = t === tab;
			t.classList.toggle('is-active', on);
			t.setAttribute('aria-selected', on ? 'true' : 'false');
			t.tabIndex = on ? 0 : -1;
			var panel = document.getElementById(t.getAttribute('aria-controls'));
			if (panel) {
				panel.hidden = !on;
			}
		});
		if (focus) {
			tab.focus();
		}
		try {
			sessionStorage.setItem(storeKey, tab.id);
		} catch (e) { /* storage unavailable */ }
	}

	tabs.forEach(function (tab, i) {
		tab.addEventListener('click', function () { activate(tab, false); });
		tab.addEventListener('keydown', function (e) {
			var next = null;
			if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
				var dir = (e.key === 'ArrowLeft') === (document.dir === 'rtl') ? 1 : -1;
				next = tabs[(i + dir + tabs.length) % tabs.length];
			} else if (e.key === 'Home') {
				next = tabs[0];
			} else if (e.key === 'End') {
				next = tabs[tabs.length - 1];
			}
			if (next) {
				e.preventDefault();
				activate(next, true);
			}
		});
	});

	try {
		var saved = document.getElementById(sessionStorage.getItem(storeKey));
		if (saved && tabs.indexOf(saved) > -1) {
			activate(saved, false);
		}
	} catch (e) { /* ignore */ }

	/* ---- Media picker ---- */
	root.addEventListener('click', function (e) {
		var pick = e.target.closest('[data-bz-image-pick]');
		var remove = e.target.closest('[data-bz-image-remove]');
		if (!pick && !remove) {
			return;
		}
		var wrap = e.target.closest('[data-bz-image]');
		var input = wrap.querySelector('input[type=hidden]');
		var preview = wrap.querySelector('.bz-admin__preview');
		var removeBtn = wrap.querySelector('[data-bz-image-remove]');

		if (remove) {
			input.value = '0';
			preview.innerHTML = '';
			removeBtn.hidden = true;
			return;
		}
		if (!window.wp || !wp.media) {
			return;
		}
		var frame = wp.media({
			title: window.bizmaxAdmin ? bizmaxAdmin.chooseImage : 'Choose image',
			button: { text: window.bizmaxAdmin ? bizmaxAdmin.useImage : 'Use image' },
			library: { type: 'image' },
			multiple: false
		});
		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			var src = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
			input.value = att.id;
			preview.innerHTML = '';
			var img = document.createElement('img');
			img.src = src;
			img.alt = '';
			preview.appendChild(img);
			removeBtn.hidden = false;
		});
		frame.open();
	});

	/* ---- Repeaters ---- */
	function renumber(rep) {
		var rows = rep.querySelectorAll(':scope > .bz-admin__rows > [data-bz-row]');
		rows.forEach(function (row, i) {
			row.querySelector('.bz-admin__row-num').textContent = '#' + (i + 1);
			row.querySelectorAll('[name]').forEach(function (el) {
				el.name = el.name.replace(/\[(\d+|__i__)\]/, '[' + i + ']');
			});
		});
		var add = rep.querySelector(':scope > [data-bz-add]');
		add.disabled = rows.length >= parseInt(rep.dataset.max || '20', 10);
	}

	root.querySelectorAll('[data-bz-repeater]').forEach(renumber);

	root.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-bz-add],[data-bz-remove],[data-bz-up],[data-bz-down]');
		if (!btn) {
			return;
		}
		var rep = btn.closest('[data-bz-repeater]');
		var rows = rep.querySelector(':scope > .bz-admin__rows');

		if (btn.hasAttribute('data-bz-add')) {
			var tpl = rep.querySelector(':scope > [data-bz-row-template]');
			var count = rows.children.length;
			var html = tpl.innerHTML.replace(/__i__/g, String(count));
			var holder = document.createElement('div');
			holder.innerHTML = html;
			var row = holder.firstElementChild;
			rows.appendChild(row);
			renumber(rep);
			var first = row.querySelector('input:not([type=hidden]),textarea,select');
			if (first) {
				first.focus();
			}
			return;
		}

		var current = btn.closest('[data-bz-row]');
		if (btn.hasAttribute('data-bz-remove')) {
			if (window.bizmaxAdmin && !window.confirm(bizmaxAdmin.confirmRm)) {
				return;
			}
			current.remove();
		} else if (btn.hasAttribute('data-bz-up') && current.previousElementSibling) {
			rows.insertBefore(current, current.previousElementSibling);
		} else if (btn.hasAttribute('data-bz-down') && current.nextElementSibling) {
			rows.insertBefore(current.nextElementSibling, current);
		}
		renumber(rep);
	});
})();
