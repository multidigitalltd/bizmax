/**
 * Lead list: change a lead's status straight from the table.
 *
 * Saves through admin-ajax (nonce + capability checked on the server), announces the result to
 * screen readers, keeps the "new leads" menu badge current and reverts the choice on failure.
 */
( function () {
	'use strict';

	var cfg = window.bizmaxLeads;
	if ( ! cfg ) {
		return;
	}

	var live = document.createElement( 'div' );
	live.className = 'screen-reader-text';
	live.setAttribute( 'role', 'status' );
	live.setAttribute( 'aria-live', 'polite' );
	document.body.appendChild( live );

	function announce( text ) {
		live.textContent = '';
		window.setTimeout( function () {
			live.textContent = text;
		}, 50 );
	}

	function updateBadge( count ) {
		var item = document.querySelector( '#menu-posts-bizmax_lead .wp-menu-name' );
		if ( ! item ) {
			return;
		}
		var badge = item.querySelector( '.awaiting-mod' );
		if ( ! count ) {
			if ( badge ) {
				badge.remove();
			}
			return;
		}
		if ( ! badge ) {
			badge = document.createElement( 'span' );
			badge.innerHTML = '<span class="pending-count" aria-hidden="true"></span>';
			item.appendChild( document.createTextNode( ' ' ) );
			item.appendChild( badge );
		}
		badge.className = 'awaiting-mod count-' + count;
		badge.querySelector( '.pending-count' ).textContent = String( count );
	}

	document.addEventListener( 'change', function ( event ) {
		var select = event.target;
		if ( ! select.matches || ! select.matches( 'select.bz-lead-status' ) ) {
			return;
		}

		var previous = select.getAttribute( 'data-status' );
		var status = select.value;
		var body = new URLSearchParams();
		body.set( 'action', 'bizmax_lead_status' );
		body.set( 'nonce', cfg.nonce );
		body.set( 'id', select.getAttribute( 'data-id' ) );
		body.set( 'status', status );

		select.setAttribute( 'aria-busy', 'true' );
		select.setAttribute( 'data-status', status );

		fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( response ) {
				return response.json().then( function ( json ) {
					if ( ! response.ok || ! json || ! json.success ) {
						throw new Error( 'failed' );
					}
					return json.data;
				} );
			} )
			.then( function ( data ) {
				updateBadge( parseInt( data.new, 10 ) || 0 );
				announce( cfg.saved.replace( '%s', cfg.labels[ status ] || status ) );
			} )
			.catch( function () {
				select.value = previous;
				select.setAttribute( 'data-status', previous );
				announce( cfg.failed );
				window.alert( cfg.failed ); // eslint-disable-line no-alert
			} )
			.then( function () {
				select.removeAttribute( 'aria-busy' );
			} );
	} );
}() );
