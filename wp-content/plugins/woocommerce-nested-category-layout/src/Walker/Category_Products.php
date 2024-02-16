<?php
/**
 * WooCommerce Nested Category Layout.
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Nested Category Layout to newer
 * versions in the future. If you wish to customize WooCommerce Nested Category Layout for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-nested-category-layout/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Nested_Category_Layout\Walker;

defined( 'ABSPATH' ) or exit;

use WP_Term;

/**
 * Category walker for products.
 *
 * A category walker which performs a traversal through the categories tree, skipping the root category (which is the category of the current page), and rendering any non-empty category names as headings, and any products within them.
 *
 * A non-empty category is defined as a category which contains products, or has any descendants that contain products.
 *
 * Products can belong to multiple categories and are therefore rendered only at the deepest category level.
 * This means that if a product belongs to sibling categories at its deepest level it can appear more than once.
 *
 * Originally introduced in v1.0 but namespaced & renamed in v1.14.2 to avoid class collisions.
 *
 * @since 1.14.2
 */
class Category_Products extends \Walker {


	/** @var string taxonomy type being handled */
	public $tree_type = 'product_cat';

	/** @var array DB fields used */
	public $db_fields = [ 'parent' => 'parent', 'id' => 'term_id' ];

	/** @var array<int, int> associative array of category id's to category depth */
	protected array $category_depths = [];

	/** @var array<int, int[]> associative array of product id's to an array of deepest parent category IDs */
	protected array $product_category_ids = [];

	/** @var bool flag to know whether the class has been initialized already */
	private bool $initialized = false;


	/**
	 * Constructor.
	 *
	 * @since 1.14.2
	 *
	 * @param array|mixed $category_depths
	 */
	public function __construct( $category_depths ) {

		$this->category_depths = (array) $category_depths;
	}


	/**
	 * Called after a category has been rendered.
	 *
	 * @see \Walker::end_el()
	 *
	 * @since 1.14.2
	 *
	 * @param string|array $output Passed by reference. Used to append additional content.
	 * @param \WP_Term $category The category.
	 * @param int $depth Level depth of the category.
	 * @param array $args Additional args.
	 */
	public function end_el( &$output, $category, $depth = 0, $args = [] ) {

		if ( is_array( $output ) ) {
			array_pop( $output );
		}

		if ( empty( $output ) ) {
			$output = '';
		}
	}


	/**
	 * Starts the list before the elements are added.
	 *
	 * The 'proper' way to use this callback is to update the $output memo with
	 * the output to be displayed. Since we're not interested in making a hierarchical
	 * list, instead we're doing a much simpler "flat" list, and it's more convenient
	 * to echo all output.
	 *
	 * @see \Walker::start_el()
	 *
	 * @since 1.14.2
	 *
	 * @param array $output Passed by reference. Used to remember categories that might need their title displayed.
	 * @param \WP_Term $category Category data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected', 'show_count', and 'show_last_update' keys, if they exist.
	 * @param int $current_object_id Current object ID.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = [], $current_object_id = 0 ) {
		global $woocommerce_loop;

		if ( ! $this->initialized ) {

			// first category, so setup and initialize things
			$this->initialized = true;

			$this->set_product_category_ids( $args );
		}

		if ( ! is_array( $output ) ) {
			$output = [];
		}

		/**
		 * Filters whether the current category has products.
		 *
		 * @since 1.14.2
		 *
		 * @param bool $has_products
		 * @param Category_Products $walker
		 */
		$has_products = (bool) apply_filters( 'wc_nested_category_layout_has_products', false, $this );

		if ( ! $has_products ) {
			$has_products = $category->count > 0;
		}

		// keep track of the current category in case a child category has products, and we need to display titles
		$category->depth = $depth;

		$output[] = $category;

		if ( $has_products && $this->should_display_category( $category ) ) {

			if ( wc_nested_category_layout()->is_layered_nav_active() && ! $this->recursive_array_match( $category->term_id, $this->product_category_ids ) ) {
				return;
			}

			// tell the template to display a "See More" link
			$woocommerce_loop['see_more'] = true;

			// Record the fact that categories have been displayed.
			// Do this so we can alter the query to skip any products that would otherwise be displayed.
			// This is done so that the number of products to be displayed can be set to '0' to have nested categories only.
			$woocommerce_loop['has_categories'] = true;

			// display nested category title(s) and product(s)
			woocommerce_nested_category_products_content_section( $output, $this->product_category_ids );
		}
	}


	/**
	 * Determines whether a value is in an array, or in any of its sub-arrays.
	 *
	 * @since 1.20.0
	 *
	 * @param mixed $needle
	 * @param array $haystack
	 * @return bool
	 */
	private function recursive_array_match( $needle, array $haystack ) : bool {

		foreach ( $haystack as $value ) {
			if ( $needle === $value || ( is_array( $value ) && $this->recursive_array_match( $needle, $value ) !== false ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Determines whether a category should be displayed.
	 *
	 * @since 1.20.0
	 *
	 * @param \WP_Term|mixed $category
	 * @return bool
	 */
	protected function should_display_category( $category ) : bool  {

		$is_product_cat = $category instanceof WP_Term && 'product_cat' === $category->taxonomy;
		$has_products   = $is_product_cat && ! empty( $this->get_category_product_ids( $category ) );

		/**
		 * Filters whether the current category should be displayed.
		 *
		 * By default, it will not display categories that have no products directly associated with them or their children.
		 *
		 * @since 1.20.0
		 *
		 * @param bool $has_products_or_subcategories
		 * @param WP_Term $category
		 * @param Category_Products $walker
		 */
		return (bool) apply_filters( 'wc_nested_category_layout_should_display_category', $has_products, $category, $this );
	}


	/**
	 * Gets the product IDs for a category or its descendants.
	 *
	 * @since 1.20.0
	 *
	 * @param WP_Term $category
	 * @return int[]
	 */
	private function get_category_product_ids( WP_Term $category ) : array {

		return get_posts( [
			'post_type'      => 'product',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => [
				[
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $category->term_id,
					'include_children' => true,
				],
			],
		] );
	}


	/**
	 * Helper function to determine all the deepest categories that the current page products belong to.
	 *
	 * @since 1.14.2
	 *
	 * @global array $woocommerce_loop associative-array used by the template files
	 * @param array $args associative array of arguments passed to the Walker
	 */
	private function set_product_category_ids( $args ) {

		// figure out the deepest categories for all the products, once
		rewind_posts();

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				global $product;

				if ( ! $product ) {
					continue;
				}

				$this->product_category_ids[ $product->get_id() ] = [];

				// first get all the root product categories
				$root_categories = get_categories( [ 'parent' => 0, 'taxonomy' => 'product_cat' ] );

				// then get all the categories the current product belongs to
				$product_category_ids = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'ids' ] );

				// determine the deepest (closest to leaf) category level for this product
				$deepest = [];

				foreach ( $root_categories as $root_cat ) {
					$root_cat->children = [];
				}

				$category_to_root = [];

				foreach ( $product_category_ids as $category_id ) {

					// determine whether this category *is* a root category, or otherwise what its root category is
					foreach ( $root_categories as $root_cat ) {

						if ( $root_cat->cat_ID == $category_id || term_is_ancestor_of( $root_cat->cat_ID, $category_id, 'product_cat' ) ) {

							$category_to_root[ $category_id ] = $root_cat->cat_ID;
							break;
						}
					}

					if ( empty( $root_cat ) ) {
						continue;
					}

					if ( isset( $deepest[ $root_cat->cat_ID ] ) ) {
						$deepest[ $root_cat->cat_ID ] = max( $deepest[ $root_cat->cat_ID ], $this->category_depths[ $category_id ] ?? -2 );
					} else {
						$deepest[ $root_cat->cat_ID ] = $this->category_depths[ $category_id ] ?? -2;
					}
				}

				// collect only the deepest categories
				foreach ( $product_category_ids as $category_id ) {

					if ( isset( $this->category_depths[ $category_id ] ) && $this->category_depths[ $category_id ] == $deepest[ $category_to_root[ $category_id ] ] ) {

						$this->product_category_ids[ $product->get_id() ][] = $category_id;
					}
				}

				// if this is empty it means the product is only in the current category, so add it
				if ( empty( $this->product_category_ids[ $product->get_id() ] ) && isset( $args['current_category'] ) ) {

					$this->product_category_ids[ $product->get_id() ][] = $args['current_category'];
				}
			}
		}
	}


}
