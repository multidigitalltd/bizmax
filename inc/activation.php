<?php
/**
 * First-run setup: create the home page, default menus and an accessibility statement draft.
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
	bizmax_ensure_a11y_page();
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
	if ( $existing ) {
		return (int) $existing[0];
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
 * Create default menus for unassigned locations.
 *
 * @param int $home_id Home page ID (for anchor links).
 */
function bizmax_ensure_menus( int $home_id ): void {
	$home      = $home_id ? get_permalink( $home_id ) : home_url( '/' );
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	$menus = array(
		'primary'  => array(
			'name'  => __( 'תפריט ראשי', 'bizmax' ),
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
			'name'  => __( 'פוטר – עמודה 1', 'bizmax' ),
			'items' => array(
				array( 'מודעות והשראה', $home . '#deschool' ),
				array( 'הנבטה', $home . '#deschool' ),
				array( 'האצה', $home . '#bizlabs' ),
			),
		),
		'footer_2' => array(
			'name'  => __( 'פוטר – עמודה 2', 'bizmax' ),
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
 * Create a draft "הצהרת נגישות" page skeleton (required by Israeli regulation; content to be completed).
 */
function bizmax_ensure_a11y_page(): void {
	if ( get_page_by_path( 'accessibility-statement' ) ) {
		return;
	}
	$content  = '<h2>הצהרת נגישות</h2>';
	$content .= '<p>אנו רואים חשיבות רבה במתן שירות שוויוני לכלל הגולשים ובשיפור הנגישות של האתר לאנשים עם מוגבלות, בהתאם לתקנות שוויון זכויות לאנשים עם מוגבלות (התאמות נגישות לשירות), התשע"ג-2013, ולתקן הישראלי ת"י 5568 המבוסס על הנחיות WCAG 2.2 ברמה AA.</p>';
	$content .= '<h3>התאמות הנגישות באתר</h3><ul><li>ניווט מלא באמצעות מקלדת וקישור "דלג לתוכן".</li><li>תמיכה בקוראי מסך: מבנה סמנטי, כותרות היררכיות, טקסטים חלופיים לתמונות ותוויות לשדות טפסים.</li><li>ניגודיות צבעים תקנית ואפשרות להגדלת הטקסט עד 200%.</li><li>כיבוד העדפת "הפחתת תנועה" של מערכת ההפעלה.</li></ul>';
	$content .= '<h3>דרכי פנייה בנושאי נגישות</h3><p>רכז/ת הנגישות: [שם]<br>טלפון: [טלפון]<br>דוא"ל: [מייל]</p>';
	$content .= '<p>ההצהרה עודכנה בתאריך: [תאריך]</p>';

	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => 'הצהרת נגישות',
			'post_name'    => 'accessibility-statement',
			'post_content' => $content,
		)
	);
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
 * The accessibility statement page (published) for the footer link, if any.
 */
function bizmax_a11y_page_url(): string {
	$page = get_page_by_path( 'accessibility-statement' );
	return ( $page && 'publish' === $page->post_status ) ? (string) get_permalink( $page ) : '';
}
