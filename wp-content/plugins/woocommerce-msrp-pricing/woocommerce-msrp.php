<?php
/*
 * Plugin Name: WooCommerce MSRP Pricing
 * Plugin URI: https://woocommerce.com/products/msrp-pricing/
 * Description: A WooCommerce extension that lets you flag Manufacturer Suggested Retail Prices against products, and display them on the front end.
 * Author: Ademti Software Ltd.
 * Version: 3.4.26
 * Woo: 18727:b9133a56078a1ffa217e74136769022b
 * WC requires at least: 8.2
 * WC tested up to: 8.5
 * Author URI: https://www.ademti-software.co.uk/
 * License: GPLv3
*/

define( 'WOOCOMMERCE_MSRP_VERSION', '3.4.26' );

/**
 * Add default option settings on plugin activation
 */
function woocommerce_msrp_activate() {
	add_option( 'woocommerce_msrp_status', 'always', '', true );
	add_option( 'woocommerce_msrp_description', 'MSRP', '', true );
}
register_activation_hook( __FILE__, 'woocommerce_msrp_activate' );

/**
 * Require classes.
 */
require_once 'woocommerce-msrp-admin.php';
require_once 'woocommerce-msrp-frontend.php';
require_once 'woocommerce-msrp-import-export.php';
require_once 'woocommerce-msrp-main.php';
require_once 'woocommerce-msrp-shortcodes.php';
require_once 'woocommerce-msrp-template-tags.php';
require_once 'woocommerce-msrp-woocommerce-product-feeds-integration.php';

// Run the extension when all plugins loaded.
$woocommerce_msrp_main = new woocommerce_msrp_main();
add_action( 'plugins_loaded', [ $woocommerce_msrp_main, 'run' ] );

/**
 * Declare support for High Performance Order Storage.
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);
