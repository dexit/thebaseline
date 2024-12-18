<?php
/**
 * WooCommerce API Manager Background Updater Class
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle DB
 * updates in the background.
 *
 * @since       2.0
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Background Updater
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	require_once( 'libraries/wp-async-request.php' );
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	require_once( 'libraries/wp-background-process.php' );
}

class WCAM_Background_API_Resource_Activations_Updater extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'wc_am_api_resource_activations_updater';

	/**
	 * Dispatch updater.
	 * Updater will still run via cron job if this fails for any reason.
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			WC_AM_LOG()->log_error( esc_html__( 'Unable to dispatch WooCommerce API Manager API Resource Activations updater: ', 'woocommerce-api-manager' ) . $dispatched->get_error_message(), 'api-resource-activations-update' );
		}
	}

	/**
	 * Handle cron healthcheck
	 * Restart the background process if not already running and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			return;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();

			return;
		}

		$this->handle();
	}

	/**
	 * Schedule fallback event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return $this->is_queue_empty() === false;
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return false
	 */
	protected function task( $item ) {
		if ( ! is_array( $item ) && ! isset( $item[ 'product_id' ] ) && ! isset( $item[ 'order_id' ] ) ) {
			return false;
		}

		global $wpdb;

		$product_id = absint( $item[ 'product_id' ] );
		$order_id   = absint( $item[ 'order_id' ] );

		WC_AM_LOG()->log_info( esc_html__( 'API Resource Activations update started for Product ID# ', 'woocommerce-api-manager' ) . absint( $product_id ) . esc_html__( ' on Order ID# ', 'woocommerce-api-manager' ) . absint( $order_id ), 'api-resource-activations-update' );

		$current_product_activations      = WC_AM_PRODUCT_DATA_STORE()->get_api_activations( $product_id );
		$item_quanity_and_refund_quantity = WC_AM_API_RESOURCE_DATA_STORE()->get_item_quantity_and_refund_quantity_by_order_id_and_product_id( $order_id, $product_id );
		$activations_purchased_total      = $current_product_activations * absint( $item_quanity_and_refund_quantity->item_qty - $item_quanity_and_refund_quantity->refund_qty );

		$data = array(
			'activations_purchased'       => $current_product_activations,
			'activations_purchased_total' => $activations_purchased_total
		);

		$where = array(
			'order_id'   => $order_id,
			'product_id' => $product_id
		);

		$data_format = array(
			'%d',
			'%d'
		);

		$where_format = array(
			'%d',
			'%d'
		);

		$updated = $wpdb->update( $wpdb->prefix . WC_AM_USER()->get_api_resource_table_name(), $data, $where, $data_format, $where_format );

		if ( ! empty( $updated ) ) {
			WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $order_id );
			WC_AM_SMART_CACHE()->refresh_cache_by_order_id( $order_id, false );
		}

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		WC_AM_LOG()->log_info( esc_html__( 'API Resource Activations update completed.', 'woocommerce-api-manager' ), 'api-resource-activations-update' );

		parent::complete();
	}
}