<?php

/**
 * woocommerce_gpf_structured_data
 *
 * Enriches the on-page microdata based on Google Product Feed data values.
 */
class WoocommerceGpfStructuredData {

	/**
	 * @var WoocommerceProductFeedsFeedItemFactory
	 */
	protected $feed_item_factory;

	/**
	 * Constructor.
	 *
	 * Store dependencies.
	 *
	 * @param WoocommerceProductFeedsFeedItemFactory $feed_item_factory
	 */
	public function __construct( WoocommerceProductFeedsFeedItemFactory $feed_item_factory ) {
		$this->feed_item_factory = $feed_item_factory;
	}

	/**
	 *    Add the filters so we can modify the structured data.
	 */
	public function initialise() {
		// Add hook to modify JSON+LD data.
		add_filter(
			'woocommerce_structured_data_product',
			array( $this, 'structured_data_product' ),
			8,
			2
		);
		add_filter(
			'woocommerce_structured_data_product_offer',
			array( $this, 'structured_data_product_offer' ),
			10,
			2
		);
	}

	/**
	 * Filter the structured data array created by WooCommerce.
	 *
	 * @param array $markup The array representing the JSON+LD as
	 *                              generated by WooCommerce
	 * @param WC_Product $product The product being output.
	 *
	 * @return array                The modified array.
	 */
	public function structured_data_product( $markup, $product ) {

		if ( $product instanceof WC_Product_Simple ||
			$product instanceof WC_Product_Bundle ) {
			return $this->structured_data_simple_product( $markup, $product );
		}
		if ( $product instanceof WC_Product_Variable ) {
			return $this->structured_data_variable_product( $markup, $product );
		}

		return $markup;
	}

	/**
	 * Filter the structured data array for offers created by WooCommerce.
	 *
	 * We only interfere for in offers for variable products and variations.
	 *
	 * @param array $markup The array representing the JSON+LD as
	 *                                    generated by WooCommerce
	 * @param WC_Product $offer_product The specific product being listed as
	 *                                    an offer.
	 *
	 * @return array                The modified array.
	 */
	public function structured_data_product_offer( $markup, $offer_product ) {

		if ( ! $offer_product instanceof WC_Product_Variable &&
			! $offer_product instanceof WC_Product_Variation ) {
			return $markup;
		}
		$parent_product = $offer_product;
		if ( $offer_product instanceof WC_Product_Variation ) {
			$parent_product = wc_get_product( $offer_product->get_parent_id() );
		}
		// Get the feed information for this product.
		// Note: We do not calculate pricing to avoid having to query all variations.
		$feed_item = $this->feed_item_factory->create( 'google', $offer_product, $parent_product, false );

		// SKU.
		if ( ! empty( $feed_item->sku ) ) {
			$markup['sku'] = $feed_item->sku;
		} else {
			$markup['sku'] = $feed_item->guid;
		}

		// Condition.
		if ( isset( $feed_item->additional_elements['condition'][0] ) ) {
			$markup['itemCondition'] = $this->schemaize_condition( $feed_item->additional_elements['condition'][0] );
		}

		// GTIN.
		if ( isset( $feed_item->additional_elements['gtin'][0] ) ) {
			$gtin_length = strlen( $feed_item->additional_elements['gtin'][0] );
			$key         = 'gtin' . $gtin_length;
			switch ( $gtin_length ) {
				case 8:
				case 12:
				case 13:
				case 14:
					$markup[ $key ] = $feed_item->additional_elements['gtin'][0];
					break;
			}
		}

		// MPN.
		if ( isset( $feed_item->additional_elements['mpn'] ) ) {
			$markup['mpn'] = $feed_item->additional_elements['mpn'][0];
		}

		return $markup;
	}

	/**
	 * Filter the data array created by WooCommerce for a variable product.
	 *
	 * @param array $markup The array representing the JSON+LD as
	 *                              generated by WooCommerce
	 * @param WC_Product $product The product being output.
	 *
	 * @return array                The modified array.
	 */
	private function structured_data_variable_product( $markup, $product ) {

		// Get the feed information for this product.
		// Note: We do not calculate pricing to avoid having to query all variations.
		$feed_item = $this->feed_item_factory->create( 'google', $product, $product, false );

		// Brand.
		if ( isset( $feed_item->additional_elements['brand'] ) ) {
			$markup['brand'] = $this->generate_brand_markup( $feed_item->additional_elements['brand'][0] );
		}

		return $markup;
	}

	/**
	 * Filter the data array created by WooCommerce for a simple product.
	 *
	 * @param array $markup The array representing the JSON+LD as
	 *                              generated by WooCommerce
	 * @param WC_Product $product The product being output.
	 *
	 * @return array                The modified array.
	 */
	private function structured_data_simple_product( $markup, $product ) {
		// Get the feed information for this product.
		// Note: We do not calculate pricing to avoid having to query all variations.
		$feed_item = $this->feed_item_factory->create( 'google', $product, $product, false );

		// SKU.
		if ( ! empty( $feed_item->sku ) ) {
			$markup['sku'] = $feed_item->sku;
		}

		// Condition.
		if ( isset( $feed_item->additional_elements['condition'] ) ) {
			$markup['itemCondition'] = $this->schemaize_condition( $feed_item->additional_elements['condition'][0] );
		}

		// GTIN.
		if ( isset( $feed_item->additional_elements['gtin'][0] ) ) {
			$raw_gtin    = str_replace( '-', '', str_replace( ' ', '', $feed_item->additional_elements['gtin'][0] ) );
			$gtin_length = strlen( $raw_gtin );
			switch ( $gtin_length ) {
				case 8:
				case 12:
				case 13:
				case 14:
					$key            = 'gtin' . $gtin_length;
					$markup[ $key ] = $feed_item->additional_elements['gtin'][0];
					break;
			}
		}

		// MPN.
		if ( isset( $feed_item->additional_elements['mpn'] ) ) {
			$markup['mpn'] = $feed_item->additional_elements['mpn'][0];
		}

		// Brand.
		if ( isset( $feed_item->additional_elements['brand'] ) ) {
			$markup['brand'] = $this->generate_brand_markup( $feed_item->additional_elements['brand'][0] );
		}

		// Colour.
		if ( isset( $feed_item->additional_elements['color'] ) ) {
			$markup['color'] = $feed_item->additional_elements['color'][0];
		}

		return $markup;
	}

	private function schemaize_condition( $condition ) {
		switch ( strtolower( $condition ) ) {
			case 'new':
				return 'https://schema.org/NewCondition';
				break;
			case 'used':
				return 'https://schema.org/UsedCondition';
				break;
			case 'refurbished':
				return 'https://schema.org/RefurbishedCondition';
				break;
		}

		return $condition;
	}

	private function generate_brand_markup( $brand ) {
		return apply_filters(
			'woocommerce_gpf_structured_data_brand',
			[
				'@type' => 'Brand',
				'name'  => $brand,
			],
			$brand
		);
	}
}
