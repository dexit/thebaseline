<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Dompdf\Dompdf;
use Themesquad\WC_Store_Catalog_PDF_Download\Utilities\Template_Utils;

class WC_Store_Catalog_PDF_Download_Ajax {
	private static $_this;

	/**
	 * Accepted layouts.
	 *
	 * @since 2.1.0
	 *
	 * @var array
	 */
	private static $accepted_layouts = array( 'single', 'grid', 'list' );

	/**
	 * Init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_action( 'wp_ajax_wc_store_catalog_pdf_download_frontend_generate_pdf_ajax', array( $this, 'generate_pdf_ajax' ) );

		add_action( 'wp_ajax_nopriv_wc_store_catalog_pdf_download_frontend_generate_pdf_ajax', array( $this, 'generate_pdf_ajax' ) );

		add_action( 'wc_store_catalog_pdf_download_product_attr', 'wc_display_product_attributes' );

		return true;
	}

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @return instance object
	 */
	public static function get_instance() {
		return self::$_this;
	}

	/**
	 * Get the download
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function generate_pdf_ajax() {
		$nonce = $_POST['ajaxPDFDownloadNonce'];

		// bail if nonce don't check out
		if ( ! wp_verify_nonce( $nonce, '_wc_store_catalog_pdf_download_nonce' ) ) {
			die( 'error' );
		}

		global $wc_posts, $layout, $is_single;

		if ( isset( $_POST['posts'] ) && ! empty( $_POST['posts'] ) ) {

			$posts = json_decode( $_POST['posts'] );

			$posts = array_map( 'absint', $posts );

		} else {
			$posts = false;
		}

		$is_single = isset( $_POST['is_single'] ) ? sanitize_text_field( $_POST['is_single'] ) : '';

		$layout = isset( $_POST['layout'] ) ? sanitize_text_field( wp_unslash( $_POST['layout'] ) ) : '';

		// single template trumps others
		$layout = ! empty( $is_single ) && $is_single === 'true' ? 'single' : $layout;

		$layout = in_array( $layout, self::$accepted_layouts, true ) ? $layout : '';

		// portrait, landscape
		$orientation = apply_filters( 'wc_store_catalog_pdf_download_orientation', 'portrait' );

		// 'letter', 'A4', 'legal'
		$size = apply_filters( 'wc_store_catalog_pdf_download_size', 'letter' );

		// create the object
		$dompdf = new DOMPDF();
		$dompdf->getOptions()->set(
			array(
				'defaultFont'     => 'DejaVu Sans',
				'isRemoteEnabled' => true,
			)
		);

		do_action( 'woocommerce_store_catalog_pdf_download_dompdf_options', $dompdf );

		if ( $wc_posts = $posts ) {

			@set_time_limit( 0 );

			ob_start();

			Template_Utils::get_template( 'pdf-layout-header-html.php' );
			Template_Utils::get_template( "pdf-layout-{$layout}-html.php" );
			Template_Utils::get_template( 'pdf-layout-footer-html.php' );

			$html = ob_get_clean();

			// render pdf
			$dompdf->loadHtml( $html );
			$dompdf->setPaper( $size, $orientation );
			$dompdf->render();

			$upload_dir = wp_upload_dir();
			$pdf_path   = $upload_dir['basedir'] . '/woocommerce-store-catalog-pdf-download/';
			$pdf_url    = $upload_dir['baseurl'] . '/woocommerce-store-catalog-pdf-download/';
			$filename   = apply_filters( 'wc_store_catalog_pdf_download_filename', str_replace( ' ', '-', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) . '-' . __( 'Store-Catalog', 'woocommerce-store-catalog-pdf-download' ) . '-' . time() . '.pdf' );

			if ( ! is_dir( $pdf_path ) ) {
				mkdir( $pdf_path, 0777, true );
			}

			file_put_contents( $pdf_path . $filename, $dompdf->output() );

			echo $pdf_url . $filename;
			exit;
		}

		echo 'error';
		exit;
	}

	/**
	 * Display the product attributes.
	 *
	 * @see wc_display_product_attributes()
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 */
	public function display_attributes( $product ) {
		wc_deprecated_function( __FUNCTION__, '2.0.0', 'wc_display_product_attributes' );

		wc_display_product_attributes( $product );
	}

	/**
	 * Returns product image, by making sure it contains absolute URLs to be used in templates.
	 *
	 * @see WC_Product->get_image()
	 *
	 * @deprecated 2.0.0
	 *
	 * @param WC_Product $product
	 * @param array      $size
	 * @return string
	 */
	public static function get_product_image( $product, $size ) {
		wc_deprecated_function( __FUNCTION__, '2.0.0', 'WC_Product->get_image()' );

		return $product->get_image( $size );
	}

	/**
	 * Gets the product meta template.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	public static function get_product_meta_template( $product ) {
		return Template_Utils::locate_template( 'pdf-layout-product-meta-html.php' );
	}

	/**
	 * Get the body template
	 *
	 * @since 1.0.0
	 * @since 2.1.0 Return an empty string if the template is not found.
	 * @deprecated 2.1.0
	 *
	 * @param string $layout The layout to be included.
	 * @return string
	 */
	public static function get_body_template( $layout ) {
		wc_deprecated_function( __FUNCTION__, '2.1.0' );

		return Template_Utils::locate_template( "pdf-layout-{$layout}-html.php" );
	}

	/**
	 * Gets the header template.
	 *
	 * @since 1.0.0
	 * @deprecated 2.1.0
	 *
	 * @return string
	 */
	public static function get_header_template() {
		wc_deprecated_function( __FUNCTION__, '2.1.0' );

		return Template_Utils::locate_template( 'pdf-layout-header-html.php' );
	}

	/**
	 * Gets the footer template.
	 *
	 * @since 1.0.0
	 * @deprecated 2.1.0
	 *
	 * @return string
	 */
	public static function get_footer_template() {
		wc_deprecated_function( __FUNCTION__, '2.1.0' );

		return Template_Utils::locate_template( 'pdf-layout-footer-html.php' );
	}

	/**
	 * Get placeholder image
	 *
	 * @since 1.0.0
	 * @param array $image_size
	 * @return html template
	 */
	public static function get_placeholder_image( $image_size ) {
		if ( ! isset( $image_size ) || empty( $image_size ) ) {
			$image_size = array( 450, 450 );
		}

		return '<img src="' . wc_placeholder_img_src() . '" alt="' . __( 'Placeholder', 'woocommerce-store-catalog-pdf-download' ) . '" width="' . esc_attr( $image_size[0] ) . '" height="' . esc_attr( $image_size[1] ) . '" class="woocommerce-placeholder wp-post-image" />';
	}
}

new WC_Store_Catalog_PDF_Download_Ajax();
