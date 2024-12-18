<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class WC_pdf_order_meta_box {

	    public function __construct() {

	    	// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
	    	if ( extension_loaded('iconv') && extension_loaded('mbstring') ) {					
				
				// Add Invoice meta box to completed orders
				add_action( 'add_meta_boxes', array( $this,'invoice_details_admin_init' ), 10, 2 );

				add_action( 'admin_init' , array( $this,'admin_pdf_url_check') );

		    	// Add Create and Delete invoice options to WooCommerce Order Actions dropdown
		    	add_filter( 'woocommerce_order_actions', array( $this, 'pdf_invoice_woocommerce_order_actions' ), 10, 2 );

		    	// Delete Invoice per order
		    	add_action ( 'woocommerce_order_action_delete_invoice', array( $this, 'delete_invoice' ) );

		    	// Order Actions Meta Box
		    	add_action ( 'woocommerce_order_action_pdf_invoices_delete_invoice', array( $this, 'delete_invoice_per_order' ) );
		    	add_action ( 'woocommerce_order_action_pdf_invoices_create_invoice', array( $this, 'create_invoice_per_order' ) );
		    	add_action ( 'woocommerce_order_action_pdf_invoices_email_invoice',  array( $this, 'email_invoice_per_order' ) );

				// Message when email has been sent
    			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ), 99 );

			}

		}

		/**
		 * Create Invoice MetaBox
		 */	
		function invoice_details_admin_init( $post_type, $post ) {

			add_meta_box( 'woocommerce-invoice-details', __('Invoice Details', 'woocommerce-pdf-invoice'), array($this,'woocommerce_invoice_details_meta_box'), array('shop_order','shop_subscription' ), 'side', 'high');

			add_meta_box( 'woocommerce-invoice-details', __('Invoice Details', 'woocommerce-pdf-invoice'), array($this,'woocommerce_invoice_details_meta_box_hpos'), array( 'woocommerce_page_wc-orders', 'woocommerce_page_wc-orders--shop_subscription' ), 'side', 'high');
		
		}
		
		/**
		 * Displays the invoice details meta box
		 * We include a download link, even if the order is not complete - let's the store owner view an invoice before the order is complete.
		 */
		function woocommerce_invoice_details_meta_box( $post ) {
			global $woocommerce;

			if( !class_exists('WC_send_pdf') ){
				include( 'class-pdf-send-pdf-class.php' );
			}

			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$order_id 		= $post->ID;
			$data 			= get_post_custom( $order_id );

			$order 			= wc_get_order( $order_id );

			$invoice_meta 	= $order->get_meta( '_invoice_meta', TRUE );

			$pdf_invoice_download_link = add_query_arg( array(
							    'post' 			=> $order_id,
							    'action' 		=> 'edit',
							    'pdf_method' 	=> 'download',
							    'pdfid' 		=> $order_id,
							), admin_url( 'post.php' ) ); 

			$pdf_invoice_email_link = add_query_arg( array(
							    'post' 			=> $order_id,
							    'action' 		=> 'edit',
							    'pdf_method' 	=> 'email',
							    'pdfid' 		=> $order_id,
							), admin_url( 'post.php' ) ); 

			?>
			<div class="invoice_details_group">
				<ul>
		
					<li class="left"><p>
						<?php _e( 'Invoice Number:', 'woocommerce-pdf-invoice' ); ?>
						<?php if ( isset( $invoice_meta['invoice_number_display'] ) ) {
								echo $invoice_meta['invoice_number_display']; 
							  } elseif( $order->get_meta( '_invoice_number_display', TRUE ) ) {
							  	echo $order->get_meta( '_invoice_number_display', TRUE ); 
							  }
						?>
					</p></li>
		
					<li class="left"><p>
						<?php _e( 'Invoice Date:', 'woocommerce-pdf-invoice' ); ?>
						<?php
						if ( isset( $invoice_meta['invoice_date'] ) ) {
							echo $invoice_meta['invoice_date'];
						} elseif( $order->get_meta( '_invoice_date', TRUE ) ) {
							echo $order->get_meta( '_invoice_date', TRUE );
						}

						?>
					</p></li>
	                
	                <li>
						<p><a class="pdf_invoice_metabox_download_invoice" href="<?php echo $pdf_invoice_download_link ?>"><?php _e( 'Download Invoice', 'woocommerce-pdf-invoice' ); ?></a></p>
					</li>

					<li>
						<p><a class="pdf_invoice_metabox_send_invoice" href="<?php echo $pdf_invoice_email_link ?>"><?php _e( 'Email Invoice', 'woocommerce-pdf-invoice' ); ?></a></p>
					</li>

				</ul>
				<div class="clear"></div>
			</div><?php
			
		}

		/**
		 * Displays the invoice details meta box
		 * We include a download link, even if the order is not complete - let's the store owner view an invoice before the order is complete.
		 */
		function woocommerce_invoice_details_meta_box_hpos( $order ) {
			global $woocommerce;

			if( !class_exists('WC_send_pdf') ){
				include( 'class-pdf-send-pdf-class.php' );
			}

			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$order_id 		= $order->get_id();
			$data 			= get_post_custom( $order_id );

			$order 			= wc_get_order( $order_id );

			$invoice_meta 	= $order->get_meta( '_invoice_meta', TRUE );

			$pdf_invoice_download_link = add_query_arg( array(
							    'post' 			=> $order_id,
							    'action' 		=> 'edit',
							    'pdf_method' 	=> 'download',
							    'pdfid' 		=> $order_id,
							), admin_url( 'post.php' ) ); 

			$pdf_invoice_email_link = add_query_arg( array(
							    'post' 			=> $order_id,
							    'action' 		=> 'edit',
							    'pdf_method' 	=> 'email',
							    'pdfid' 		=> $order_id,
							), admin_url( 'post.php' ) ); 

			?>
			<div class="invoice_details_group">
				<ul>
		
					<li class="left"><p>
						<?php _e( 'Invoice Number:', 'woocommerce-pdf-invoice' ); ?>
						<?php if ( isset( $invoice_meta['invoice_number_display'] ) ) {
								echo $invoice_meta['invoice_number_display']; 
							  } elseif( $order->get_meta( '_invoice_number_display', TRUE ) ) {
							  	echo $order->get_meta( '_invoice_number_display', TRUE ); 
							  }
						?>
					</p></li>
		
					<li class="left"><p>
						<?php _e( 'Invoice Date:', 'woocommerce-pdf-invoice' ); ?>
						<?php
						if ( isset( $invoice_meta['invoice_date'] ) ) {
							echo $invoice_meta['invoice_date'];
						} elseif( $order->get_meta( '_invoice_date', TRUE ) ) {
							echo $order->get_meta( '_invoice_date', TRUE );
						}

						?>
					</p></li>
	                
	                <li>
						<p><a class="pdf_invoice_metabox_download_invoice" href="<?php echo $pdf_invoice_download_link ?>"><?php _e( 'Download Invoice', 'woocommerce-pdf-invoice' ); ?></a></p>
					</li>

					<li>
						<p><a class="pdf_invoice_metabox_send_invoice" href="<?php echo $pdf_invoice_email_link ?>"><?php _e( 'Email Invoice', 'woocommerce-pdf-invoice' ); ?></a></p>
					</li>

				</ul>
				<div class="clear"></div>
			</div><?php
			
		}

		/**
		 * Check Admin URL for pdfaction
		 */
		function admin_pdf_url_check() {
			global $woocommerce;

			if ( is_admin() && isset( $_GET['pdfid'] ) ) {

				$order_id 	= stripslashes( $_GET['pdfid'] );
				$order   	= wc_get_order($order_id);

				$sendback = add_query_arg( array(
							    'post' 			=> $order_id,
							    'action' 		=> 'edit'
							), admin_url( 'post.php' ) );

				if( isset( $_GET['pdf_method']) && $_GET['pdf_method'] == 'download' ) {

					// Add order note
					$order->add_order_note( __( "Invoice downloaded manually.", 'woocommerce-pdf-invoice' ), false, true );

					// Change the post saved message.
					add_filter( 'redirect_post_location', array( __CLASS__, 'set_pdf_downloaded_message' ) );

					$sendback = add_query_arg( array(
							    'message' 		=> '54',
							    'pdf_method' 	=> 'send',
							    'pdfid' 		=> $order_id
							), $sendback );

					$order->save();

					wp_redirect( $sendback );
					exit;

				}

				if( isset( $_GET['pdf_method']) && $_GET['pdf_method'] == 'email' ) {

					// Send the 'Resend Invoice', complete with PDF invoice!
					WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $order_id, $order );

					// Add order note
					$order->add_order_note( __( "Invoice emailed to customer manually.", 'woocommerce-pdf-invoice' ), false, true );

					// Change the post saved message.
					add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );

					$sendback = add_query_arg( array(
							    'message' 		=> '51'
							), $sendback );

					wp_redirect( $sendback );
					exit;

				}

				if( isset( $_GET['pdf_method']) && $_GET['pdf_method'] == 'send' ) {

					echo WC_send_pdf::get_woocommerce_pdf_invoice( $order, NULL, 'false' );
					exit;

				} 

			}

		}

	    /**
	     * [pdf_invoice_woocommerce_order_actions description]
	     * Add Create and Delete invoice options to the Order Actions dropdown.
	     * These options only show for admins
	     */
	    function pdf_invoice_woocommerce_order_actions( $orderactions, $order ) {

	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_woocommerce_order_actions', 'administrator', $orderactions );
			$current_user 		= wp_get_current_user();

			// Only admins can do this!
			if( in_array( $allowed_user_role, $current_user->roles ) ) {

		        // If there is an invoice then show Delete option else show Create option
		        if ( $order->get_meta( '_invoice_number', TRUE ) ) {
		        	$orderactions['pdf_invoices_email_invoice'] 	= 'Email PDF Invoice';
		        	$orderactions['pdf_invoices_delete_invoice'] 	= 'Delete Invoice';
		        } else {
		        	$orderactions['pdf_invoices_create_invoice'] 	= 'Create Invoice';
		        }

		    }

	        return $orderactions;
	    }

		/**
		 * [email_invoice_per_order description]
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		public static function email_invoice_per_order( $order ) {

			if( !is_null( $order) && is_object($order) ) {

				$order_id = $order->get_id();

				// Send the 'Resend Invoice', complete with PDF invoice!
				WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $order_id, $order );

				// Add order note
				$order->add_order_note( __( "Invoice emailed to customer manually.", 'woocommerce-pdf-invoice' ), false, true );

				// Change the post saved message.
				add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );

			}

		}

		/**
		 * [create_invoice_per_order description]
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		public static function create_invoice_per_order( $order ) {
			$order_id = $order->get_id();
			WC_pdf_functions::woocommerce_completed_order_create_invoice( $order_id );

			// Add order note
			$order->add_order_note( __( "Invoice created manually.", 'woocommerce-pdf-invoice' ), false, true );

			// Change the post saved message.
			add_filter( 'redirect_post_location', array( __CLASS__, 'set_pdf_created_message' ) );
		}

	    /**
	     * [delete_invoice_per_order description]
	     * @param  [type] $order [description]
	     * @return [type]        [description]
	     */
		public static function delete_invoice_per_order( $order ) {

			$ordernote 					= '';
			$order_id   				= $order->get_id();
			$invoice_meta 				= WC_pdf_functions::get_invoice_meta();
			$old_pdf_invoice_meta_items	= $order->get_meta( '_invoice_meta', TRUE );

			// Add an order note with the original infomation
			foreach( $old_pdf_invoice_meta_items as $key => $value ) {
				$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
			}

			// Delete the invoice meta
			foreach( $invoice_meta as $meta_key ) {
				// delete_post_meta( $order_id, $meta_key );
				WC_pdf_functions::delete_order_meta_data( $meta_key, $order, $order_id );
			}

			// Delete other postmeta
			WC_pdf_functions::delete_order_meta_data( '_invoice_created_mysql', $order, $order_id );
			WC_pdf_functions::delete_order_meta_data( '_wc_pdf_invoice_created_date', $order, $order_id );
			WC_pdf_functions::delete_order_meta_data( '_invoice_meta', $order, $order_id );

			WC_pdf_admin_functions::handle_next_invoice_number();

			// Add order note
			$order->add_order_note( __("Invoice deleted. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

			// Change the post saved message.
			add_filter( 'redirect_post_location', array( __CLASS__, 'set_pdf_deleted_message' ) );

		}

		/**
		 * [set_email_sent_message description]
		 * @param [type] $location [description]
		 */
		public static function set_email_sent_message( $location ) {
			return add_query_arg( 'message', 51, $location );
		}

		/**
		 * [set_pdf_deleted_message description]
		 * @param [type] $location [description]
		 */
		public static function set_pdf_deleted_message( $location ) {
			return add_query_arg( 'message', 52, $location );
		}

		/**
		 * [set_pdf_created_message description]
		 * @param [type] $location [description]
		 */
		public static function set_pdf_created_message( $location ) {
			return add_query_arg( 'message', 53, $location );
		}

		/**
		 * [set_pdf_downloaded_message description]
		 * @param [type] $location [description]
		 */
		public static function set_pdf_downloaded_message( $location ) {
			return add_query_arg( 'message', 54, $location );
		}

		/**
		 * [post_updated_messages description]
		 * @param  [type] $messages [description]
		 * @return [type]           [description]
		 */
		public static function post_updated_messages( $messages ) {
			$messages['shop_order'][51] =  __( 'PDF invoice emailed to customer.', 'woocommerce-pdf-invoice' );
			$messages['shop_order'][52] =  __( 'PDF invoice deleted.', 'woocommerce-pdf-invoice' );
			$messages['shop_order'][53] =  __( 'PDF invoice created manually.', 'woocommerce-pdf-invoice' );
			$messages['shop_order'][54] =  __( 'PDF invoice downloaded.', 'woocommerce-pdf-invoice' );
			return $messages;
		}


	} // EOF WC_pdf_order_meta_box

	$GLOBALS['WC_pdf_order_meta_box'] = new WC_pdf_order_meta_box();
