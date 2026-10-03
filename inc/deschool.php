<?php
/**
 * DeSchool LMS, bundled with the theme (formerly the standalone "DeSchool" plugin, md-deschool 1.17.0).
 *
 * The module lives in modules/deschool/ with the plugin's code unchanged, so every post type,
 * meta key, user meta key, option, shortcode, AJAX action, endpoint and template is identical
 * and existing content keeps working.
 *
 * While the standalone plugin is still active it owns everything and the theme copy stays
 * dormant (loading both would declare the same classes twice). Once the plugin is
 * deactivated, the theme copy takes over on the next request with the same data.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

// Plugins load before the theme, so the plugin's constant tells us it is active.
if ( defined( 'MDDS_VERSION' ) ) {
	add_action( 'admin_notices', 'bizmax_deschool_plugin_notice' );
	return;
}

require BIZMAX_DIR . '/modules/deschool/bootstrap.php';

/**
 * Ask administrators to deactivate the standalone plugin now that the theme includes it.
 */
function bizmax_deschool_plugin_notice(): void {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'תבנית ביזמקס כוללת כעת את כל הפונקציונליות של DeSchool. אפשר להשבית את התוסף DeSchool: הקורסים, הפרקים, ההתקדמות והשורטקודים ימשיכו לעבוד מתוך התבנית, עם אותם נתונים. אין למחוק את הקורסים.', 'bizmax' ),
		esc_url( admin_url( 'plugins.php' ) ),
		esc_html__( 'למסך התוספים', 'bizmax' )
	);
}
