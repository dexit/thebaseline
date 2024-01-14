<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'  ;
}

class FPRewardSystemSendpointTabList extends WP_List_Table {

	public function __construct() {
		global $status, $page ;
		parent::__construct( array(
			'singular' => 'send_application',
			'plural'   => 'send_applications',
			'ajax'     => true,
		) ) ;
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ] ;
	}

	public function column_userloginname( $item ) {

		$page = isset($_REQUEST[ 'page' ]) ?wc_clean(wp_unslash($_REQUEST[ 'page' ])):'' ;
		$tab = isset($_REQUEST[ 'tab' ]) ?wc_clean(wp_unslash($_REQUEST[ 'tab' ])):'' ;
		$section = isset($_REQUEST[ 'section' ]) ?wc_clean(wp_unslash($_REQUEST[ 'section' ])):'' ;
				
		if ( 'Paid' == $item[ 'status' ] ) {
			//Build row actions
			$actions = array(
				'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , $page , $tab , $section , 'send_application_delete' , $item[ 'id' ] ),
					) ;

			//Return the title contents
			return sprintf( '%1$s %3$s' ,
					/* $1%s */ $item[ 'userloginname' ] ,
					/* $2%s */ $item[ 'id' ] ,
					/* $3%s */ $this->row_actions( $actions )
					) ;
		} elseif ( 'Rejected' == $item[ 'status' ] ) {
			//Build row actions
			$actions = array(
				'delete' => sprintf( '<a href="?page=%s&tab=%s&action=%s&id=%s&section=%s">Delete</a>' , $page , $tab , $section, 'delete' , $item[ 'id' ] ),
					) ;
			return sprintf( '%1$s %3$s' ,
					/* $1%s */ $item[ 'userloginname' ] ,
					/* $2%s */ $item[ 'id' ] ,
					/* $3%s */ $this->row_actions( $actions )
					) ;
		} else {
			$actions = array(
				'accept' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Approve</a>' , $page , $tab , $section , 'accept' , $item[ 'id' ] ),
				'reject' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Reject</a>' , $page , $tab , $section , 'reject' , $item[ 'id' ] ),
					) ;
			//Return the title contents
			return sprintf( '%1$s %3$s' ,
					/* $1%s */ $item[ 'userloginname' ] ,
					/* $2%s */ $item[ 'id' ] ,
					/* $3%s */ $this->row_actions( $actions )
					) ;
		}
	}

	public function column_cb( $item ) {
		return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />' , $item[ 'id' ]
				) ;
	}

	public function get_columns() {
		$columns = array(
			'cb'                  => '<input type="checkbox" />', //Render a checkbox instead of text            
			'userloginname'       => __( 'Sent by' , 'rewardsystem' ),
			'selecteduser'        => __( 'Received by' , 'rewardsystem' ),
			'pointstosend'        => __( 'Points' , 'rewardsystem' ),
			'sendercurrentpoints' => __( 'Current user Points' , 'rewardsystem' ),
			'status'              => __( 'Request Status' , 'rewardsystem' ),
			'date'                => __( 'Requested date' , 'rewardsystem' ),
				) ;
		return $columns ;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'userloginname'       => array( 'userloginname', false ), //true means it's already sorted            
			'selecteduser'        => array( 'selecteduser', false ),
			'pointstosend'        => array( 'pointstosend', false ),
			'sendercurrentpoints' => array( 'sendercurrentpoints', false ),
			'status'              => array( 'status', false ),
			'date'                => array( 'date', false ),
				) ;
		return $sortable_columns ;
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete' , 'rewardsystem' ),
			'rspaid' => __( 'Mark as Approve' , 'rewardsystem' ),
			'rsdue'  => __( 'Mark as Reject' , 'rewardsystem' ),
				) ;
		return $actions ;
	}

	public function process_bulk_action() {
		global $wpdb ;
				$db = &$wpdb;
		$table_name = $db->prefix . 'sumo_reward_send_point_submitted_data' ; // do not forget about tables prefix
		$ids        = isset( $_REQUEST[ 'id' ] ) ? absint($_REQUEST[ 'id' ]) : array() ;
		$ids        = srp_check_is_array( $ids ) ? $ids : ( empty( $_REQUEST[ 'id' ] ) ? array() : explode( ',' , $ids ) ) ;

		if ( ! srp_check_is_array( $ids ) ) {
			return ;
		}

		if ( 'send_application_delete' === $this->current_action() ) {
			foreach ( $ids as $eachid ) {
				$user_ids = $db->get_results( $db->prepare( 'SELECT selecteduser,id,userid,pointstosend FROM %s WHERE id = %d' , $table_name, $eachid ) , ARRAY_A ) ;
				if ( ! srp_check_is_array( $user_ids ) ) {
					continue ;
				}

				foreach ( $user_ids as $value ) {
					$user_id     = $value[ 'userid' ] ;
					$updatoption = $value[ 'id' ] . 'sendpointupadate' ;
					if ( '1' == get_user_meta( $user_id , $updatoption , true ) ) {
						continue ;
					}

					$table_args   = array(
						'user_id'           => $user_id,
						'pointstoinsert'    => $value[ 'pointstosend' ],
						'checkpoints'       => 'SEP',
						'totalearnedpoints' => $value[ 'pointstosend' ],
							) ;
					$senderinfo   = get_userdata( $user_id ) ;
					$receiverinfo = get_userdata( $value[ 'selecteduser' ] ) ;
					$this->rs_confirmation_mail_and_admin_mail_for_Sendpoints( 'Deleted' , $senderinfo , $receiverinfo , $value[ 'pointstosend' ] ) ;
					RSPointExpiry::insert_earning_points( $table_args ) ;
					RSPointExpiry::record_the_points( $table_args ) ;
					update_user_meta( $user_id , $updatoption , '1' ) ;
				}
			}
			$idstodelete = implode( ',' , $ids ) ;
			$db->query( "DELETE FROM $table_name WHERE id IN($idstodelete)" ) ;
		} elseif ( 'rspaid' === $this->current_action() || 'accept' === $this->current_action() ) {
			$countids = count( $ids ) ;
			foreach ( $ids as $eachid ) {
				$db->update( $table_name , array( 'status' => 'Paid' ) , array( 'id' => $eachid ) ) ;
				$user_ids = $db->get_results( $db->prepare( "SELECT selecteduser,id,userid,pointstosend FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
				if ( ! srp_check_is_array( $user_ids ) ) {
					continue ;
				}

				foreach ( $user_ids as $value ) {
					$user_id      = $value[ 'selecteduser' ] ;
					$senduser     = $value[ 'userid' ] ;
					$senderinfo   = get_userdata( $senduser ) ;
					$receiverinfo = get_userdata( $user_id ) ;
					$table_args   = array(
						'user_id'           => $user_id,
						'pointstoinsert'    => $value[ 'pointstosend' ],
						'checkpoints'       => 'SP',
						'totalearnedpoints' => $value[ 'pointstosend' ],
						'nomineeid'         => $senduser,
							) ;
					$this->rs_confirmation_mail_and_admin_mail_for_Sendpoints( 'Accepted' , $senderinfo , $receiverinfo , $value[ 'pointstosend' ] ) ;
					RSPointExpiry::insert_earning_points( $table_args ) ;
					RSPointExpiry::record_the_points( $table_args ) ;

					// Log to be record for Sender after Admin Approval
					$table_args = array(
						'user_id'           => $senduser,
						'usedpoints'        => $value[ 'pointstosend' ],
						'checkpoints'       => 'SPA',
						'totalearnedpoints' => $value[ 'pointstosend' ],
						'nomineeid'         => $user_id,
							) ;
					RSPointExpiry::record_the_points( $table_args ) ;
				}
			}
			$message = __( $countids . ' Status Changed to Paid' , 'rewardsystem' ) ;
			if ( ! empty( $message ) ) :
				?>
				<div id="message" class="updated"><p><?php echo esc_html($message); ?></p></div>
				<?php
			endif ;
		} elseif ( 'reject' === $this->current_action() ) {
			$countids = count( $ids ) ;
			foreach ( $ids as $eachid ) {
				$db->update( $table_name , array( 'status' => 'Rejected' ) , array( 'id' => $eachid ) ) ;
				$message  = __( $countids . ' Status Changed to Rejected' , 'rewardsystem' ) ;
				$user_ids = $db->get_results( $db->prepare( "SELECT selecteduser,id,userid,pointstosend FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
				if ( ! srp_check_is_array( $user_ids ) ) {
					continue ;
				}

				foreach ( $user_ids as $value ) {
					$user_id     = $value[ 'userid' ] ;
					$updatoption = $value[ 'id' ] . 'sendpointupadate' ;
					if ( '1' == get_user_meta( $user_id , $updatoption , true ) ) {
						continue ;
					}

					$senderinfo   = get_userdata( $user_id ) ;
					$receiverinfo = get_userdata( $value[ 'selecteduser' ] ) ;
					$this->rs_confirmation_mail_and_admin_mail_for_Sendpoints( 'Rejected' , $senderinfo , $receiverinfo , $value[ 'pointstosend' ] ) ;
					$table_args   = array(
						'user_id'           => $user_id,
						'pointstoinsert'    => $value[ 'pointstosend' ],
						'checkpoints'       => 'SEP',
						'totalearnedpoints' => $value[ 'pointstosend' ],
							) ;
					RSPointExpiry::insert_earning_points( $table_args ) ;
					RSPointExpiry::record_the_points( $table_args ) ;
					update_user_meta( $user_id , $updatoption , '1' ) ;
				}
			}
			if ( ! empty( $message ) ) :
				?>
				<div id="message" class="updated"><p><?php echo esc_html($message); ?></p></div>
				<?php
			endif ;
		} elseif ( 'delete' === $this->current_action() ) {
			$countids = count( $ids ) ;
			foreach ( $ids as $eachid ) {
				$user_ids = $db->get_results( $db->prepare( "SELECT selecteduser,id,userid,pointstosend FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
				if ( ! srp_check_is_array( $user_ids ) ) {
					continue ;
				}

				foreach ( $user_ids as $value ) {
					$user_id     = $value[ 'userid' ] ;
					$updatoption = $value[ 'id' ] . 'sendpointupadate' ;
					if ( '1'  == get_user_meta( $user_id , $updatoption , true )) {
						continue ;
					}

					$senderinfo   = get_userdata( $user_id ) ;
					$receiverinfo = get_userdata( $value[ 'selecteduser' ] ) ;
					$this->rs_confirmation_mail_and_admin_mail_for_Sendpoints( 'Deleted' , $senderinfo , $receiverinfo , $value[ 'pointstosend' ] ) ;
					$table_args   = array(
						'user_id'           => $user_id,
						'pointstoinsert'    => $value[ 'pointstosend' ],
						'checkpoints'       => 'SEP',
						'totalearnedpoints' => $value[ 'pointstosend' ],
							) ;
					RSPointExpiry::insert_earning_points( $table_args ) ;
					RSPointExpiry::record_the_points( $table_args ) ;
					update_user_meta( $user_id , $updatoption , '1' ) ;
				}
			}
			$idstodelete = implode( ',' , $ids ) ;
			$db->query( "DELETE FROM $table_name WHERE id IN($idstodelete)" ) ;
			if ( ! empty( $message ) ) :
				?>
				<div id="message" class="updated"><p><?php echo esc_html($message); ?></p></div>
				<?php
			endif ;
		} else {
			$countids = count( $ids ) ;
			foreach ( $ids as $eachid ) {
				$db->update( $table_name , array( 'status' => 'Due' ) , array( 'id' => $eachid ) ) ;
				$message = __( $countids . ' Status Changed to Due' , 'rewardsystem' ) ;
			}
			if ( ! empty( $message ) ) :
				?>
				<div id="message" class="updated"><p><?php echo esc_html($message); ?></p></div>
				<?php
			endif ;
		}
		$redirect = remove_query_arg( array( 'action', 'id' ) , get_permalink() ) ;
		wp_safe_redirect( $redirect ) ;
		exit ;
	}

	private function table_data( $startpoint, $perPage ) {
		global $wpdb ;
		$data       = array() ;
		$orderby               = ( isset( $_REQUEST[ 'orderby' ] ) && in_array( sanitize_text_field($_REQUEST[ 'orderby' ]) , array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_text_field($_REQUEST[ 'orderby' ] ): 'id' ;
		$order                 = ( isset( $_REQUEST[ 'order' ] ) && in_array( sanitize_text_field($_REQUEST[ 'order' ]) , array( 'asc', 'desc' ) ) ) ? sanitize_text_field($_REQUEST[ 'order' ]) : 'asc' ;

				$query_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_send_point_submitted_data ORDER BY %s %s LIMIT %d OFFSET %d", $orderby, $order, $perPage , $startpoint ) , ARRAY_A ) ;
		$i          = 1 ;
		foreach ( $query_data as $user ) {
			$sender      = $user[ 'userid' ] ;
			$sender_info = get_user_by( 'id' , $sender ) ;
			if ( ! empty( $sender_info ) && is_object( $sender_info ) ) {
				$customer_info = $sender_info->display_name . ' (#' . $sender_info->ID . ' - ' . sanitize_email( $sender_info->user_email ) . ')' ;
				$customer      = get_user_by( 'id' , $user[ 'selecteduser' ] ) ;
				if ( is_object( $customer ) ) {
					$reciver_info = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email( $customer->user_email ) . ')' ;
					$data[]       = array(
						'id'                  => $user[ 'id' ],
						'userloginname'       => $customer_info,
						'selecteduser'        => $reciver_info,
						'pointstosend'        => $user[ 'pointstosend' ],
						'sendercurrentpoints' => $user[ 'sendercurrentpoints' ],
						'status'              => $user[ 'status' ],
						'date'                => $user[ 'date' ],
							) ;
					$i++ ;
				}
			}
		}
		return $data ;
	}

	public function prepare_items() {
		global $wpdb ;
		$columns               = $this->get_columns() ;
		$hidden                = array() ;
		$sortable              = $this->get_sortable_columns() ;
		// here we configure table headers, defined in our methods
		$this->_column_headers = array( $columns, $hidden, $sortable ) ;
		$this->process_bulk_action() ;
		// will be used in pagination settings
				$total_items           = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}sumo_reward_send_point_submitted_data" ) ;
		// prepare query params, as usual current page, order by and order direction
		$paged                 = isset( $_REQUEST[ 'paged' ] ) ? max( 0 , intval( $_REQUEST[ 'paged' ] ) - 1 ) : 0 ;
		// [REQUIRED] define $items array
		// notice that last argument is ARRAY_A, so we will retrieve array
		$user                  = get_current_user_id() ;
		$screen                = get_current_screen() ;
		$per_page              = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user , $screen ) ;
		$currentPage           = $this->get_pagenum() ;
		$startpoint            = ( $currentPage - 1 ) * $per_page ;
		$data                  = $this->table_data( $startpoint , $per_page ) ;
		$this->items           = $data ;
		// [REQUIRED] configure pagination
		$this->set_pagination_args( array(
			'total_items' => $total_items, // total items defined above
			'per_page'    => $per_page, // per page constant defined at top of method
			'total_pages' => ceil( $total_items / $per_page ), // calculate pages count
		) ) ;
	}

	public function rs_confirmation_mail_and_admin_mail_for_Sendpoints( $status, $sender_user_info, $receiver_user_info, $returnedpointssss ) {
		$approval_type = get_option( 'rs_request_approval_type' ) ;
		$headers       = '' ;
		global $woocommerce ;
		if ( '1'  == $approval_type ) {
			$sender_name         = is_object( $sender_user_info ) ? $sender_user_info->user_login : '' ;
			$receiver_name       = is_object( $receiver_user_info ) ? $receiver_user_info->user_login : '' ;
			$sender_first_name   = is_object( $sender_user_info ) ? $sender_user_info->first_name : '' ;
			$receiver_first_name = is_object( $sender_user_info ) ? $receiver_user_info->first_name : '' ;
			$receiver_mail       = is_object( $receiver_user_info ) ? $receiver_user_info->user_email : '' ;
			$sender_mail_id      = is_object( $sender_user_info ) ? $sender_user_info->user_email : '' ;
			$admin_email_id      = get_option( 'admin_email' ) ;
			$admin_name          = get_bloginfo( 'name' , 'display' ) ;
			if ('yes' ==  get_option( 'rs_mail_for_send_points_confirmation_mail_for_user' ) ) {
				if ( '' != $status && '' != $sender_name && '' != $sender_mail_id && '' != $admin_name && '' != $admin_email_id ) {
					$confirmation_email_subject                = get_option( 'rs_email_subject_for_send_points_confirmation' ) ;
					$email_confirmation_message                = get_option( 'rs_email_message_for_send_points_confirmation' ) ;
					$confirmation_email_message_for_sendpoints = str_replace( '[request]' , $status , str_replace( '[user_name] ' , $sender_name , str_replace( '[points]' , $returnedpointssss , str_replace( '[receiver_name]' , $receiver_name , $email_confirmation_message ) ) ) ) ;
					add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
					ob_start() ;
					wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $confirmation_email_subject ) ) ;
					echo esc_html($confirmation_email_message_for_sendpoints );
					wc_get_template( 'emails/email-footer.php' ) ;
					$woo_temp_msg                              = ob_get_clean() ;
					$message_headers                           = "MIME-Version: 1.0\r\n" ;
					// $message_headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
					$message_headers                           .= "From: \"{$admin_name}\" <{$admin_email_id}>\n" . 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n" ;
					$message_headers                           .= 'Reply-To: ' . $sender_name . ' <' . $sender_name . ">\r\n" ;
					FPRewardSystem::$rs_from_name              = $admin_name ;
					add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
					if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) {
						wp_mail( $sender_mail_id , $confirmation_email_subject , $confirmation_email_message_for_sendpoints , $message_headers ) ;
					} else {
						$mailer = WC()->mailer() ;
						$mailer->send( $sender_mail_id , $confirmation_email_subject , $woo_temp_msg , $message_headers ) ;
					}
					remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
				}
			}
			if ( 'yes' == get_option( 'rs_mail_for_send_points_for_user' ) ) {
				$Reason = get_option( 'rs_reason_for_send_points_mail' ) ;
				if ( '' != $sender_name && '' != $returnedpointssss && '' != $status ) {
					$email_subject = get_option( 'rs_email_subject_for_send_points' ) ;
					$email_message = get_option( 'rs_email_message_for_send_points' ) ;
					$message       = str_replace( '[rs_sendpoints]' , $returnedpointssss , str_replace( '[specific_user]' , $sender_name , str_replace( '[user_name]' , $receiver_name , $email_message ) ) ) ;
					$Email_message = str_replace( '[status]' , $status , str_replace( '[reason_message]' , $Reason , $message ) ) ;
					$Email_message = str_replace( '[rsfirstname]' , $receiver_name , str_replace( '[rslastname]' , $receiver_last_name , $Email_message ) ) ;
					$Email_message = do_shortcode( $Email_message ) ;
					add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;

					ob_start() ;
					wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
					echo esc_html($Email_message) ;
					wc_get_template( 'emails/email-footer.php' ) ;
					$woo_temp_msg                 = ob_get_clean() ;
					$message_headers              = "MIME-Version: 1.0\r\n" ;
					// $message_headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
					$message_headers              .= "From: \"{$sender_name}\" <{$sender_mail_id}>\n" . 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n" ;
					$message_headers              .= 'Reply-To: ' . $receiver_name . ' <' . $receiver_mail . ">\r\n" ;
					FPRewardSystem::$rs_from_name = $sender_name ;
					add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
					if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) {
						wp_mail( $receiver_mail , $email_subject , $Email_message , $message_headers );
					} else {
						$mailer = WC()->mailer() ;
						$mailer->send( $receiver_mail , $email_subject , $woo_temp_msg , $message_headers ) ;
					}
					remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 1 ) ;
				}
			}
		}
	}
}
