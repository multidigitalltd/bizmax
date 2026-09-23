<?php
/**
 * Bizmax theme bootstrap.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

define( 'BIZMAX_VERSION', '1.0.0' );
define( 'BIZMAX_DIR', get_template_directory() );
define( 'BIZMAX_URI', get_template_directory_uri() );

require BIZMAX_DIR . '/inc/setup.php';
require BIZMAX_DIR . '/inc/assets.php';
require BIZMAX_DIR . '/inc/template-tags.php';
require BIZMAX_DIR . '/inc/customizer.php';
require BIZMAX_DIR . '/inc/home-content.php';
require BIZMAX_DIR . '/inc/contact-form.php';
require BIZMAX_DIR . '/inc/elementor.php';
require BIZMAX_DIR . '/inc/schema-org.php';
require BIZMAX_DIR . '/inc/activation.php';

if ( is_admin() ) {
	require BIZMAX_DIR . '/inc/home-admin.php';
}
