<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Product Data Store Class
 *
 * @since       2.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Product Data Store
 */
class WC_AM_Product_Data_Store {

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return null|\WC_AM_Product_Data_Store
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {}

	/**
	 * Return the product object.
	 *
	 * @since 2.0
	 *
	 * @param int|mixed $product WC_Product or order ID.
	 *
	 * @return false|null|\WC_Product
	 */
	public function get_product_object( $product ) {
		return is_object( $product ) ? $product : wc_get_product( $product );
	}

	/**
	 * Get product metadata.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int|WC_Product $product
	 * @param string         $meta_key
	 * @param bool           $single
	 *
	 * @return bool|mixed
	 */
	public function get_meta( $product, $meta_key = '', $single = true ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			if ( $single ) {
				/**
				 * @usage returns a single value for a single key. A single value for the single order.
				 * echo WC_AM_ORDER_DATA_STORE()->get_meta( $order_id, '_api_new_version' );
				 */
				return $product->get_meta( $meta_key, $single );
			} else {
				/**
				 * @usage returns multiple values if there are multiple keys. One value for each order.
				 * $o = WC_AM_ORDER_DATA_STORE()->get_meta( $order_id, '_api_new_version', false );
				 * echo $o['_api_new_version'];
				 */
				return WC_AM_ARRAY()->flatten_meta_object( $product->get_meta( $meta_key, $single ) );
			}
		}

		return false;
	}

	/**
	 * Get all product meta data.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int|WC_Product $product
	 *
	 * @return array|bool
	 */
	public function get_meta_data( $product ) {
		$product = $this->get_product_object( $product );

		return is_object( $product ) ? $product->get_meta_data() : false;
	}

	/**
	 * Return array of flattened metadata.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return array
	 */
	public function get_meta_flattened( $product ) {
		return WC_AM_ARRAY()->flatten_meta_object( $this->get_meta_data( $product ) );
	}

	/**
	 * Returns product type, i.e. simple, variable, etc.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return string|bool
	 */
	public function get_type( $product ) {
		$product = $this->get_product_object( $product );

		return is_object( $product ) ? $product->get_type() : false;
	}

	/**
	 * Returns a list of product objects.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param array $args
	 *
	 * @return array|\stdClass
	 */
	public function get_products( $args = array() ) {
		return wc_get_products( $args );
	}

	/**
	 * Return an array of download data for a product.
	 * Current data as of WC 3.2 includes: [id], [name], [file], [previous_hash]
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return array|bool
	 */
	public function get_downloads( $product ) {
		$product = $this->get_product_object( $product );

		return is_object( $product ) ? $product->get_downloads() : false;
	}

	/**
	 * Returns the number of downloads of a prodduct.
	 *
	 * @since 2.0
	 *
	 * @param int    $order_id
	 * @param string $order_key
	 *
	 * @return bool|int
	 */
	public function get_download_count( $order_id, $order_key ) {
		global $wpdb;

		$result = $wpdb->get_row( $wpdb->prepare( "
			SELECT download_count
			FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
			WHERE order_id = %d
			AND order_key = %s
			LIMIT 1
		", $order_id, $order_key ) );

		if ( is_object( $result ) ) {
			return $result->download_count;
		}

		return false;
	}

	/**
	 * Return only the first/latest download URL for a product download.
	 *
	 * @see     get_all_download_urls() to allow more download URLs.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int|WC_Product $product
	 *
	 * @return string|bool
	 */
	public function get_first_download_url( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			$downloads = $this->get_downloads( $product );

			if ( is_array( $downloads ) && ! WC_AM_FORMAT()->empty( $downloads ) ) {
				foreach ( $downloads as $download => $value ) {
					// return only the latest/first download URL.
					return $value[ 'file' ];
				}
			}
		}

		return false;
	}

	/**
	 * Return all download URLs for all product downloads.
	 *
	 * @since   2.5.7
	 *
	 * @param int|WC_Product $product
	 *
	 * @return string|bool
	 */
	public function get_all_download_urls( $product ) {
		$product = $this->get_product_object( $product );
		$urls    = array();

		if ( is_object( $product ) ) {
			$downloads = $this->get_downloads( $product );

			if ( is_array( $downloads ) && ! WC_AM_FORMAT()->empty( $downloads ) ) {
				foreach ( $downloads as $download => $value ) {
					$urls[] = $value[ 'file' ];
				}
			}
		}

		return $urls;
	}

	/**
	 * Return download ID for a product download.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int|WC_Product $product
	 *
	 * @return bool|mixed
	 */
	public function get_download_id( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			$downloads = $this->get_downloads( $product );

			if ( is_array( $downloads ) && ! WC_AM_FORMAT()->empty( $downloads ) ) {
				foreach ( $downloads as $download => $value ) {
					// return only the latest/first download id.
					return $value[ 'id' ];
				}
			}
		}

		return false;
	}

	/**
	 * Returns an array with the product_id, and variation_id (if there is one).
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return array
	 */
	public function get_product_ids( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			return array(
				'product_id'   => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
				'variation_id' => $product->is_type( 'variation' ) ? $product->get_id() : 0,
			);
		}

		return array();
	}

	/**
	 * Return parent  product ID.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product|WC_Order_Item $product
	 *
	 * @return bool|int
	 */
	public function get_parent_product_id( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			return $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		}

		return false;
	}

	/**
	 * Return the parent_id using any product_id.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return bool|null|string
	 */
	public function get_parent_id_from_product_id( $product_id ) {
		global $wpdb;

		$parent_id = $this->get_parent_product_id( $product_id );

		if ( $parent_id === false ) {
			$sql = "
                SELECT post_parent
                FROM {$wpdb->prefix}posts
                WHERE ID = %d
                AND post_type = %s
            ";

			// If the product variation lists the $product_id in the post_parent column, then $product_id is the parent product_id.
			$parent_id = $wpdb->get_var( $wpdb->prepare( $sql, $product_id, 'product_variation' ) );

			if ( ! empty( $parent_id ) ) {
				return $parent_id;
			} else {
				$sql = "
                SELECT ID
                FROM {$wpdb->prefix}posts
                WHERE ID = %d
                AND post_type = %s
                AND post_parent = %d
            ";

				// This is a simple product.
				$parent_id = $wpdb->get_var( $wpdb->prepare( $sql, $product_id, 'product', 0 ) );

				// This could be a grouped parent product.
				if ( empty( $parent_id ) ) {
					$sql = "
	                SELECT ID
	                FROM {$wpdb->prefix}posts
	                WHERE ID = %d
	                AND post_type = %s
	            ";

					$parent_id = $wpdb->get_var( $wpdb->prepare( $sql, $product_id, 'product' ) );

					// Check if the parent product is a grouped product parent.
					if ( $this->is_product_grouped( $parent_id ) ) {
						/**
						 * A grouped product parent is not considered a parent for this purpose, since it is not required to be flagged as an API product,
						 * so the grouped child product will be checked instead, since it can be any type of product that is only linked to the
						 * grouped parent product, and yet the child is also a standalone product.
						 */
						return $product_id;
					}
				}
			}
		}

		return ! empty( $parent_id ) && $parent_id == $product_id ? $product_id : false;
	}

	/**
	 * Return parent or variable product ID.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return bool|int
	 */
	public function get_product_id( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			$product_id = ! empty( $product->get_product_id() ) ? $product->get_product_id() : $product->get_id();

			return ! empty( $product->get_variation_id() ) ? $product->get_variation_id() : $product_id;
		}

		return false;
	}

	/**
	 * Returns the product type, i.e. simple.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return string|false
	 */
	public function get_product_type( $product ) {
		$product = $this->get_product_object( $product );

		return is_object( $product ) ? $product->get_type() : false;
	}

	/**
	 * Returns a boolean to indicate if the '_downloadable' checkbox is checked or not.
	 *
	 * @since 2.0
	 *
	 * @param int $product
	 *
	 * @return bool
	 */
	public function get_downloadable( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			return $product->exists() && $product->get_downloadable();
		}

		return false;
	}

	/**
	 * Return the number of API activations for a product.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return int
	 */
	public function get_api_activations( $product_id ) {
		$unlimited_activations = WC_AM_PRODUCT_DATA_STORE()->is_api_product_unlimited_activations( $product_id ); // since 2.2
		$activations           = $unlimited_activations ? WCAM()->get_unlimited_activation_limit() : $this->get_meta( $product_id, '_api_activations' );

		return ! WC_AM_FORMAT()->empty( $activations ) ? (int) $activations : 0;
	}

	/**
	 * Return the API access expires value for a product.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return int
	 */
	public function get_api_access_expires( $product_id ) {
		$access_expires = $this->get_meta( $product_id, '_access_expires' );

		return ! WC_AM_FORMAT()->empty( $access_expires ) ? (int) $access_expires : 0;
	}

	/**
	 * Return the legacy software title that was used to find the correct product ID.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return bool|mixed
	 */
	public function get_product_legacy_api_software_title( $product_id ) {
		$title = $this->get_meta( $product_id, '_api_resource_title' );

		return ! WC_AM_FORMAT()->empty( $title ) ? $title : false;
	}

	/**
	 * Return the total number of API Products.
	 *
	 * @since 2.1
	 *
	 * @return int
	 */
	public function get_api_products_count() {
		global $wpdb;

		$api_products_count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(post_id)
			FROM {$wpdb->postmeta}
			WHERE meta_key = %s
			AND meta_value = %s
		", '_is_api', 'yes' ) );

		return ! WC_AM_FORMAT()->empty( $api_products_count ) ? $api_products_count : 0;
	}

	/**
	 * Return total number of secure hashes used to secure download URLs.
	 *
	 * @since 2.1
	 *
	 * @return int|string|null
	 */
	public function get_secure_hash_count() {
		global $wpdb;

		$secure_hash_count = $wpdb->get_var( "
			SELECT COUNT(hash_id)
			FROM {$wpdb->prefix}" . WC_AM_USER()->get_secure_hash_table_name() . "
		" );

		return ! WC_AM_FORMAT()->empty( $secure_hash_count ) ? $secure_hash_count : 0;
	}

	/**
	 *
	 *
	 * @since 2.0
	 *
	 * @param object $data
	 *
	 * @return array
	 */
	public function flatten_get_meta( $data ) {
		$array = array();

		if ( ! WC_AM_FORMAT()->empty( $data ) ) {
			foreach ( (array) $data as $key => $value ) {
				// Skip empty meta values.
				if ( ! WC_AM_FORMAT()->empty( $value->value ) ) {
					$array[ $value->key ] = $value->value;
				}
			}
		}

		return $array;
	}

	/**
	 * Update product metadata.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int|WC_Product $product
	 * @param string         $meta_key
	 * @param mixed          $meta_value
	 */
	public function update_meta( $product, $meta_key, $meta_value ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			$product->update_meta_data( $meta_key, $meta_value );
			$product->save_meta_data();
		}
	}

	/**
	 * Replace a missing _api_resource_product_id meta value, if the product is an API product.
	 *
	 * @since 2.0.13
	 *
	 * @param int $product_id
	 * @param int $parent_id
	 */
	public function update_missing_api_resource_product_id( $product_id, $parent_id = 0 ) {
		$has_api_resource_product_id = $this->has_api_resource_product_id( $product_id );

		if ( WC_AM_FORMAT()->empty( $has_api_resource_product_id ) ) {
			if ( ! WC_AM_FORMAT()->empty( $parent_id ) ) {
				$is_api = $this->get_meta( $parent_id, '_is_api' );

				if ( ! WC_AM_FORMAT()->empty( $is_api ) && $is_api == 'yes' ) {
					$this->update_meta( $product_id, '_api_resource_product_id', $product_id );
				}
			} elseif ( WC_AM_FORMAT()->empty( $this->is_api_product( $product_id ) ) ) {
				$this->update_meta( $product_id, '_api_resource_product_id', $product_id );
			}
		} elseif ( ! WC_AM_FORMAT()->empty( $parent_id ) && WC_AM_PRODUCT_DATA_STORE()->get_meta( $product_id, '_api_resource_product_id' ) != $parent_id ) {
			/*
			 * If Parent Product ID does not match the Product _api_resource_product_id, then update _api_resource_product_id. Issue occurs when duplicating/cloning a product.
			 * Update is done automatically when product edit screen is displayed.
			 */
			$is_api = $this->get_meta( $parent_id, '_is_api' );

			if ( ! WC_AM_FORMAT()->empty( $is_api ) && $is_api == 'yes' ) {
				$this->update_meta( $product_id, '_api_resource_product_id', $product_id );
			}
		}
	}

	/**
	 * Delete product metadata.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int|WC_Product $product
	 * @param string         $meta_key
	 */
	public function delete_meta( $product, $meta_key ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			$product->delete_meta_data( $meta_key );
		}
	}

	/**
	 * Returns true if product type is grouped.
	 *
	 * @since 2.0
	 *
	 * @param int $product
	 *
	 * @return bool
	 */
	public function is_product_grouped( $product ) {
		return $this->get_product_type( $product ) == 'grouped';
	}

	/**
	 * Returns true if product type is external.
	 *
	 * @since 2.0
	 *
	 * @param int $product
	 *
	 * @return bool
	 */
	public function is_product_external( $product ) {
		return $this->get_product_type( $product ) == 'external';
	}

	/**
	 * Returns true if is a parent product.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function is_parent_product( $product_id ) {
		global $wpdb;

		$sql = "
                SELECT ID
                FROM {$wpdb->prefix}posts
                WHERE post_type = %s
                AND post_parent = %d
            ";

		// If the product variation lists the $product_id in the post_parent column, then this is a parent product.
		$variation_id = $wpdb->get_var( $wpdb->prepare( $sql, 'product_variation', $product_id ) );

		if ( ! WC_AM_FORMAT()->empty( $variation_id ) ) {
			return true;
		} else {
			$sql = "
                SELECT ID
                FROM {$wpdb->prefix}posts
                WHERE post_type = %s
                AND post_parent = %d
            ";

			$parent_id = $wpdb->get_var( $wpdb->prepare( $sql, 'product', 0 ) );

			return ! WC_AM_FORMAT()->empty( $parent_id ) && $parent_id == $product_id;
		}
	}

	/**
	 * Returns true if is a parent product with variations/children.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function is_parent_product_with_variations( $product_id ) {
		global $wpdb;

		$sql = "
                SELECT ID
                FROM {$wpdb->prefix}posts
                WHERE post_type = %s
                AND post_parent = %d
            ";

		// If the product variation lists the $product_id in the post_parent column, then this is a parent product.
		$variation_id = $wpdb->get_var( $wpdb->prepare( $sql, 'product_variation', $product_id ) );

		return ! WC_AM_FORMAT()->empty( $variation_id );
	}

	/**
	 * Return true if product API checkbox is checked.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id Requires a parent product ID of a single or variable product.
	 *
	 * @return bool
	 */
	public function is_api_product( $product_id ) {
		$is_api = $this->get_meta( $product_id, '_is_api' );

		if ( WC_AM_FORMAT()->empty( $is_api ) ) {
			$parent_id = $this->get_parent_product_id( $product_id );
			$is_api    = $this->get_meta( $parent_id, '_is_api' );
		}

		return ! WC_AM_FORMAT()->empty( $is_api ) && $is_api == 'yes';
	}

	/**
	 * Return true if product Activations Unlimited checkbox is checked.
	 *
	 * @since 2.2.0
	 *
	 * @param int $product_id Requires a parent product ID of a single or variable product.
	 *
	 * @return bool
	 */
	public function is_api_product_unlimited_activations( $product_id ) {
		$is_unlimited = $this->get_meta( $product_id, '_api_activations_unlimited' );

		return ! WC_AM_FORMAT()->empty( $is_unlimited ) && $is_unlimited == 'yes';
	}

	/**
	 * Return true if product subscription required checkbox is checked.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return bool
	 */
	public function is_api_subscription_required_product( $product ) {
		return ! WC_AM_FORMAT()->empty( $this->get_meta( $product, '_api_is_subscription' ) ) == 'yes';
	}

	/**
	 * Return true if the product can be downloaded.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return bool
	 */
	public function is_downloadable_product( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			return $product->exists() && $product->is_downloadable() && $product->has_file();
		}

		return false;
	}

	/**
	 * Return true if the product has downloable product permission.
	 *
	 * @deprecated since 2.0
	 *
	 * @since      2.0
	 *
	 * @param string $order_key
	 * @param int    $product_id
	 *
	 * @return bool
	 */
	public function has_download_permission( $order_key, $product_id ) {
		global $wpdb;

		$sql = "
			SELECT *
			FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
			WHERE order_key = %s
			AND product_id = %s
		";

		$args = array(
			$order_key,
			(int) $product_id
		);

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $args ) );

		return ! WC_AM_FORMAT()->empty( $result );
	}

	/**
	 * Return true if the product can be downloaded.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return bool
	 */
	public function has_download( $product ) {
		$product = $this->get_product_object( $product );

		if ( $product ) {
			return $product->exists() && $product->has_file();
		}

		return false;
	}

	/**
	 * Return true if the product has an API Resource Product ID set.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function has_api_resource_product_id( $product_id ) {
		$product_id = $this->get_meta( $product_id, '_api_resource_product_id' );

		return ! WC_AM_FORMAT()->empty( $product_id );
	}

	/**
	 * Returns true if the product exists and is not in the trash.
	 *
	 * @since 2.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function has_valid_product_status( $product_id ) {
		return $this->product_exists( $product_id ) && $this->product_is_active( $product_id );
	}

	/**
	 * Remove API product downlads from the My Account Downloads section, so they are only displayed on the API Downloads section.
	 *
	 * @since 1.4.6.1
	 *
	 * @param $downloads
	 *
	 * @return mixed
	 */
	public function filter_get_downloadable_products( $downloads ) {
		foreach ( $downloads as $key => $download ) {
			if ( $this->is_api_product( WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_parent_id( $download[ 'product_id' ] ) ) ) {
				unset( $downloads[ $key ] );
			}
		}

		return $downloads;
	}

	/**
	 * Remove API product downlads from emails, and Order Details, in the My Account dashboard.
	 *
	 * @since 1.4.6.1
	 *
	 * @param $files
	 * @param $item
	 *
	 * @return array
	 */
	public function filter_get_item_downloads( $files, $item ) {
		$product_id = ! WC_AM_FORMAT()->empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ];

		return $this->is_api_product( WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_parent_id( $product_id ) ) ? array() : $files;
	}

	/**
	 * Verifies a product exists.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function product_exists( $product_id ) {
		$product = $this->get_product_object( $product_id );

		return is_object( $product ) && $product->exists();
	}

	/**
	 * Check if a product exists and is not in the trash
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function product_is_active( $product_id ) {
		$product = $this->get_product_object( $product_id );

		return is_object( $product ) && $product->get_status() !== 'trash';
	}

	/**
	 * Return true if the product requires a download.
	 *
	 * @since 2.0
	 *
	 * @param int|WC_Product $product
	 *
	 * @return bool
	 */
	public function requires_download( $product ) {
		$product = $this->get_product_object( $product );

		if ( $product ) {
			return $product->exists() && $product->is_downloadable();
		}

		return false;
	}

	/**
	 * Clear caches.
	 *
	 * @since 2.3.8
	 *
	 * @param int|WC_Product $product
	 */
	public function clear_caches( $product ) {
		$product = $this->get_product_object( $product );

		if ( is_object( $product ) ) {
			wc_delete_product_transients( $product->get_id() );

			if ( $product->get_parent_id( 'edit' ) ) {
				wc_delete_product_transients( $product->get_parent_id( 'edit' ) );
				WC_Cache_Helper::invalidate_cache_group( 'product_' . $product->get_parent_id( 'edit' ) );
			}

			WC_Cache_Helper::invalidate_attribute_count( array_keys( $product->get_attributes() ) );
			WC_Cache_Helper::invalidate_cache_group( 'product_' . $product->get_id() );
		}
	}
}