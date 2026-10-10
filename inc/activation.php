<?php
/**
 * First-run setup: create the home page, default menus and the accessibility statement (inc/a11y-statement.php).
 * Nothing here overrides existing site configuration.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Run once when the theme is activated.
 */
function bizmax_after_switch(): void {
	// Every step is idempotent: nothing is created twice and existing settings are never overridden.
	$home_id = bizmax_ensure_home_page();
	bizmax_ensure_menus( $home_id );
	update_option( 'bizmax_menus_setup', 2 );
	bizmax_ensure_a11y_page();
	update_option( 'bizmax_a11y_statement_setup', 1, false );
	bizmax_ensure_privacy_page();
}
add_action( 'after_switch_theme', 'bizmax_after_switch' );

/**
 * Create the home page (if no page uses the template yet) and make it the front page
 * only when the site has no static front page configured.
 *
 * @return int Page ID.
 */
function bizmax_ensure_home_page(): int {
	$existing = bizmax_find_home_page();
	if ( $existing ) {
		return $existing;
	}

	$home_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => __( 'דף הבית', 'bizmax' ),
			'post_name'    => 'home',
			'post_content' => '',
			'meta_input'   => array( '_wp_page_template' => 'template-home.php' ),
		),
		true
	);
	if ( is_wp_error( $home_id ) ) {
		return 0;
	}

	if ( 'page' !== get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
	return (int) $home_id;
}

/**
 * ID of the page that uses the home template (0 if none). Never creates anything.
 */
function bizmax_find_home_page(): int {
	$existing = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'any',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'meta_key'               => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'             => 'template-home.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	return $existing ? (int) $existing[0] : 0;
}

/**
 * One-time setup on existing installs (runs once, on the first dashboard visit after updating).
 *
 * Sites that had their own "תפריט ראשי" before the theme was activated never got the theme's
 * header menu (the name collided). This creates the design's menus under theme-specific names
 * and assigns them only to locations that are still empty; it never replaces an assigned menu,
 * never creates pages, and does not run again (a deleted menu is not re-created).
 */
function bizmax_maybe_upgrade_menus(): void {
	if ( (int) get_option( 'bizmax_menus_setup', 0 ) >= 2 || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	update_option( 'bizmax_menus_setup', 2 );
	$home_id = bizmax_find_home_page();
	bizmax_ensure_menus( $home_id && 'publish' === get_post_status( $home_id ) ? $home_id : 0 );
}
add_action( 'admin_init', 'bizmax_maybe_upgrade_menus' );

/**
 * Create default menus for unassigned locations.
 *
 * @param int $home_id Home page ID (for anchor links).
 */
function bizmax_ensure_menus( int $home_id ): void {
	$home      = $home_id ? get_permalink( $home_id ) : home_url( '/' );
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	$menus = array(
		'primary'  => array(
			'name'  => __( 'ביזמקס – תפריט ראשי', 'bizmax' ),
			'items' => array(
				array( 'המתחם', $home . '#coworking' ),
				array( 'אודות', $home . '#about' ),
				array( 'דה סקול', $home . '#deschool' ),
				array( 'תכנית ביזלאבס', $home . '#bizlabs' ),
				array( 'יומן פעילויות', $home . '#events' ),
				array( 'יצירת קשר', $home . '#contact' ),
			),
		),
		'footer_1' => array(
			'name'  => __( 'ביזמקס – פוטר עמודה 1', 'bizmax' ),
			'items' => array(
				array( 'מודעות והשראה', $home . '#deschool' ),
				array( 'הנבטה', $home . '#deschool' ),
				array( 'האצה', $home . '#bizlabs' ),
			),
		),
		'footer_2' => array(
			'name'  => __( 'ביזמקס – פוטר עמודה 2', 'bizmax' ),
			'items' => array(
				array( 'דה סקול', $home . '#deschool' ),
				array( 'צמיחה', $home . '#deschool' ),
			),
		),
	);

	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) ) {
			continue;
		}
		// A menu with this name already exists (an earlier activation): assign it, don't duplicate.
		$existing = wp_get_nav_menu_object( $menu['name'] );
		if ( $existing ) {
			$locations[ $location ] = (int) $existing->term_id;
			continue;
		}
		$menu_id = wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		foreach ( $menu['items'] as $position => [ $title, $url ] ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => $title,
					'menu-item-url'      => $url,
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
					'menu-item-position' => $position + 1,
				)
			);
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Make sure a privacy policy page exists (the contact form links to it).
 */
function bizmax_ensure_privacy_page(): void {
	if ( (int) get_option( 'wp_page_for_privacy_policy' ) > 0 ) {
		return;
	}
	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => 'מדיניות הפרטיות',
			'post_name'    => 'privacy-policy',
			'post_content' => '<p>[יש להשלים את מדיניות הפרטיות של האתר]</p>',
		)
	);
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_option( 'wp_page_for_privacy_policy', $page_id );
	}
}

/**
 * The published accessibility statement page URL (footer and accessibility panel), if any.
 * Uses the page chosen in the Customizer, falling back to the page created on activation.
 */
function bizmax_a11y_page_url(): string {
	static $url = null;
	if ( null === $url ) {
		$id   = absint( bizmax_mod( 'bizmax_a11y_page' ) );
		$page = $id ? get_post( $id ) : get_page_by_path( 'accessibility-statement' );
		$url  = ( $page instanceof WP_Post && 'page' === $page->post_type && 'publish' === $page->post_status ) ? (string) get_permalink( $page ) : '';
	}
	return $url;
}
