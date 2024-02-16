<?php
/**
 * WC_Gateway_Masterpass_Support class file.
 *
 * @package WooCommerce Redsys Gateway
 */

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Dummy Payments Blocks integration
 *
 * @since 1.0.3
 */
final class WC_Gateway_Masterpass_Support extends AbstractPaymentMethodType {

	/**
	 * The gateway instance.
	 *
	 * @var WC_Gateway_Masterpass_Redsys
	 */
	private $gateway;

	/**
	 * Payment method name/id/slug.
	 *
	 * @var string
	 */
	protected $name = 'masterpass';

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_masterpass_settings', array() );

	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return WCRed()->is_gateway_enabled( 'masterpass' );
	}

	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$script_path       = '/assets/js/frontend/blocks.js';
		$script_asset_path = REDSYS_PLUGIN_PATH_P . 'assets/js/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => '1.2.0',
			);
		$script_url        = REDSYS_PLUGIN_URL_P . $script_path;

		wp_register_script(
			'wc-masterpass-payments-blocks',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-masterpass-payments-blocks', 'woo-redsys-gateway-light', REDSYS_PLUGIN_PATH_P . 'languages/' );
		}

		return array( 'wc-masterpass-payments-blocks' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => WCRed()->get_redsys_option( 'title', 'masterpass' ),
			'description' => WCRed()->get_redsys_option( 'description', 'masterpass' ),
			'supports'    => array(
				'products',
				'refunds',
			),
		);
	}
}

