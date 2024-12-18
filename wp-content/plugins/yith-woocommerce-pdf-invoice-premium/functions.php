<?php
/**
 * Functions to be used in the plugin.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Functions
 */

/**
 * Some constant values used for url argument vars.
 */
defined( 'YITH_YWPI_RESET_DROPBOX' ) || define( 'YITH_YWPI_RESET_DROPBOX', 'reset-dropbox' );
defined( 'YITH_YWPI_GO_TO_DROPBOX' ) || define( 'YITH_YWPI_GO_TO_DROPBOX', 'authenticate-dropbox' );

if ( ! function_exists( 'ywpi_get_filesize_text' ) ) {
	/**
	 * Convert a file size in a textual value
	 *
	 * @param int $size the size in bytes.
	 *
	 * @return string file size in text mode
	 */
	function ywpi_get_filesize_text( $size ) {
		$unit = array( 'bytes', 'KB', 'MB', 'GB', 'TB' );
		$step = 0;

		while ( $size >= 1024 ) {
			$size = $size / 1024;
			++$step;
		}

		return sprintf( '%s %s', round( $size ), $unit[ $step ] );
	}
}

if ( ! function_exists( 'ywpi_get_option_with_placeholder' ) ) {
	/**
	 * Retrieve option value with a mandatory placeholder queued if not exists
	 *
	 * @param string $option_name name of the option to retrieve.
	 * @param string $placeholder name of the mandatory placeholder to be included.
	 * @param mixed  $obj         the object.
	 *
	 * @return mixed|string|void    new option value
	 */
	function ywpi_get_option_with_placeholder( $option_name, $placeholder, $obj = null ) {
		$value = ywpi_get_option( $option_name, $obj );

		$placeholder = apply_filters( 'ywpi_get_option_mandatory_placeholder', $placeholder );

		if ( ! isset( $value ) ) {
			return $placeholder;
		}

		if ( false === strpos( $value, $placeholder ) ) {
			return $value . $placeholder;
		}

		return $value;
	}
}

if ( ! function_exists( 'ywpi_is_active_woo_eu_vat_number' ) ) {
	/***
	 * Check if WooThemes EU VAT number is active
	 */
	function ywpi_is_active_woo_eu_vat_number() {
		return defined( 'WC_EU_VAT_VERSION' ) && ( version_compare( WC_EU_VAT_VERSION, '2.1.0' ) >= 0 );
	}
}

if ( ! function_exists( 'ywpi_use_woo_eu_vat_number' ) ) {
	/***
	 * Check if WooThemes EU VAT number should be used
	 */
	function ywpi_use_woo_eu_vat_number() {
		return ywpi_is_active_woo_eu_vat_number() && ( 'eu-vat-number' === strval( ywpi_get_option( 'ywpi_ask_vat_number_source' ) ) );
	}
}

if ( ! function_exists( 'ywpi_start_plugin_compatibility' ) ) {
	/**
	 * Init all third part plugin compatibilities
	 *
	 * @since  1.0.0
	 */
	function ywpi_start_plugin_compatibility() {
		if ( defined( 'YITH_WPV_PREMIUM' ) ) {
			require_once YITH_YWPI_INC_DIR . 'class.yith-ywpi-multivendor-loader.php';
		}
	}
}

if ( ! function_exists( 'ywpi_get_option' ) ) {
	/**
	 * Make a get_option call with filterable option name
	 *
	 * @param string $option         Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param mixed  $obj            The object id associated to the option.
	 * @param mixed  $default_option Optional. Default value to return if the option does not exist.
	 *
	 * @return mixed Value set for the option.
	 *
	 * @since  1.0.0
	 */
	function ywpi_get_option( $option, $obj = null, $default_option = false ) {
		$option = apply_filters( 'ywpi_option_name', $option, $obj );

		return get_option( $option, $default_option );
	}
}

if ( ! function_exists( 'ywpi_update_option' ) ) {
	/**
	 * Make a update_option call with filterable option name
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param mixed  $value  the value.
	 * @param mixed  $obj    the object id associated to the option.
	 *
	 * @return mixed Value set for the option.
	 *
	 * @since  1.0.0
	 */
	function ywpi_update_option( $option, $value, $obj = null ) {
		$option = apply_filters( 'ywpi_option_name', $option, $obj );
		wp_cache_delete( $option, 'options' );

		return update_option( $option, $value );
	}
}

if ( ! function_exists( 'ywpi_document_behave_as_invoice' ) ) {
	/**
	 * Check if the current document behave like an invoice document, that is, the document is of type Invoice, Pro-forma invoice or Credit note
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_document_behave_as_invoice( $document ) {
		return ( $document instanceof YITH_Invoice ) || ( $document instanceof YITH_Pro_Forma ) || ( $document instanceof YITH_Credit_Note );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_picture' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_picture( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_picture', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_picture', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_picture', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_quantity' ) ) {
	/**
	 * Check if the quantity column should be shown for a specific document.
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_quantity( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_quantity', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_quantity', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_quantity', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_product_price' ) ) {
	/**
	 * Check if the product price column should be shown for a specific document.
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_product_price( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_product_price', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_product_price', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_product_price', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_regular_price' ) ) {
	/**
	 * Check if the regular price column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_regular_price( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_regular_price', $document, 'no' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_regular_price', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_regular_price', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_sale_price' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_sale_price( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_sale_price', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_sale_price', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_sale_price', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_line_total' ) ) {
	/**
	 * Check if the line total column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_line_total( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_line_total', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_line_total', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_line_total', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_percentage' ) ) {
	/**
	 * Check if the percentage column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_percentage( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_percentage', $document, 'no' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_percentage', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_percenage', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_total_taxed' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_total_taxed( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_total_taxed', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_total_taxed', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_total_taxed', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_tax' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_tax( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_tax', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_tax', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_tax', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_percentage_tax' ) ) {
	/**
	 * Check if the percentage tax column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_percentage_tax( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_percentage_tax', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_percentage_tax', $document, 'yes' ) );
		}
		return apply_filters( 'ywpi_is_enabled_column_percentage_tax', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_variation' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document.
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_variation( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_variation', $document, 'no' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_variation', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_variation', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_sku' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_sku( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_SKU', $document, 'yes' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_SKU', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_sku', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_weight_dimension' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_weight_dimension( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_weight', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_weight', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_short_description' ) ) {
	/**
	 * Check if the picture column should be shown for a specific document
	 *
	 * @param object $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_short_description( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_invoice_column_short_description', $document, 'no' ) );
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_column_short_description', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_column_short_description', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_visible_order_totals' ) ) {
	/**
	 * Retrieve if the order totals section should be shown for a document
	 *
	 * @param object $document The document object.
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 */
	function ywpi_is_visible_order_totals( $document ) {
		$is_visible = false;

		if ( ywpi_document_behave_as_invoice( $document ) ) {
			$is_visible = true;
		} elseif ( $document instanceof YITH_Shipping ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_show_order_totals', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_visible_order_totals', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_visible_order_discount' ) ) {
	/**
	 * Retrieve if the order discount amount should be shown for a document
	 *
	 * @param object $document The document object.
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 */
	function ywpi_is_visible_order_discount( $document ) {
		$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_show_discount', $document, 'yes' ) );

		return apply_filters( 'ywpi_is_visible_order_discount', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_visible_broken_down_taxes' ) ) {
	/**
	 * Retrieve if the broken down taxes should be shown for a document
	 *
	 * @param object $document The document object.
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 */
	function ywpi_is_visible_broken_down_taxes( $document ) {
		$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_broken_down_taxes', $document, 'yes' ) );

		return apply_filters( 'ywpi_is_visible_broken_down_taxes', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_get_order_document_by_type' ) ) {
	/**
	 * Retrieve the document or the document list of a specific type for an oder
	 *
	 * @param int    $order_id      the order id.
	 * @param string $document_type the document type. The type of document. It could be 'invoice', 'packing-slip', 'credit-note', 'proforma'.
	 *
	 * @return YITH_Document|void
	 * @since  1.0.0
	 */
	function ywpi_get_order_document_by_type( $order_id, $document_type = '' ) {
		if ( ! is_numeric( $order_id ) ) {
			return null;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return null;
		}

		$document = null;

		switch ( $document_type ) {
			case 'proforma':
				$document = new YITH_Pro_Forma( $order_id );
				break;

			case 'credit-note':
				$document = new YITH_Credit_Note( $order_id );
				break;

			case 'invoice':
				$document = new YITH_Invoice( $order_id );
				break;

			case 'packing-slip':
				$document = new YITH_Shipping( $order_id );
				break;

			default:
				$document = apply_filters( 'yith_ywpi_get_order_documents_by_type', $document, $document_type, $order_id );
		}

		return $document;
	}
}

if ( ! function_exists( 'ywpi_get_invoice' ) ) {
	/**
	 * Retrieve the invoice for a specific order, if exists
	 *
	 * @param int|WC_Order $order the order or order id.
	 * @param string       $type The document type.
	 *
	 * @return YITH_Invoice|mixed|void
	 * @since  1.0.0
	 */
	function ywpi_get_invoice( $order, $type = 'invoice' ) {
		return ywpi_get_order_document_by_type( $order, $type );
	}
}

if ( ! function_exists( 'ywpi_get_packing_slip' ) ) {
	/**
	 * Retrieve the packing slip document for a specific order, if exists
	 *
	 * @param int|WC_Order $order The order or order id.
	 *
	 * @return YITH_Shipping|mixed|void
	 * @since  1.0.0
	 */
	function ywpi_get_packing_slip( $order ) {
		return ywpi_get_order_document_by_type( $order, 'packing-slip' );
	}
}

if ( ! function_exists( 'ywpi_get_pro_forma' ) ) {
	/**
	 * Retrieve the pro-forma document for a specific order, if exists
	 *
	 * @param int|WC_Order $order the order or order id.
	 *
	 * @return YITH_Pro_Forma|mixed|void
	 * @since  1.0.0
	 */
	function ywpi_get_pro_forma( $order ) {
		return ywpi_get_order_document_by_type( $order, 'proforma' );
	}
}

if ( ! function_exists( 'ywpi_get_credit_note' ) ) {
	/**
	 * Retrieve the credit note document for a specific order, if exists
	 *
	 * @param int|WC_Order $order the order or order id.
	 *
	 * @return YITH_Credit_Note|mixed|void
	 * @since  1.0.0
	 */
	function ywpi_get_credit_note( $order ) {
		return ywpi_get_order_document_by_type( $order, 'credit-note' );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_credit_note_subtotal_column' ) ) {
	/**
	 * Check if the 'subtotal' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_credit_note_subtotal_column( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_subtotal_column', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_subtotal_column', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_credit_note_total_tax_column' ) ) {
	/**
	 * Check if the 'tax' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_credit_note_total_tax_column( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_total_tax_column', $document, 'no' ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_total_tax_column', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_credit_note_total_shipping_column' ) ) {
	/**
	 * Check if the 'tax' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_credit_note_total_shipping_column( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_total_shipping_column', $document ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_total_shipping_column', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_credit_note_total_column' ) ) {
	/**
	 * Check if the 'total' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document The document object.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_credit_note_total_column( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_total_column', $document ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_total_column', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_picture_credit_notes' ) ) {
	/**
	 * Check if the 'total' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document Document.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_picture_credit_notes( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_product_image_column', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_total_column', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_sku_credit_notes' ) ) {
	/**
	 * Check if the 'total' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document Document.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_sku_credit_notes( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_product_sku_column', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_total_column', $is_visible, $document );
	}
}

if ( ! function_exists( 'ywpi_is_enabled_column_product_name_credit_notes' ) ) {
	/**
	 * Check if the 'total' column is enabled for credit note
	 *
	 * @param YITH_Credit_Note $document Document.
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	function ywpi_is_enabled_column_product_name_credit_notes( $document ) {
		$is_visible = false;

		if ( $document instanceof YITH_Credit_Note ) {
			$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_credit_note_product_name_column', $document, 'yes' ) );
		}

		return apply_filters( 'ywpi_is_enabled_credit_note_total_column', $is_visible, $document );
	}
}
