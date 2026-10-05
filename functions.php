<?php
/**
 * Bizmax theme bootstrap.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

define( 'BIZMAX_VERSION', '1.5.2' );
define( 'BIZMAX_DIR', get_template_directory() );
define( 'BIZMAX_URI', get_template_directory_uri() );

require BIZMAX_DIR . '/inc/setup.php';
require BIZMAX_DIR . '/inc/assets.php';
require BIZMAX_DIR . '/inc/performance.php';
require BIZMAX_DIR . '/inc/template-tags.php';
require BIZMAX_DIR . '/inc/customizer.php';
require BIZMAX_DIR . '/inc/home-content.php';
require BIZMAX_DIR . '/inc/bizlabs.php';
require BIZMAX_DIR . '/inc/leads.php';
require BIZMAX_DIR . '/inc/contact-form.php';
require BIZMAX_DIR . '/inc/turnstile.php';
require BIZMAX_DIR . '/inc/elementor.php';
require BIZMAX_DIR . '/inc/schema-org.php';
require BIZMAX_DIR . '/inc/activation.php';
require BIZMAX_DIR . '/inc/a11y-statement.php';
require BIZMAX_DIR . '/inc/a11y.php';
require BIZMAX_DIR . '/inc/deschool.php';

if ( is_admin() ) {
	require BIZMAX_DIR . '/inc/home-admin.php';
	require BIZMAX_DIR . '/inc/leads-admin.php';
}
