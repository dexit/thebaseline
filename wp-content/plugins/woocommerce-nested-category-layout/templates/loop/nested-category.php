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
 * needs please refer to http://www.skyverge.com/product/woocommerce-nested-category-layout/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined('ABSPATH') or exit;

/**
 * Template for displaying nested category products.
 *
 * @type array<int, int[]> $woocommerce_product_category_ids associative array of product IDs containing category IDs
 * @type \WP_Term $category current category object
 * @type bool $see_more whether to display see more link or not
 * @type int $total_found total number of products found
 *
 * @since 1.0
 * @version 1.20.0
 */
global $woocommerce_loop;

$product_category_level = isset( $category->depth ) ? (int) $category->depth + 2 : 1;
$num_columns            = max( 1, ! empty( $woocommerce_loop['columns'] ) ? (int) $woocommerce_loop['columns'] : (int) get_option( 'woocommerce_catalog_columns', 4 ) );

if ( $total_found && have_posts() ) :

	/**
	 * Action hook to add content before the nested category products section.
	 *
	 * @since 1.20.0
	 *
	 * @param \WP_Term $category category object
	 */
	do_action( 'woocommerce_nested_category_products_before_content_section', $category );

	/**
	 * Filters the CSS classes applied to the <ul> products wrapper element.
	 *
	 * @since 1.11.1
	 *
	 * @param string[] $classes array of CSS classes
	 * @param int $product_category_level current product category level
	 * @param int $num_columns number of columns to display
	 * @param bool $see_more whether the see more link will be displayed or not
	 */
	$classes = (array) apply_filters( 'wc_nested_category_layout_loop_products_wrapper_classes', [
		'wc-nested-category-layout-subcategory-products',
		'subcategory-products',
		'products',
		'product-category-level-' . $product_category_level,
		'columns-' . $num_columns
	], $product_category_level, $num_columns, $see_more );

	?>
	<ul class="<?php echo implode( ' ', $classes ); ?>">
		<?php

		// get the current product category ID
		if ( ! is_object( $category ) ) {
			$product_category_term_id = 0;
		} else {
			$product_category_term_id = $category->term_id;
		}

		// loop through all products
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			global $product;

			// ensure that the product is valid and visible
			if ( ! $product || ! $product->is_visible() ) :
				continue;
			endif;

			$product_id = $product->get_id();
			$product_category_term_ids = $woocommerce_product_category_ids[ $product_id ] ?? [];

			// ensure that the product belongs to the current category
			if ( ! in_array( $product_category_term_id, $product_category_term_ids, false ) && wc_nested_category_layout()->is_layered_nav_active() ) :
				// note: not bumping $products_displayed is intentional here
				continue;
			endif;

			// display the product thumbnail content
			wc_get_template_part( 'content', 'product' );

		endwhile; endif; // products loop

		?>
	</ul>

	<div style="clear:both;">
		<?php if ( $see_more ) : ?>

			<a class="woocommerce-nested-category-layout-see-more" href="<?php echo esc_attr( get_term_link( $category ) ); ?>"><?php
				/**
				 * Filters the "see more" string in nested category layout views.
				 *
				 * @since 1.0
				 *
				 * @param string $see_more text message string (must not contain HTML)
				 * @param \WP_Term $category category object
				 */
				echo esc_html( (string) apply_filters('wc_nested_category_layout_see_more_message', __( 'See more', 'woocommerce-nested-category-layout' ), $category ) ); ?></a>

		<?php endif; // $see_more ?>
	</div>
	<?php

	/**
	 * Action hook to add content after the nested category products section.
	 *
	 * @since 1.20.0
	 *
	 * @param \WP_Term $category category object
	 */
	do_action( 'woocommerce_nested_category_products_after_content_section', $category );

endif; // have_posts() ?>
