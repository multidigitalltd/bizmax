/* Bizmax – site notice popup: shows once per visitor, until the notice text changes (no dependencies). */
(function () {
	'use strict';

	var dialog = document.getElementById('bz-notice');
	if (!dialog) {
		return;
	}

	var KEY = 'bzNotice';
	var id = dialog.getAttribute('data-bz-notice');

	function seen() {
		try {
			return localStorage.getItem(KEY) === id;
		} catch (e) {
			return false;
		}
	}

	function remember() {
		try {
			localStorage.setItem(KEY, id);
		} catch (e) {}
	}

	if (seen()) {
		dialog.remove();
		return;
	}

	function close() {
		if (typeof dialog.close === 'function') {
			dialog.close();
		} else {
			dialog.removeAttribute('open');
			remember();
		}
	}

	// Esc, the close buttons and a click on the backdrop all end here.
	dialog.addEventListener('close', remember);
	dialog.querySelectorAll('[data-bz-notice-close]').forEach(function (el) {
		el.addEventListener('click', close);
	});
	dialog.addEventListener('click', function (e) {
		if (e.target === dialog) {
			close();
		}
	});

	// Open shortly after load, so it does not compete with the first paint.
	window.setTimeout(function () {
		if (typeof dialog.showModal === 'function') {
			dialog.showModal();
		} else {
			dialog.setAttribute('open', '');
		}
	}, 1200);
})();
