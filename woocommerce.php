<?php
/**
 * WooCommerce wrapper.
 *
 * @package Bizmax
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="bz-main bz-page">
	<div class="bz-content bz-content--wide">
		<?php woocommerce_content(); ?>
	</div>
</main>
<?php
get_footer();
