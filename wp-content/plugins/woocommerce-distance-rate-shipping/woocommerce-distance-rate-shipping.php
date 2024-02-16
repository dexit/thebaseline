<?php
/**
 * Plugin Name: WooCommerce Distance Rate Shipping
 * Version: 1.4.0
 * Plugin URI: https://woocommerce.com/products/woocommerce-distance-rate-shipping/
 * Description: Set up shipping rates based on the distance from your store to the customer, as well as charge based on number of items, order total or time to travel to customer.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 6.2
 * Tested up to: 6.4
 * Text Domain: woocommerce-distance-rate-shipping
 * Domain Path: /languages
 * WC tested up to: 8.5
 * WC requires at least: 8.3
 *
 * Woo: 461314:bbb6fc986fe0f074dcd5141d451b4821
 *
 * Copyright: © 2024 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-distance-rate-shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WC_DISTANCE_RATE_VERSION' ) ) {
	define( 'WC_DISTANCE_RATE_VERSION', '1.4.0' ); // WRCS: DEFINED_VERSION.
	define( 'WC_DISTANCE_RATE_FILE', __FILE__ );
}

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_distance_rate_init', 1 );

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );

/**
 * Initialize plugin.
 */
function wc_distance_rate_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_distance_rate_woocommerce_deactivated' );
		return;
	}

	require_once __DIR__ . '/includes/class-wc-distance-rate.php';
	new WC_Distance_Rate();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_distance_rate_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Distance Rate Shipping requires %s to be installed and active.', 'woocommerce-distance-rate-shipping' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

/**
 * Declaring Cart and Checkout Blocks compatibility.
 */
function wc_distance_rate_declare_cart_checkout_blocks_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'wc_distance_rate_declare_cart_checkout_blocks_compatibility' );
