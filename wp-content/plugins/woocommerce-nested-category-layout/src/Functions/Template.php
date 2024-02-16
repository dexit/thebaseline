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

/*
 * Template Function Overrides
 *
 * @since 1.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! function_exists( 'woocommerce_nested_category_products_content_section' ) ) {


	/**
	 * Outputs HTML for each product-containing nested category sections on the modified shop pages.
	 *
	 * @see \SkyVerge\WooCommerce\Nested_Category_Layout\Walker\Category_Products::start_el()
	 *
	 * @since 1.0
	 *
	 * @param \WP_Term[] $categories array of category objects
	 * @param array<int, int[]> $product_category_ids associative array of product id to array of deepest categories the products belong to
	 * @return void HTML
	 */
	function woocommerce_nested_category_products_content_section( array $categories, array $product_category_ids = [] ) {
		global $wp_query;

		$title               = '';
		$current_category    = $product_category = null;
		$is_product_category = $wp_query && is_product_category();

		// build up the sub-category title, starting with the title of the current page category
		if ( $is_product_category ) {

			// using the queried object name addresses a case where a filter is applied by third parties and the taxonomy query var is no longer a product category
			$object           = get_queried_object();
			$current_category = $object && isset( $object->term_id ) ? get_term_by( 'id', $object->term_id, $object->taxonomy ?: 'product_cat' ) : null;
			$title            = $object && ! empty( $object->name ) ? '<span>' . wptexturize( $object->name ) . '</span>' : '';
		}

		// add any saved up category titles, along with the current
		foreach ( $categories as $product_category ) {

			$title .= sprintf(
				'%1$s<a href="%2$s">%3$s</a>',
				! empty( $title ) ? ' - ' : '',
				esc_attr( get_term_link( $product_category ) ),
				wptexturize( $product_category->name )
			);

			// set the title empty if it's the main product category query and categories to iterate are the same for the category page
			if ( ( $current_category && $current_category->term_id === $product_category->term_id && $is_product_category ) || ( $wp_query && is_product_category( $product_category->term_id ) ) ) {
				$title = '';
			}
		}

		if ( ! empty( $title ) ) {

			/**
			 * Filters the category title.
			 *
			 * @since 1.0
			 *
			 * @param string $title category title HTML
			 * @param \WP_Term[] $categories list of category objects
			 * @param \WP_Term $current_category current category object
			 */
			echo wp_kses_post( (string) apply_filters( 'wc_nested_category_layout_category_title_html', sprintf( '<h2 class="wc-nested-category-layout-category-title">%s</h2>', $title ), $categories, $current_category ) );
		}

		// optional thumbnail/description of the category
		$category = $categories[ count( $categories ) - 1 ];

		if ( $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ) ) {

			$image_src  = wp_get_attachment_image_src( $thumbnail_id );
			$image_html = '';

			if ( is_array( $image_src ) && isset( $image_src[0] ) ) {
				$image_html = '<img src="' . esc_attr( $image_src[0] ) . '" alt="' . esc_attr( $category->name ). '" class="wc-nested-category-layout-category-image" />';
			}

			/**
			 * Filters the current category image.
			 *
			 * @since 1.0
			 *
			 * @param string $image_html current category image HTML
			 * @param \WP_Term $category current category object
			 */
			echo (string) apply_filters( 'wc_nested_category_layout_category_image', $image_html, $category );
		}

		/* @see the_content() for the WordPress filter applied here to the optional category description */
		if ( $category->description && has_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description' ) && ( $description = apply_filters( 'the_content', (string) $category->description ) ) ) {

			/**
			 * Filters the current category description.
			 *
			 * @since 1.20.0
			 *
			 * @param string $description the description HTML
			 * @param \WP_Term $category current category object
			 */
			echo wp_kses_post( (string) apply_filters( 'wc_nested_category_layout_category_description', '<div class="subcategory-term_description term_description">' . $description . '</div>', $category ) );
		}

		$subcategory_products_per_page = (int) get_option( 'woocommerce_subcat_posts_per_page', 1 );

		$ordering = WC()->query->get_catalog_ordering_args();

		$query_args = [
			'post_type'      => 'product',
			'wc_query'       => 'product_query',
			'posts_per_page' => $subcategory_products_per_page,
			'tax_query'      => [
				[
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => [ $category->term_id ],
					'include_children' => false,
				],
			],
			'orderby' => $ordering['orderby'],
			'order'   => $ordering['order'],
		];

		if ( isset( $ordering['meta_key'] ) ) {
			$query_args['meta_key'] = $ordering['meta_key'];
		}

		query_posts( $query_args );

		wc_set_loop_prop( 'loop', 0 );

		wc_get_template( 'loop/nested-category.php', [
			'category'                         => $current_category ?: $product_category,
			'woocommerce_product_category_ids' => $product_category_ids,
			'see_more'                         => $wp_query->found_posts > $subcategory_products_per_page,
			'total_found'                      => $wp_query->found_posts,
		], '', wc_nested_category_layout()->get_plugin_path() . '/templates/' );

		wp_reset_query();
	}


}

if ( ! function_exists( 'woocommerce_category_products_content_section' ) ) {


	/**
	 * Our own template function, called for the current page category products, if they are contained by none of the deeper nested sub-categories.
	 *
	 * @since 1.0
	 * @deprecated since 1.20.0
	 *
	 * @param object $category category object, or null if on the /shop/ page
	 * @param array $product_category_ids associative array of product id to array of deepest categories the products belong to
	 */
	function woocommerce_category_products_content_section( $category, $product_category_ids ) {

		wc_get_template('loop/nested-category.php', [
			'woocommerce_product_category_ids' => $product_category_ids,
			'category'                         => $category,
		], '', wc_nested_category_layout()->get_plugin_path() . '/templates/' );
	}


}

if ( ! function_exists( 'woocommerce_product_subcategories' ) ) {


	/**
	 * Display product sub categories as thumbnails.
	 *
	 * This is a largely unchanged copy of the core woocommerce function, which
	 * simply bails if the nested category layout is detected to be enabled
	 * on the current page.
	 *
	 * Code based on WooCommerce 2.0.3 {@see woocommerce_product_subcategories()}
	 *
	 * @since 1.0
	 * @deprecated since 1.20.0
	 * @see woocommerce/woocommerce-template.php
	 *
	 * @return void|bool
	 */
	function woocommerce_product_subcategories( $args = [] ) {
		global $wp_query;

		// JES: don't show the subcategory thumbnails on the shop page or product category page with the nested layout option is enabled
		if ( ( is_shop() && 'yes' === get_option( 'woocommerce_nested_subcat_shop', 'no' ) ) ||
			 ( is_product_category() && 'yes' === get_option( 'woocommerce_nested_subcat_'.wc_nested_category_layout()->get_current_product_category_id(), 'no' ) ) ) {
			return true;
		}
		// End of modification

		$defaults = [
			'before'        => '',
			'after'         => '',
			'force_display' => false,
		];

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		// Main query only
		if ( ! is_main_query() && ! $force_display ) {
			return;
		}

		// Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
		if ( is_search() || is_filtered() || is_paged() || ( ! is_product_category() && ! is_shop() ) ) {
			return;
		}

		// Check categories are enabled
		if ( is_shop() && get_option( 'woocommerce_shop_page_display' ) === '' ) {
			return;
		}

		// Find the category + category parent, if applicable
		$term      = get_queried_object();
		$parent_id = empty( $term->term_id ) ? 0 : $term->term_id;

		if ( is_product_category() ) {
			$display_type = get_term_meta( $term->term_id, 'display_type', true );

			switch ($display_type) {
				case 'products':
					return;
				case '':
					if ( get_option( 'woocommerce_category_archive_display' ) === '' ) {
						return;
					}
				break;
			}
		}

		// NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( http://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work
		$args = (array) apply_filters( 'woocommerce_product_subcategories_args', [
			'parent'       => $parent_id,
			'menu_order'   => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 1,
			'taxonomy'     => 'product_cat',
			'pad_counts'   => 1,
		] );

		$product_categories     = get_categories( $args );

		if ( $product_categories ) {
			echo $before;

			foreach ( $product_categories as $category ) {
				wc_get_template('content-product_cat.php', [
					'category' => $category,
				]);
			}

			// if we are hiding products, disable the loop and pagination
			if ( is_product_category() ) {
				$display_type = get_term_meta( $term->term_id, 'display_type', true );

				switch ( $display_type ) {

					case 'subcategories':
						$wp_query->post_count    = 0;
						$wp_query->max_num_pages = 0;
					break;

					case '':
						if ( get_option( 'woocommerce_category_archive_display' ) === 'subcategories' ) {
							$wp_query->post_count    = 0;
							$wp_query->max_num_pages = 0;
						}
					break;
				}
			}

			if ( is_shop() && get_option( 'woocommerce_shop_page_display' ) === 'subcategories' ) {
				$wp_query->post_count    = 0;
				$wp_query->max_num_pages = 0;
			}

			echo $after;
		}

		return true;
	}


}

if ( ! function_exists( 'woocommerce_reset_loop' ) ) {


	/**
	 * Reset the loop's index and columns when we're done outputting a product loop.
	 *
	 * Code based on WooCommerce 2.0.3 {@see woocommerce_reset_loop()}
	 *
	 * @since 1.0
	 * @deprecated since 1.20.0
	 * @see woocommerce/woocommerce-template.php
	 *
	 * @return void
	 */
	function woocommerce_reset_loop() {
		global $woocommerce_loop;

		if ( isset( $woocommerce_loop['loop'] ) && $woocommerce_loop['loop'] ) {
			$woocommerce_loop['has_products'] = true;
		}

		// reset loop/columns globals when starting a new loop
		$woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';
	}


}
