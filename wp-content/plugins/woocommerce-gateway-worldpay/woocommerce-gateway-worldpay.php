<?php
/*
Plugin Name: WooCommerce WorldPay Gateway
Plugin URI: http://woothemes.com/woocommerce/
Description: Extends WooCommerce. Provides a WorldPay gateway for WooCommerce. Includes basic support for Subscriptions. http://www.worldpay.com.
Version: 5.3.2
Author: Andrew Benbow
Author URI: http://www.chromeorange.co.uk
WC requires at least: 3.0.0
WC tested up to: 8.5.0
Woo: 18646:6bc48c9d12dc0c43add4b099665a80b0
*/

/*  Copyright 2011  Andrew Benbow  (email : andrew@chromeorange.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	Test environment:
	https://secure-test.worldpay.com/wcc/iadmin (https://secure-test.worldpay.com/wcc/iadmin)

	Production environment:
	https://secure.worldpay.com/wcc/iadmin (https://secure.worldpay.com/wcc/iadmin)
*/

// Blocks
use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '6bc48c9d12dc0c43add4b099665a80b0', '18646' );

// Defines
define( 'WORLDPAYPLUGINPATH', plugin_dir_path( __FILE__ ) );
define( 'WORLDPAYPLUGINURL', plugin_dir_url( __FILE__ ) );
define( 'WORLDPAYPLUGINVERSION', '5.3.2' );

// Load Admin files
if( is_admin() ) {
	include('classes/class-wc-gateway-worldpay-security.php');
	$WC_Gateway_Worldpay_Security = new WC_Gateway_Worldpay_Security();
}

/**
 * Localization
 */
load_plugin_textdomain( 'woocommerce_worlday', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );


// Init WorldPay Gateway after WooCommerce has loaded
add_action( 'plugins_loaded', 'init_worldpay_gateway', 0 );

function init_worldpay_gateway() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	/**
	 * Include Form Gateway class
	 */
	include('classes/worldpay-form-class.php');


	add_filter('woocommerce_payment_gateways', 'add_worldpay_form_gateway' );

	/**
	 * Load the widget
	 */
	include('classes/class-wc-gateway-worldpay-widget.php');

	// Add Fraud Order Status
	add_filter( 'woocommerce_register_shop_order_post_statuses', 'worldpay_register_fraud_order_status' );
	add_filter( 'wc_order_statuses', 'worldpay_fraud_order_status' );
	// Set Fraud Order Status as a paid order status
	add_filter( 'woocommerce_order_is_paid_statuses', 'worldpay_fraud_order_status_is_paid' );

	function worldpay_fraud_order_status( $statuses ) {
		$statuses['wc-fraud-screen'] = _x( 'Fraud Screen', 'Order status', 'woocommerce_worlday' );
		return $statuses;
	}

	/**
	 * New order status for WooCommerce 2.2 or later
	 *
	 * @return void
	 */
	function worldpay_register_fraud_order_status( $statuses ) {
	    $statuses['wc-fraud-screen'] = array(
	            'label'                     => _x( 'Fraud Screen', 'Order status', 'woocommerce_worlday' ),
	            'public'                    => true,
	            'exclude_from_search'       => false,
	            'show_in_admin_all_list'    => true,
	            'show_in_admin_status_list' => true,
	            'label_count'               => _n_noop( 'Fraud Screening Required <span class="count">(%s)</span>', 'Fraud Screening Required <span class="count">(%s)</span>', 'woocommerce_worlday' )
	        );

	    return $statuses;
	}

	/**
	 * [worldpay_fraud_order_status_is_paid description]
	 * @param  [type] $paid [description]
	 * @return [type]       [description]
	 */
	function worldpay_fraud_order_status_is_paid( $paid ) {
		$paid[] = 'fraud-screen';

		return $paid;
	}

} // END init_worldpay_gateway

/**
 * Add the Gateway to WooCommerce
 */
function add_worldpay_form_gateway($methods) {
	$methods[] = 'WC_Gateway_Worldpay_Form';
	return $methods;
}

// Blocks Support
add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_worldpay_woocommerce_block_support' );

function woocommerce_gateway_worldpay_woocommerce_block_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once( dirname(__FILE__) . "/classes/blocks/blocks-class.php" );
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Worldpay_Blocks_Support );
			}
		);
	}
}

// Support HPOS
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );
