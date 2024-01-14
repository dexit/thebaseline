<?php

/**
 * WooCommerce API Manager Admin System Status Class
 *
 * @since       2.0.21
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Admin/Admin System Status
 */

defined( 'ABSPATH' ) || exit;

class WC_AM_Admin_System_Status {

	private $aws_s3_configured;
	private $wc_am_product_id = 260110;

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Admin_System_Status
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		$amazon_s3_constants         = defined( 'WC_AM_AWS3_ACCESS_KEY_ID' ) && defined( 'WC_AM_AWS3_SECRET_ACCESS_KEY' );
		$amazon_s3_access_key_id     = get_option( 'woocommerce_api_manager_amazon_s3_access_key_id' );
		$amazon_s3_secret_access_key = get_option( 'woocommerce_api_manager_amazon_s3_secret_access_key' );
		$configured                  = $amazon_s3_constants || ( ! empty( $amazon_s3_access_key_id ) && ! empty( $amazon_s3_secret_access_key ) );
		$this->aws_s3_configured     = ! empty( $configured );

		add_action( 'woocommerce_system_status_report', array( $this, 'render_system_status_items' ) );
	}

	/**
	 * Renders the WooCommerce API Manager information on the WooCommerce status page
	 *
	 * @since 2.1
	 */
	public function render_system_status_items() {
		$wc_api_manager_data = array();

		$this->set_api_manager_cache( $wc_api_manager_data );
		$this->set_api_manager_homepage_cached( $wc_api_manager_data );
		$this->set_live_site_url( $wc_api_manager_data );
		$this->set_api_manager_version( $wc_api_manager_data );
		$this->set_api_manager_database_version( $wc_api_manager_data );
		$this->set_woocommerce_account_data( $wc_api_manager_data );
		$this->set_api_manager_amazon_s3_configured( $wc_api_manager_data );

		if ( $this->aws_s3_configured ) {
			$this->set_api_manager_amazon_s3_region( $wc_api_manager_data );
		}

		if ( WCAM()->get_db_cache() ) {
			$this->set_api_manager_api_cache_expires( $wc_api_manager_data );
			$this->set_api_manager_database_cache_expires( $wc_api_manager_data );
		}

		$this->set_api_manager_download_url_expires( $wc_api_manager_data );
		$this->set_api_manager_hide_product_order_api_keys( $wc_api_manager_data );
		$this->set_api_manager_hide_masterapi_key( $wc_api_manager_data );
		$this->set_api_manager_grace_period( $wc_api_manager_data );

		$this->set_api_manager_api_key_activations( $wc_api_manager_data );
		$this->set_api_manager_products_count( $wc_api_manager_data );
		$this->set_api_manager_api_resources( $wc_api_manager_data );
		$this->set_api_manager_wc_am_subs_api_resources( $wc_api_manager_data );
		$this->set_api_manager_wc_subs_api_resources( $wc_api_manager_data );
		$this->set_api_manager_associated_api_keys( $wc_api_manager_data );
		$this->set_api_manager_secure_hash_count( $wc_api_manager_data );
		$this->set_api_manager_grace_period_count( $wc_api_manager_data );
		$this->set_api_manager_legacy_product_id_count( $wc_api_manager_data );
		$this->set_api_manager_next_api_resource_cleanup_scheduled( $wc_api_manager_data );

		$this->set_theme_overrides( $wc_api_manager_data );

		$system_status_sections = array(
			array(
				'title'   => esc_attr__( 'WooCommerce API Manager', 'woocommerce-api-manager' ),
				'tooltip' => esc_attr__( 'This section shows information about the WooCommerce API Manager.', 'woocommerce-api-manager' ),
				'data'    => apply_filters( 'wc_api_manager_system_status', $wc_api_manager_data ),
			),

		);

		foreach ( $system_status_sections as $section ) {
			$section_title   = $section[ 'title' ];
			$section_tooltip = $section[ 'tooltip' ];
			$debug_data      = $section[ 'data' ];

			include_once __DIR__ . '/status.php';
		}
	}

	/**
	 * Database Cache on or off.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_cache( &$debug_data ) {
		$debug_data[ 'wc_api_manager_cache' ] = array(
			'name'    => _x( 'API & Database Cache Enabled', 'API & Database Cache Enabled, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'   => 'Cache Enabled',
			'note'    => WCAM()->get_db_cache() ? esc_attr__( 'Yes', 'woocommerce-api-manager' ) : esc_attr__( 'No', 'woocommerce-api-manager' ),
			'success' => WCAM()->get_db_cache() ? 1 : 0,
		);
	}

	/**
	 * Homepage cachable?
	 *
	 * @since 3.1.2
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_homepage_cached( &$debug_data ) {
		$debug_data[ 'wc_api_manager_homepage_cached' ] = array(
			'name'    => _x( 'Homepage cachable?', 'Is the homepage cachable, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'   => 'Hide Product Order API Keys?',
			'note'    => WC_AM_DISABLE_HOMEPAGE_CACHE ? esc_attr__( 'No', 'woocommerce-api-manager' ) : esc_attr__( 'Yes', 'woocommerce-api-manager' ),
			'success' => WC_AM_DISABLE_HOMEPAGE_CACHE ? 0 : 1,
		);
	}

	/**
	 * Live site URL.
	 *
	 * @since 2.6.8
	 *
	 * @param $debug_data
	 */
	private function set_live_site_url( &$debug_data ) {
		$debug_data[ 'wcs_live_site_url' ] = array(
			'name'      => _x( 'Live URL', 'Live URL, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Live URL',
			'note'      => '<a href="' . esc_url( home_url() ) . '">' . esc_html( home_url() ) . '</a>',
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * WooCommerce API Manager Version.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_version( &$debug_data ) {
		$debug_data[ 'wc_api_manager_version' ] = array(
			'name'      => _x( 'WC API Manager Version', 'WC API Manager Version, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'WC API Manager Version',
			'note'      => esc_attr( WC_AM_VERSION ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * WooCommerce API Manager Database Version.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_database_version( &$debug_data ) {
		$wc_am_db_version = get_option( 'wc_am_db_version' );

		$debug_data[ 'wc_api_manager_database_version' ] = array(
			'name'      => _x( 'WC API Manager Database Version', 'WC API Manager Database Version, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'WC API Manager Database Version',
			'note'      => ! empty( $wc_am_db_version ) ? esc_attr( $wc_am_db_version ) : 'Not yet upgraded.',
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Include information about whether the store is linked to a WooCommerce account and whether they have an active WCS product key.
	 */
	/**
	 * WooCommerce API Manager Account Data.
	 *
	 * @since 2.6.8
	 *
	 * @param $debug_data
	 */
	private function set_woocommerce_account_data( &$debug_data ) {

		if ( ! class_exists( 'WC_Helper' ) ) {
			return;
		}

		$woocommerce_account_auth      = WC_Helper_Options::get( 'auth' );
		$woocommerce_account_connected = ! empty( $woocommerce_account_auth );

		$debug_data[ 'wcs_woocommerce_account_connected' ] = array(
			'name'    => _x( 'WooCommerce Account Connected', 'label for the system status page', 'woocommerce-api-manager' ),
			'label'   => 'WooCommerce Account Connected',
			'note'    => $woocommerce_account_connected ? 'Yes' : 'No',
			'success' => $woocommerce_account_connected,
		);

		if ( ! $woocommerce_account_connected ) {
			return;
		}

		// Check for an active WooCommerce Subscriptions product key
		$woocommerce_account_api_manger = WC_Helper::get_subscriptions();
		$site_id                        = absint( $woocommerce_account_auth[ 'site_id' ] );
		$has_active_product_key         = false;

		foreach ( $woocommerce_account_api_manger as $subscription ) {
			if ( ! empty( $subscription[ 'product_id' ] ) && $this->wc_am_product_id === $subscription[ 'product_id' ] ) {
				$has_active_product_key = in_array( $site_id, $subscription[ 'connections' ], false ); // phpcs:ignore WordPress.PHP.StrictInArray.FoundNonStrictFalse -- In case the value from $subscription['connections'] is a string.
				break;
			}
		}

		$debug_data[ 'wcs_active_product_key' ] = array(
			'name'    => _x( 'Active Product Key', 'label for the system status page', 'woocommerce-api-manager' ),
			'label'   => 'Active Product Key',
			'note'    => $has_active_product_key ? 'Yes' : 'No',
			'success' => $has_active_product_key,
		);
	}

	/**
	 * Amazon S3 Download Configured.
	 *
	 * @since 2.1.3
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_amazon_s3_configured( &$debug_data ) {
		$debug_data[ 'wc_api_manager_amazon_s3_configured' ] = array(
			'name'    => _x( 'Amazon S3 Configured', 'Amazon S3 Configured, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'   => 'Amazon S3 Configured',
			'note'    => $this->aws_s3_configured ? esc_attr__( 'Yes', 'woocommerce-api-manager' ) : esc_attr__( 'No', 'woocommerce-api-manager' ),
			'success' => $this->aws_s3_configured ? 1 : 0,
		);
	}

	/**
	 * WooCommerce API Manager Database Version.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_amazon_s3_region( &$debug_data ) {
		$aws_s3_region = get_option( 'woocommerce_api_manager_aws_s3_region' );

		$debug_data[ 'wc_api_manager_$aws_s3_region' ] = array(
			'name'      => _x( 'Amazon S3 Region', 'Amazon S3 Region, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Amazon S3 Region',
			'note'      => ! empty( $aws_s3_region ) ? esc_attr( $aws_s3_region ) : sprintf( __( '%sPick a region.%s', 'woocommerce-api-manager' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=api_manager' ) ) . '">', '</a>' ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * API Cache Expires.
	 *
	 * @since 2.2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_api_cache_expires( &$debug_data ) {
		$debug_data[ 'wc_api_manager_api_cache_expires' ] = array(
			'name'      => _x( 'API Cache Expires', 'API Cache Expires, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'API Cache Expires',
			'note'      => ( absint( WCAM()->get_api_cache_expires() ) / 60 ) . esc_html__( ' hour', 'woocommerce-api-manager' ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Database Cache Expires.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_database_cache_expires( &$debug_data ) {
		$debug_data[ 'wc_api_manager_database_cache_expires' ] = array(
			'name'      => _x( 'Database Cache Expires', 'Database Cache Expires, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Database Cache Expires',
			'note'      => ( absint( WCAM()->get_db_cache_expires() ) / 60 ) . esc_html__( ' hours', 'woocommerce-api-manager' ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Download URLs Expire.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_download_url_expires( &$debug_data ) {
		$time         = get_option( 'woocommerce_api_manager_url_expire' );
		$expires_time = $time < 2 ? esc_attr( $time ) . esc_attr__( ' day', 'woocommerce-api-manager' ) : esc_attr( $time ) . esc_attr__( ' days', 'woocommerce-api-manager' );

		$debug_data[ 'wc_api_manager_download_url_expires' ] = array(
			'name'      => _x( 'Download URLs Expire', 'Download URLs Expire, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Download URLs Expire',
			'note'      => $expires_time,
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Hide Product Order API Keys?
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_hide_product_order_api_keys( &$debug_data ) {
		$hide_keys = get_option( 'woocommerce_api_manager_hide_product_order_api_keys' ) === 'yes';

		$debug_data[ 'wc_api_manager_hide_product_order_api_keys' ] = array(
			'name'    => _x( 'Hide Product Order API Keys?', 'Hide Product Order API Keys, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'   => 'Hide Product Order API Keys?',
			'note'    => $hide_keys ? esc_attr__( 'Yes', 'woocommerce-api-manager' ) : esc_attr__( 'No', 'woocommerce-api-manager' ),
			'success' => $hide_keys ? 1 : 0,
		);
	}

	/**
	 * Hide Master API Key?
	 *
	 * @since 2.6.14
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_hide_masterapi_key( &$debug_data ) {
		$hide_key = get_option( 'woocommerce_api_manager_hide_master_key' ) === 'yes';

		$debug_data[ 'wc_api_manager_hide_master_key' ] = array(
			'name'    => _x( 'Hide Master API Key?', 'Hide Master Key, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'   => 'Hide Master API Key?',
			'note'    => $hide_key ? esc_attr__( 'Yes', 'woocommerce-api-manager' ) : esc_attr__( 'No', 'woocommerce-api-manager' ),
			'success' => $hide_key ? 0 : 1,
		);
	}

	/**
	 * Secure Download URL Hashes Count.
	 *
	 * @since 2.6.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_grace_period( &$debug_data ) {
		$grace_period = get_option( 'woocommerce_api_manager_grace_period' );

		$debug_data[ 'wc_api_manager_grace_period' ] = array(
			'name'      => _x( 'Grace Period', 'Grace Period, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Grace Period',
			'note'      => esc_attr( $grace_period[ 'number' ] . ' ' . $grace_period[ 'unit' ] ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * API Key Activations.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_api_key_activations( &$debug_data ) {
		$debug_data[ 'wc_api_manager_api_key_activations' ] = array(
			'name'      => _x( 'API Key Activations', 'API Key Activations Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'API Key Activations',
			'note'      => esc_attr( WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_count() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * API Products Count.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_products_count( &$debug_data ) {
		$debug_data[ 'wc_api_manager_products_count' ] = array(
			'name'      => _x( 'API Products', 'API Products Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'API Products',
			'note'      => esc_attr( WC_AM_PRODUCT_DATA_STORE()->get_api_products_count() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * All API Resources.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_api_resources( &$debug_data ) {
		$debug_data[ 'wc_api_manager_api_resources' ] = array(
			'name'      => _x( 'All API Resources', 'All API Resources Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'All API Resources',
			'note'      => esc_attr( WC_AM_API_RESOURCE_DATA_STORE()->count_resources() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * WC Subscriptions API Resources.
	 *
	 * @since 2.6.8
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_wc_am_subs_api_resources( &$debug_data ) {
		$debug_data[ 'wc_api_manager_wc_am_subs_api_resources' ] = array(
			'name'      => _x( 'WC AM Subscription Resources', 'WC AM Subscription Resources Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'WC AM Subscription Resources',
			'note'      => esc_attr( WC_AM_API_RESOURCE_DATA_STORE()->count_non_sub_resources() ) . __( ' - (Grouped ', 'woocommerce-api-manager' ) . esc_attr( WC_AM_API_RESOURCE_DATA_STORE()->count_non_sub_resources( true ) ) . ')',
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * WC Subscriptions API Resources.
	 *
	 * @since 2.6.8
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_wc_subs_api_resources( &$debug_data ) {
		$debug_data[ 'wc_api_manager_wc_subs_api_resources' ] = array(
			'name'      => _x( 'WC Subscriptions Resources', 'WC Subscriptions Resources Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'WC Subscriptions Resources',
			'note'      => esc_attr( WC_AM_API_RESOURCE_DATA_STORE()->count_sub_resources() ) . __( ' - (Grouped ', 'woocommerce-api-manager' ) . esc_attr( WC_AM_API_RESOURCE_DATA_STORE()->count_sub_resources( true ) ) . ')',
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Associated API Keys.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_associated_api_keys( &$debug_data ) {
		$debug_data[ 'wc_api_manager_associated_api_keys' ] = array(
			'name'      => _x( 'Associated API Keys', 'Associated API Keys Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Associated API Keys',
			'note'      => esc_attr( WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->get_associated_api_key_count() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Secure Download URL Hashes Count.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_secure_hash_count( &$debug_data ) {
		$debug_data[ 'wc_api_manager_secure_hash_count' ] = array(
			'name'      => _x( 'Secure Download URL Hashes', 'Secure Download URL Hashes Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Secure Download URL Hashes',
			'note'      => esc_attr( WC_AM_PRODUCT_DATA_STORE()->get_secure_hash_count() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Grace Periods Count.
	 *
	 * @since 2.6.2
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_grace_period_count( &$debug_data ) {
		$debug_data[ 'wc_api_manager_grace_period_count' ] = array(
			'name'      => _x( 'Grace Periods', 'Grace Periods Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Grace Periods',
			'note'      => esc_attr( WC_AM_GRACE_PERIOD()->count() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Grace Periods Count.
	 *
	 * @since 2.7.1
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_legacy_product_id_count( &$debug_data ) {
		$debug_data[ 'wc_api_manager_legacy_product_id_count' ] = array(
			'name'      => _x( 'Legacy Product IDs', 'Legacy Product IDs Count, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Legacy Product IDs',
			'note'      => esc_attr( WC_AM_LEGACY_PRODUCT_ID()->count() ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * Next API Resource Cleanup Scheduled.
	 *
	 * @since 2.6.10
	 *
	 * @param $debug_data
	 */
	private function set_api_manager_next_api_resource_cleanup_scheduled( &$debug_data ) {
		$next_cleanup = WC_AM_BACKGROUND_EVENTS()->get_next_scheduled_cleanup();

		$debug_data[ 'wc_api_manager_next_api_resource_cleanup_scheduled' ] = array(
			'name'      => _x( 'Next API Resources Cleanup Scheduled', 'Next Resources Cleanup Scheduled, Label on WooCommerce -> System Status page', 'woocommerce-api-manager' ),
			'label'     => 'Next API Resources Cleanup Scheduled',
			'note'      => ( ! empty( $next_cleanup ) ) ? '<code>' . wc_clean( WC_AM_FORMAT()->unix_timestamp_to_date( $next_cleanup ) ) . '</code>' : __( 'Not scheduled.', 'woocommerce-api-manager' ),
			'mark'      => '',
			'mark_icon' => '',
		);
	}

	/**
	 * List WooCommerce API Manager template files that have been overridden.
	 *
	 * @since 2.1
	 *
	 * @param $debug_data
	 */
	private function set_theme_overrides( &$debug_data ) {
		$theme_overrides = $this->get_theme_overrides();

		if ( ! empty( $theme_overrides[ 'overrides' ] ) ) {
			$debug_data[ 'wc_am_theme_overrides' ] = array(
				'name'  => _x( 'WooCommerce API Manager Template Theme Overrides', 'label for the system status page', 'woocommerce-api-manager' ),
				'label' => 'WooCommerce API Manager Template Theme Overrides',
				'data'  => $theme_overrides[ 'overrides' ],
			);

			// Include a note on how to update if the templates are out of date.
			if ( ! empty( $theme_overrides[ 'has_outdated_templates' ] ) && true === $theme_overrides[ 'has_outdated_templates' ] ) {
				$debug_data[ 'wc_am_theme_overrides' ] += array(
					'mark_icon' => 'warning',
					'note'      => sprintf( __( '%1$sLearn how to update%2$s', 'woocommerce-api-manager' ), '<a href="https://docs.woocommerce.com/document/fix-outdated-templates-woocommerce/" target="_blank">', '</a>' ),
				);
			}
		}
	}

	/**
	 * Determine WooCommerce API Manager template files that have been overridden.
	 *
	 * @since 2.1
	 *
	 * @return array
	 */
	private function get_theme_overrides() {
		$wc_am_template_dir = dirname( WCAM()->get_plugin_file() ) . '/templates/';
		$wc_template_path   = trailingslashit( wc()->template_path() );
		$theme_root         = trailingslashit( get_theme_root() );
		$overridden         = array();
		$outdated           = false;
		$templates          = WC_Admin_Status::scan_template_files( $wc_am_template_dir );

		foreach ( $templates as $file ) {
			$theme_file = false;
			$locations  = array(
				get_stylesheet_directory() . "/{$file}",
				get_stylesheet_directory() . "/{$wc_template_path}{$file}",
				get_template_directory() . "/{$file}",
				get_template_directory() . "/{$wc_template_path}{$file}",
			);

			foreach ( $locations as $location ) {
				if ( is_readable( $location ) ) {
					$theme_file = $location;
					break;
				}
			}

			if ( ! empty( $theme_file ) ) {
				$core_version  = WC_Admin_Status::get_file_version( $wc_am_template_dir . $file );
				$theme_version = WC_Admin_Status::get_file_version( $theme_file );

				$overridden_template_output = sprintf( '<br><code>%s</code>', esc_html( str_replace( $theme_root, '', $theme_file ) ) );

				if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
					$outdated                   = true;
					$overridden_template_output .= sprintf( /* translators: %1$s is the file version, %2$s is the core version */ esc_html__( ' version %1$s is out of date. The core version is %2$s', 'woocommerce-api-manager' ), '<strong style="color:red">' . esc_html( $theme_version ) . '</strong>', '<strong>' . esc_html( $core_version ) . '</strong>' );
				}

				$overridden[ 'overrides' ][] = $overridden_template_output;
			}
		}

		$overridden[ 'has_outdated_templates' ] = $outdated;

		return $overridden;
	}

} // end of class