<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

    class WC_pdf_debug {

    	private $id;
    	private $debug;

        public function __construct() {

        	global $woocommerce;

        	// Get PDF Invoice Options
        	$woocommerce_pdf_invoice_settings = get_option('woocommerce_pdf_invoice_settings');
        	$this->id 	 = 'woocommerce_pdf_invoice';
        	$this->debug = false;

        	if( isset( $woocommerce_pdf_invoice_settings["pdf_debug"] ) && $woocommerce_pdf_invoice_settings["pdf_debug"] == "true" ) {
        		$this->debug = true;
        	}

        	// Add meta box for invoice values
			if ( $this->debug == true ) {
				// Add Invoice meta box
				add_action( 'add_meta_boxes', array( $this,'invoice_meta_init' ), 10, 2 );
				add_action( 'woocommerce_update_order', array( $this,'save_invoice_meta' ) );

			}

        }

        /**
         * [invoice_meta description]
         * @param  [type] $post_type [description]
         * @param  [type] $post      [description]
         * @return [type]            [description]
         */
		function invoice_meta_init( $post_type, $post ) {
			// Get the current user
        	$current_user = wp_get_current_user();

        	$invoice_meta_box_allowed_user_role  = apply_filters( 'pdf_invoice_allowed_user_role_invoice_meta_box', 'administrator' );

        	if ( in_array($invoice_meta_box_allowed_user_role, $current_user->roles) ) {
				add_meta_box( 'woocommerce-invoice-meta', __('Invoice Meta', 'woocommerce-pdf-invoice'), array($this,'woocommerce_invoice_meta_box'), 'shop_order', 'advanced', 'low');

				add_meta_box( 'woocommerce-invoice-meta', __('Invoice Meta', 'woocommerce-pdf-invoice'), array($this,'woocommerce_invoice_meta_box_hpos'), array( 'woocommerce_page_wc-orders', 'woocommerce_page_wc-orders--shop_subscription' ), 'advanced', 'low');
			}
		}
		
		/**
		 * [woocommerce_invoice_meta_box description]
		 * @param  [type] $post [description]
		 * @return [type]       [description]
		 */
		function woocommerce_invoice_meta_box( $post ) {
			global $woocommerce;

			$order 							  = wc_get_order( $post->ID );
			$order_id 						  = $order->get_id();

			$data 							  = get_post_custom( $order_id );
			$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$pdf_invoice_meta_items			  = WC_pdf_functions::clean_invoice_meta( $order->get_meta( '_invoice_meta', TRUE ) );

			// Make sure new field wc_pdf_invoice_number does not cause a meta_update if it's empty
			if( !isset( $pdf_invoice_meta_items['wc_pdf_invoice_number'] ) || $pdf_invoice_meta_items['wc_pdf_invoice_number'] == '' ) {

				$pdf_invoice_meta_items['wc_pdf_invoice_number'] = $pdf_invoice_meta_items['invoice_number'];

				// Add wc_pdf_invoice_number
				WC_pdf_functions::update_order_meta_data ( '_invoice_meta', $pdf_invoice_meta_items, $order, $order_id );
				WC_pdf_functions::update_order_meta_data ( '_wc_pdf_invoice_number', $pdf_invoice_meta_items['invoice_number'], $order, $order_id );

			}

?>
			<div class="invoice_meta_group">
				<ul>
<?php 
			foreach( $pdf_invoice_meta_items as $key => $value ) {
				echo '<li><span>' . ucwords( str_replace( '_', ' ', $key) ) . ' : </span><input name="' . $key . '" type="text" value="' . $value . '" /></li>';
			}
?>
				</ul>
				<p><?php _e('Please ensure you are aware of any potential legal issues before changing this information.<br />Changing the "Invoice Number" field IS NOT RECOMMENDED, changing this could cause duplicate invoice numbers.', 'woocommerce-pdf-invoice'); ?></p>
				<div class="clear"></div>
			</div><?php
			
		}

		/**
		 * [woocommerce_invoice_meta_box_hpos description]
		 * @param  [type] $post [description]
		 * @return [type]       [description]
		 */
		function woocommerce_invoice_meta_box_hpos( $order ) {
			global $woocommerce;

			$order_id 						  = $order->get_id();

			$data 							  = get_post_custom( $order_id );
			$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$pdf_invoice_meta_items			  = WC_pdf_functions::clean_invoice_meta( $order->get_meta( '_invoice_meta', TRUE ) );

			if( is_array( $pdf_invoice_meta_items ) ) {

				// Make sure new field wc_pdf_invoice_number does not cause a meta_update if it's empty
				if( !isset( $pdf_invoice_meta_items['wc_pdf_invoice_number'] ) || $pdf_invoice_meta_items['wc_pdf_invoice_number'] == '' ) {

					$pdf_invoice_meta_items['wc_pdf_invoice_number'] = $pdf_invoice_meta_items['invoice_number'];

					// Add wc_pdf_invoice_number
					WC_pdf_functions::update_order_meta_data ( '_invoice_meta', $pdf_invoice_meta_items, $order, $order_id );
					WC_pdf_functions::update_order_meta_data ( '_wc_pdf_invoice_number', $pdf_invoice_meta_items['invoice_number'], $order, $order_id );

				}

?>
				<div class="invoice_meta_group">
					<ul>
<?php 
				foreach( $pdf_invoice_meta_items as $key => $value ) {
					echo '<li><span>' . ucwords( str_replace( '_', ' ', $key) ) . ' : </span><input name="' . $key . '" type="text" value="' . $value . '" /></li>';
				}
?>
					</ul>
					<p><?php _e('Please ensure you are aware of any potential legal issues before changing this information.<br />Changing the "Invoice Number" field IS NOT RECOMMENDED, changing this could cause duplicate invoice numbers.', 'woocommerce-pdf-invoice'); ?></p>
					<div class="clear"></div>
				</div><?php

			}
			
		}

		/**
		 * [save_invoice_meta description]
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		function save_invoice_meta( $order ) {
			global $woocommerce;

			if( !is_object( $order ) ) {
				$order 	 = wc_get_order( $order );
			}

			// Get the current user
        	$current_user = wp_get_current_user();
        	$save_invoice_meta_allowed_user_role  = apply_filters( 'pdf_invoice_allowed_user_role_invoice_meta_box', 'administrator' );
        	
        	if( in_array( $save_invoice_meta_allowed_user_role, $current_user->roles ) ) {

				$order_id                		  = $order->get_id();
				$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );
				$old_pdf_invoice_meta_items		  = WC_pdf_functions::clean_invoice_meta( $order->get_meta( '_invoice_meta', TRUE ) );
				$ordernote 						  = '';
				$new_invoice_meta 				  = array();

				if( isset( $old_pdf_invoice_meta_items['invoice_created'] ) ) {

					$invoice_meta_fields = WC_pdf_functions::get_invoice_meta_fields();	
						
					foreach( $invoice_meta_fields as $invoice_meta_field ) {

						// Clean up empty values
						$old_pdf_invoice_meta_items[$invoice_meta_field] = json_encode( $old_pdf_invoice_meta_items[$invoice_meta_field] ) == 'null' ? '' : $old_pdf_invoice_meta_items[$invoice_meta_field];

						// Build old values array so that the array order of the old list matches the new list
						$old_invoice_meta[$invoice_meta_field] = $old_pdf_invoice_meta_items[$invoice_meta_field];

						// Build new values array
						$new_invoice_meta[$invoice_meta_field] = isset( $_POST[$invoice_meta_field] ) ? wc_clean( $_POST[$invoice_meta_field] ) : wc_clean( $old_pdf_invoice_meta_items[$invoice_meta_field] );

					}

					// Only update if the invoice meta has changed.
					if( md5( json_encode($old_invoice_meta) ) !== md5( json_encode($new_invoice_meta) ) ) {

						// Update the invoice_meta
						WC_pdf_functions::update_order_meta_data ( '_invoice_meta', $new_invoice_meta, $order, $order_id );

						// Update the individual invoice meta
						foreach( $new_invoice_meta as $key => $value ) {
							WC_pdf_functions::update_order_meta_data ( '_'.$key, $value, $order, $order_id );
						}

						// Add an order note with the original infomation
						foreach( $old_pdf_invoice_meta_items as $key => $value ) {
							$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
						}

						// Add order note
						$order->add_order_note( __("Invoice information changed. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

						// Let's check the "next invoice number" setting
						if ( isset($_POST['invoice_number']) && wc_clean( $_POST['invoice_number'] ) > get_option( 'woocommerce_pdf_invoice_current_invoice' ) ) {
							update_option( 'woocommerce_pdf_invoice_current_invoice', wc_clean( $_POST['invoice_number'] ) );
						}
						

					}

				}

			}
			
		}

        /**
         * [sagepay_debug description]
         * @param  Array   $tolog   contents for log
         * @param  String  $id      payment gateway ID
         * @param  String  $message additional message for log
         * @param  boolean $start   is this the first log entry for this transaction
         */
        public static function pdf_debug( $tolog = NULL, $id = NULL, $message = NULL, $start = FALSE ) {

        	if( !class_exists('WC_Logger') ) {
        		return;
        	}

            if( !isset( $logger ) ) {
                $logger      = new stdClass();
                $logger->log = new WC_Logger();
            }

            /**
             * If this is the start of the logging for this transaction add the header
             */
            if( $start ) {

                $logger->log->add( $id, __('', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('PDF Invoice Log', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('' .date('d M Y, H:i:s'), 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('', 'woocommerce-pdf-invoice') );

            }

            $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );
            $logger->log->add( $id, $message );
            $logger->log->add( $id, print_r( $tolog, TRUE ) );
            $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );

        }
    }

    $GLOBALS['WC_pdf_debug'] = new WC_pdf_debug();