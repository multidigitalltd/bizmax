<?php
/**
 * DeSchool module bootstrap (theme-bundled replacement for md-deschool.php).
 *
 * Loaded from inc/deschool.php only when the standalone plugin is not active.
 *
 * @package MultiDigital\DeSchool
 */

declare( strict_types=1 );

namespace MultiDigital\DeSchool;

defined( 'ABSPATH' ) || exit;

/**
 * Core constants, as defined by the plugin. MDDS_VERSION follows the theme version so asset
 * URLs (?ver=) change with every theme release, and the module's one-time routine (rewrite
 * flush for /unit/ and /learn/, ensure the account/catalog pages exist) runs after updates.
 */
define( 'MDDS_VERSION', '1.17.0-bizmax-' . BIZMAX_VERSION );
define( 'MDDS_FILE', __FILE__ );
define( 'MDDS_PATH', trailingslashit( __DIR__ ) );
define( 'MDDS_URL', BIZMAX_URI . '/modules/deschool/' );
define( 'MDDS_BASENAME', 'bizmax/modules/deschool/bootstrap.php' );
define( 'MDDS_BUNDLED', true );

require_once MDDS_PATH . 'includes/class-autoloader.php';

Autoloader::register();

/**
 * Access the module singleton (same API as the plugin).
 *
 * @return Plugin
 */
function mdds(): Plugin {
	return Plugin::instance();
}

// The plugin booted on plugins_loaded; the theme loads after it, so boot right away
// (every component hooks into init or later).
mdds();
