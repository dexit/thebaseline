<?php
/**
 * Extension Compatibilty
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    2.1.0
 * @version  3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Free_Gift_Coupons_Compatibility Class.
 *
 * Load classes for making Free Gift Coupons compatible with other plugins.
 */
class WC_Free_Gift_Coupons_Compatibility { 

	/**
	 * The single instance of the class
	 *
	 * @var $instance
	 * @since 3.1.0
	 */
	protected static $instance = null;

	/**
	 * Define dependencies.
	 *
	 * @var array $required
	 * @since 3.4.0
	 */
	protected static $required = array(
		'blocks' => '7.2.0',
	);


	/**
	 * Main WC_Free_Gift_Coupons_Compatibility Instance.
	 *
	 * Ensures only one instance of WC_Free_Gift_Coupons_Compatibility is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_Free_Gift_Coupons_Compatibility - Main instance
	 * @since 3.1.0
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {

		// Declare WooCommerce Features compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_features_compatibility' ) );

		// Initialize.
		add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
	}

	/**
	 * Declare WooCommerce Features compatibility.
	 *
	 * @since 3.6.0
	 */
	public function declare_features_compatibility() {

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		// High Performance Order Storage (HPOS) Compatibility
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_Free_Gift_Coupons::plugin_basename(), true );

		// Cart/Checkout Blocks compatibility.
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', WC_Free_Gift_Coupons::plugin_basename(), true );
	}

	/**
	 * Init compatibility classes.
	 */
	public static function init() {

		// All Product for Subscriptions support.
		if ( class_exists( 'WCS_ATT' ) ) {
			include_once  'modules/class-wc-fgc-apfs-compatibility.php' ;
		}

		// WooCommerce Cart/Checkout Blocks support.
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), self::$required[ 'blocks' ], '>=' ) ) {
			include_once 'modules/class-wc-fgc-blocks-compatibility.php';
		}

		// Smart Coupons support.
		if ( class_exists( 'WC_Smart_Coupons' ) ) {
			include_once  'modules/class-wc-fgc-smart-coupons-compatibility.php' ;
		}

		// Subscriptions support.
		if ( class_exists( 'WC_Subscriptions' ) ) {
			include_once  'modules/class-wc-fgc-subscriptions-compatibility.php' ;
		}

		// Product Addons support.
		if ( class_exists( 'WC_Product_Addons' ) ) {
			include_once  'modules/class-wc-fgc-product-addons-compatibility.php';
		}

		// Name Your Price support.
		if ( class_exists( 'WC_Name_Your_Price' ) ) {
			include_once  'modules/class-wc-fgc-name-your-price-compatibility.php';
		}

		// Deactivate functionality from mini-extensions.
		self::unload();


	}

	/**
	 * Unload mini-extensions.
	 * 
	 * @since 3.0.0
	 */
	public static function unload() {
		// Deactivate functionality added by WC Free Gift Coupons - Choose Variation mini-extension.
		if ( class_exists( 'WC_FGC_Choose_Variation' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_FGC_Choose_Variation', 'init' ) );
		}
	}

	/**
	 * ---------------------------------------------------------------------------------
	 * Deprecated Methods
	 * ---------------------------------------------------------------------------------
	 */

	/**
	 * Declare HPOS (Custom Order tables) compatibility.
	 *
	 * @since 3.3.4
	 * @deprecated 3.6.0
	 */
	public function declare_hpos_compatibility() {

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_Free_Gift_Coupons::plugin_basename(), true );
	}

}

WC_Free_Gift_Coupons_Compatibility::instance();
