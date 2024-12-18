<?php
/**
 * Privacy class file.
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * Privacy class.
 */
class WC_Shipping_Flat_Rate_Boxes_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( __( 'Flat Rate Boxes', 'woocommerce-shipping-flat-rate-boxes' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		// translators: %s is privacy page link.
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-shipping-flat-rate-boxes' ), 'https://docs.woocommerce.com/document/privacy-shipping/#woocommerce-shipping-flat-rate-boxes' ) );
	}
}

new WC_Shipping_Flat_Rate_Boxes_Privacy();
