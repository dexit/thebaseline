<?php
/*
 * Message Tab Setting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSMessage' ) ) {

	class RSMessage {

		public static function init() {
			add_action( 'woocommerce_rs_settings_tabs_fprsmessage' , array( __CLASS__, 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmessage' , array( __CLASS__, 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'rs_default_settings_fprsmessage' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'fp_action_to_reset_settings_fprsmessage' , array( __CLASS__, 'reset_message_tab' ) ) ;

			add_action( 'woocommerce_admin_field_uploader' , array( __CLASS__, 'rs_add_upload_your_gift_voucher' ) ) ;

			add_action( 'woocommerce_admin_field_reward_table_sorting' , array( __CLASS__, 'reward_table_sorting' ) ) ;

			add_action( 'rs_display_save_button_fprsmessage' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fprsmessage' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;

			if ( class_exists( 'SUMOPaymentPlans' ) ) {
				add_filter( 'woocommerce_fprsmessage_settings' , array( __CLASS__, 'add_custom_field_messages_for_paymentplan' ) ) ;
			}

			if ( class_exists( 'SUMO_Bookings' ) ) {
				add_filter( 'woocommerce_fprsmessage_settings' , array( __CLASS__, 'add_custom_field_messages_for_sumo_bookings' ) ) ;
			}

			if ( class_exists( 'FPWaitList' ) ) {
				add_filter( 'woocommerce_fprsmessage_settings' , array( __CLASS__, 'add_custom_field_messages_for_waitlist' ) ) ;
			}
		}

		public static function add_custom_field_messages_for_waitlist( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_single__product_page_msg' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
										$updated_settings[] = array(
						'type'   => 'title',
						'id'     => 'rs_sumo_waitlist_section',
						'desc'   => __('<h3>SUMO Waitlist Message Settings</h3><br><br>', 'rewardsystem'),
										) ;
										$updated_settings[] = array(
										'name'    => __( 'Show/Hide Earn Point(s) Message for SUMO WaitList' , 'rewardsystem' ),
										'id'      => 'rs_show_hide_message_for_waitlist',
										'std'     => '1',
										'default' => '1',
										'newids'  => 'rs_show_hide_message_for_waitlist',
										'type'    => 'select',
										'options' => array(
										'1' => __( 'Show' , 'rewardsystem' ),
										'2' => __( 'Hide' , 'rewardsystem' ),
										),
										) ;
										$updated_settings[] = array(
										'name'    => __( 'Earn Point(s) Message for Subscribing Product' , 'rewardsystem' ),
										'id'      => 'rs_message_for_subscribing_product',
										'std'     => 'Earn [subscribingpoints] points for Subscribing this Product',
										'default' => 'Earn [subscribingpoints] points for Subscribing this Product',
										'type'    => 'textarea',
										'newids'  => 'rs_message_for_subscribing_product',
										) ;
										$updated_settings[] = array(
										'type' => 'sectionend',
										'id'   => 'rs_sumo_waitlist_section',
										) ;
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		public static function add_custom_field_messages_for_sumo_bookings( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_single__product_page_msg' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Earn Point(s) Message for Booking Product' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_booking_product',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_booking_product',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for Booking Product' , 'rewardsystem' ),
						'id'      => 'rs_message_for_booking_product',
						'std'     => 'By purchasing this product you can earn reward points, the Points information will be displayed in Cart.',
						'default' => 'By purchasing this product you can earn reward points, the Points information will be displayed in Cart.',
						'type'    => 'textarea',
						'newids'  => 'rs_message_for_booking_product',
							) ;
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		public static function add_custom_field_messages_for_paymentplan( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {

				if ( isset( $section[ 'id' ] ) && '_rs_paymentplans_message_settings' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {

					$updated_settings[] = array(
						'type'   => 'title',
						'id'     => 'rs_cart_page_payment_plan',
						'newids' => 'rs_cart_page_payment_plan',
						'desc'   => __('<h3>Cart Page Message Settings</h3><br><br>', 'rewardsystem'),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Earn Point(s) Message for each Product' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_each_payment_plan_products',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_each_payment_plan_products',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for each Product for SUMO Payment Plan Products' , 'rewardsystem' ),
						'id'      => 'rs_message_payment_plan_product_in_cart',
						'std'     => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue]). Points will be added to the account after receiving Final Payment.',
						'default' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue]). Points will be added to the account after receiving Final Payment.',
						'type'    => 'textarea',
						'newids'  => 'rs_message_payment_plan_product_in_cart',
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Earn Point(s) Message for each Product (Buying Reward Points)' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_buy_point_message_for_each_payment_plan_products',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_buy_point_message_for_each_payment_plan_products',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for each Product (Buying Reward Points) for SUMO Payment Plan Products' , 'rewardsystem' ),
						'id'      => 'rs_buy_point_message_payment_plan_product_in_cart',
						'std'     => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points. Points will be added to the account after receiving Final Payment.',
						'default' => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points. Points will be added to the account after receiving Final Payment.',
						'type'    => 'textarea',
						'newids'  => 'rs_buy_point_message_payment_plan_product_in_cart',
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Total Points that can be Earned for referral' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_total_payment_plan_points_referral',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_total_payment_plan_points_referral',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for each Product (Referral Reward Points) for SUMO Payment Plan Products' , 'rewardsystem' ),
						'id'      => 'rs_referral_point_message_payment_plan_product_in_cart',
						'std'     => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[rs_referral_payment_plan]</strong> reward points. The Points will be credited once the Final Payment is made',
						'default' => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[rs_referral_payment_plan]</strong> reward points. The Points will be credited once the Final Payment is made',
						'type'    => 'textarea',
						'newids'  => 'rs_referral_point_message_payment_plan_product_in_cart',
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Total Points that can be Earned' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_total_payment_plan_points',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_total_payment_plan_points',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Message for Total Points that can be Earned' , 'rewardsystem' ),
						'id'      => 'rs_message_payment_plan_total_price_in_cart',
						'std'     => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue]). [rs_points_on_hold] Points will be added to the account after receiving the Final Payment.',
						'default' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue]). [rs_points_on_hold] Points will be added to the account after receiving the Final Payment.',
						'type'    => 'textarea',
						'newids'  => 'rs_message_payment_plan_total_price_in_cart',
							) ;
					$updated_settings[] = array(
						'type' => 'sectionend',
						'id'   => 'rs_cart_page_payment_plan',
							) ;
					$updated_settings[] = array(
						'type' => 'title',
						'desc' => __('<h3>Checkout Page Message Settings</h3><br><br>', 'rewardsystem'),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Earn Point(s) Message for each Product' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_each_payment_plan_products_checkout_page',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_each_payment_plan_products_checkout_page',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for each Product for SUMO Payment Plan Products' , 'rewardsystem' ),
						'id'      => 'rs_message_payment_plan_product_in_checkout',
						'std'     => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue]). Points will be added to the account after receiving Final Payment.',
						'default' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue]). Points will be added to the account after receiving Final Payment.',
						'type'    => 'textarea',
						'newids'  => 'rs_message_payment_plan_product_in_checkout',
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Earn Point(s) Message for each Product (Buying Reward Points)' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_buy_point_message_for_each_payment_plan_products_checkout_page',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_buy_point_message_for_each_payment_plan_products_checkout_page',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for each Product (Buying Reward Points) for SUMO Payment Plan Products' , 'rewardsystem' ),
						'id'      => 'rs_buy_point_message_payment_plan_product_in_checkout',
						'std'     => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points. Points will be added to the account after receiving Final Payment.',
						'default' => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points. Points will be added to the account after receiving Final Payment.',
						'type'    => 'textarea',
						'newids'  => 'rs_buy_point_message_payment_plan_product_in_checkout',
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Total Points that can be Earned for referral' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_total_payment_plan_points_referrel_checkout',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_total_payment_plan_points_referrel_checkout',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Earn Point(s) Message for each Product (Referral Reward Points) for SUMO Payment Plan Products' , 'rewardsystem' ),
						'id'      => 'rs_referral_point_message_payment_plan_product_in_checkout',
						'std'     => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[rs_referral_payment_plan]</strong> reward points. The Points will be credited once the Final Payment is made',
						'default' => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[rs_referral_payment_plan]</strong> reward points. The Points will be credited once the Final Payment is made',
						'type'    => 'textarea',
						'newids'  => 'rs_referral_point_message_payment_plan_product_in_checkout',
							) ;

					$updated_settings[] = array(
						'name'    => __( 'Show/Hide Total Points that can be Earned' , 'rewardsystem' ),
						'id'      => 'rs_show_hide_message_for_total_payment_plan_points_checkout_page',
						'std'     => '1',
						'default' => '1',
						'newids'  => 'rs_show_hide_message_for_total_payment_plan_points_checkout_page',
						'type'    => 'select',
						'options' => array(
							'1' => __( 'Show' , 'rewardsystem' ),
							'2' => __( 'Hide' , 'rewardsystem' ),
						),
							) ;
					$updated_settings[] = array(
						'name'    => __( 'Message for Total Points that can be Earned' , 'rewardsystem' ),
						'id'      => 'rs_message_payment_plan_total_price_in_checkout',
						'std'     => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue]). [rs_points_on_hold] Points will be added to the account after receiving the Final Payment.',
						'default' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue]). [rs_points_on_hold] Points will be added to the account after receiving the Final Payment.',
						'type'    => 'textarea',
						'newids'  => 'rs_message_payment_plan_total_price_in_checkout',
							) ;
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
						/**
						 * Hook:woocommerce_fprsmessage_settings.
						 * 
						 * @since 1.0
						 */
			return apply_filters( 'woocommerce_fprsmessage_settings' , array(
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Shop and Category Page Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_shop_page_msg',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_for_simple_product',
					'desc' => __('<h3>Product Purchase Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Simple Products - Logged in Users' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_message_for_simple_in_shop',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_message_for_simple_in_shop',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_simple_in_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_simple_in_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_message_in_shop_page_for_simple',
					'std'     => 'Earn [rewardpoints] Reward Points',
					'default' => 'Earn [rewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_in_shop_page_for_simple',
				),
				array(
					'name'    => __( 'Position to display the Earn Points Message for Simple Products' , 'rewardsystem' ),
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'id'      => 'rs_message_position_for_simple_products_in_shop_page',
					'newids'  => 'rs_message_position_for_simple_products_in_shop_page',
					'options' => array(
						'1' => __( 'Before Product Price' , 'rewardsystem' ),
						'2' => __( 'After Product Price' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_for_simple_product',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_for_variable_product',
					'desc' => __('<h3>Product Purchase Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'   => __( 'Show/Hide Earn Point(s) Message for Variable Products' , 'rewardsystem' ),
					'id'     => 'rs_enable_display_earn_message_for_variation',
					'type'   => 'checkbox',
					'newids' => 'rs_enable_display_earn_message_for_variation',
					'desc'   => __( 'Enable this checkbox to display the points to earn for first created variation on shop page' , 'rewardsystem' ),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Variable Products - Logged in Users' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_message_for_variable_in_shop',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_message_for_variable_in_shop',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Variable Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_variable_in_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_variable_in_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variable Products' , 'rewardsystem' ),
					'id'      => 'rs_message_in_shop_page_for_variable',
					'std'     => 'Earn [variationrewardpoints] Reward Points',
					'default' => 'Earn [variationrewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_in_shop_page_for_variable',
				),
				array(
					'name'    => __( 'Position to display the Earn Points Message for Variable Products' , 'rewardsystem' ),
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'id'      => 'rs_msg_position_for_var_products_in_shop_page',
					'newids'  => 'rs_msg_position_for_var_products_in_shop_page',
					'options' => array(
						'1' => __( 'Before Product Price' , 'rewardsystem' ),
						'2' => __( 'After Product Price' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_for_variable_product',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_cart_total_based_product_purchase',
					'desc' => __('<h3>Product Purchase Points - Based on Cart Total</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Product Purchase based on Cart Total[Fixed Reward Points]' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_shop',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_shop',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_fixed_cart_total_based_product_purchase_in_shop',
					'std'     => 'By purchasing the below-listed products, you can earn a fixed amount of points. You will come to know the earn points information once you add the product(s) to cart.',
					'default' => 'By purchasing the below-listed products, you can earn a fixed amount of points. You will come to know the earn points information once you add the product(s) to cart.',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_fixed_cart_total_based_product_purchase_in_shop',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Product Purchase based on Cart Total[Percentage of Cart Total]' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_percent_cart_total_based_product_purchase_in_shop',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_percent_cart_total_based_product_purchase_in_shop',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_percent_cart_total_based_product_purchase_in_shop',
					'std'     => 'By purchasing the below-listed product(s), you can earn points based on a percentage of cart total. You will come to know the earn points information once you add the product(s) to cart.',
					'default' => 'By purchasing the below-listed product(s), you can earn points based on a percentage of cart total. You will come to know the earn points information once you add the product(s) to cart.',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_percent_cart_total_based_product_purchase_in_shop',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_cart_total_based_product_purchase',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_point_notice_for_simple_product',
					'desc' => __('<h3>Buying Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Simple Products - Logged in Users (Buying Reward Points)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_buy_points_message_for_simple_in_shop',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_buy_points_message_for_simple_in_shop',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products - Guests (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_pont_message_for_simple_in_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_pont_message_for_simple_in_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_in_shop_page_for_simple',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_in_shop_page_for_simple',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_point_notice_for_simple_product',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_point_notice_for_variable_product',
					'desc' => __('<h3>Buying Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Variable Products - Logged in Users (Buying Reward Points)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_buy_points_message_for_variable_in_shop',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_buy_points_message_for_variable_in_shop',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Variable Products - Guests (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_pont_message_for_variable_in_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_pont_message_for_variable_in_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variable Products (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_in_shop_page_for_variable',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_in_shop_page_for_variable',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_point_notice_for_variable_product',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_notice_for_out_of_stock_product',
					'desc' => __('<h3>Out of Stock Product - Simple/Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Out of Stock Products (Applicable for Simple and Variable products )' , 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_message_for_outofstock',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_message_for_outofstock',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_notice_for_out_of_stock_product',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_gift_icon_for_product',
					'desc' => __('<h3>Gift Icon Uploader - Simple/Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Enable Gift Icon Uploader' , 'rewardsystem' ),
					'id'      => '_rs_enable_disable_gift_icon',
					'std'     => '1',
					'default' => '1',
					'newids'  => '_rs_enable_disable_gift_icon',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Enable' , 'rewardsystem' ),
						'2' => __( 'Disable' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'uploader',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_gift_icon_for_product',
				),
				array(
					'type' => 'sectionend',
					'id'   => '_rs_shop_page_msg',
				),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Custom Page Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_custom_shop_page_msg',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_pp_points_for_simple_product_in_custom_page',
					'desc' => __('<h3>Product Purchase Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Simple Products - Logged in Users' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_message_for_simple_in_custom_shop',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_message_for_simple_in_custom_shop',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_simple_in_custom_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_simple_in_custom_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_message_in_custom_shop_page_for_simple',
					'std'     => 'Earn [rewardpoints] Reward Points',
					'default' => 'Earn [rewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_in_custom_shop_page_for_simple',
				),
				array(
					'name'    => __( 'Position to display the Earn Points Message for Simple Products' , 'rewardsystem' ),
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'id'      => 'rs_message_position_for_simple_products_in_custom_shop_page',
					'newids'  => 'rs_message_position_for_simple_products_in_custom_shop_page',
					'options' => array(
						'1' => __( 'Before Product Price' , 'rewardsystem' ),
						'2' => __( 'After Product Price' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_pp_points_for_simple_product_in_custom_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_pp_points_for_variable_product_in_custom_page',
					'desc' => __('<h3>Product Purchase Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'   => __( 'Show/Hide Earn Point(s) Message for Variable Products' , 'rewardsystem' ),
					'id'     => 'rs_enable_display_earn_message_for_variation_custom_shop',
					'type'   => 'checkbox',
					'newids' => 'rs_enable_display_earn_message_for_variation_custom_shop',
					'desc'   => __( 'Enable this checkbox to display the points to earn for first created variation on shop page' , 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variations of Variable Product' , 'rewardsystem' ),
					'id'      => 'rs_message_for_custom_shop_variation',
					'std'     => 'Earn [variationrewardpoints] Reward Points',
					'default' => 'Earn [variationrewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_custom_shop_variation',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_pp_points_for_variable_product_in_custom_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_points_for_simple_product_in_custom_page',
					'desc' => __('<h3>Buying Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Simple Products - Logged in Users (Buying Reward Points)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_buy_points_message_for_simple_in_custom',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_buy_points_message_for_simple_in_custom',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products - Guests (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_simple_in_custom_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_simple_in_custom_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_in_custom_shop_page_for_simple',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_in_custom_shop_page_for_simple',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_points_for_simple_product_in_custom_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_points_for_variable_product_in_custom_page',
					'desc' => __('<h3>Buying Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Variable Products - Logged in Users (Buying Reward Points)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_buy_points_message_for_variable_in_custom_shop',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_buy_points_message_for_variable_in_custom_shop',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Variable Products - Guests (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_variable_in_custom_shop_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_variable_in_custom_shop_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variable Products (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_in_custom_shop_page_for_variable',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_in_custom_shop_page_for_variable',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_points_for_variable_product_in_custom_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_custom_shop_page_for_out_of_stock_product',
					'desc' => __('<h3>Out of Stock Product - Custom Page</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Out of Stock Products (Applicable for Simple and Variable products )' , 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_message_for_customshop',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_message_for_customshop',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_custom_shop_page_for_out_of_stock_product',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_custom_taxonomy_options',
					'desc' => __('<h3>Product Purchase Points - Custom Taxonomies</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Select the Taxonomies' , 'rewardsystem' ),
					'id'      => 'rs_messages_in_taxonomy_page',
					'std'     => '',
					'default' => '',
					'class'   => 'srp-rp-product-include-taxonomy',
					'newids'  => 'rs_messages_in_taxonomy_page',
					'type'    => 'multiselect',
					'options' => fp_taxonomy_pages(),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_custom_taxonomy_options',
				),
				array(
					'type' => 'sectionend',
					'id'   => '_rs_custom_shop_page_msg',
				),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Single Product Page Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_single__product_page_msg',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_position_for_notice_in_cart_page',
					'desc' => __('<h3>Message(s) Position in Product Page</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Position to display the points messages' , 'rewardsystem' ),
					'id'      => 'rs_msg_position_in_product_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_msg_position_in_product_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Before Single Product' , 'rewardsystem' ),
						'2' => __( 'Before Single Product Summary' , 'rewardsystem' ),
						'3' => __( 'Single Product Summary' , 'rewardsystem' ),
						'4' => __( 'After Single Product' , 'rewardsystem' ),
						'5' => __( 'After Single Product Summary' , 'rewardsystem' ),
						'6' => __( 'After Product Meta End' , 'rewardsystem' ),
						'7' => __( 'Before Add to Cart Quantity' , 'rewardsystem' ),
						'8' => __( 'After Add to Cart Quantity' , 'rewardsystem' ),
						'9' => __( 'After Add to Cart Form' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_position_for_notice_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_points_notice_for_simple_product_in_product_page',
					'desc' => __('<h3>Product Purchase Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Simple Products - Logged in Users' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_single_product',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_single_product',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Notice Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_message_for_single_product_point_rule',
					'std'     => 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])',
					'default' => 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_single_product_point_rule',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_single_product_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_single_product_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Notice Message for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_purchase_reward_message_single_product_guest',
					'std'     => 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])',
					'default' => 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])',
					'type'    => 'textarea',
					'newids'  => 'rs_purchase_reward_message_single_product_guest',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_shop_archive_single',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_shop_archive_single',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_message_in_single_product_page',
					'std'     => 'Earn [rewardpoints] Reward Points',
					'default' => 'Earn [rewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_in_single_product_page',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_earn_message_single_product_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_earn_message_single_product_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
								),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_earn_message_single_product_guest',
					'std'     => 'Earn [rewardpoints] Reward Points',
					'default' => 'Earn [rewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_earn_message_single_product_guest',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products in Related Products Field' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_shop_archive_single_related_products',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_shop_archive_single_related_products',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products in Related Products Field' , 'rewardsystem' ),
					'id'      => 'rs_message_in_single_product_page_related_products',
					'std'     => 'Earn [rewardpoints] Reward Points',
					'default' => 'Earn [rewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_in_single_product_page_related_products',
				),
				array(
					'name'    => __( 'Position to display the Earn Points Message for Simple Products' , 'rewardsystem' ),
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'id'      => 'rs_message_position_in_single_product_page_for_simple_products',
					'newids'  => 'rs_message_position_in_single_product_page_for_simple_products',
					'options' => array(
						'1' => __( 'Before Product Price' , 'rewardsystem' ),
						'2' => __( 'After Product Price' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_points_notice_for_simple_product_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_points_notice_for_variable_product_in_product_page',
					'desc' => __('<h3>Product Purchase Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message in Variation Level for Variable Products' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_variable_in_single_product_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_variable_in_single_product_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variations of Variable Product' , 'rewardsystem' ),
					'id'      => 'rs_message_for_single_product_variation',
					'std'     => 'Earn [variationrewardpoints] Reward Points',
					'default' => 'Earn [variationrewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_single_product_variation',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message in Variation Level for Variable Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_variable_in_single_product_page_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_variable_in_single_product_page_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
								),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variations of Variable Product - Guests' , 'rewardsystem' ),
					'id'      => 'rs_earn_message_single_product_variation_guest',
					'std'     => 'Earn [variationrewardpoints] Reward Points',
					'default' => 'Earn [variationrewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_earn_message_single_product_variation_guest',
				),
				array(
					'name'    => __( 'Position to display the Earn Points Message for Variable Products' , 'rewardsystem' ),
					'std'     => '1',
					'default' => '1',
					'type'    => 'select',
					'id'      => 'rs_message_position_in_single_product_page_for_variable_products',
					'newids'  => 'rs_message_position_in_single_product_page_for_variable_products',
					'options' => array(
						'1' => __( 'Before Product Price' , 'rewardsystem' ),
						'2' => __( 'After Product Price' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Variable Products - Logged in Users' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_variable_product',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_variable_product',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Notice Message for Variable Products' , 'rewardsystem' ),
					'id'      => 'rs_message_for_variation_products',
					'std'     => 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])',
					'default' => 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_variation_products',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Variable Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_variation_single_product_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_variation_single_product_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
								),
				),
				array(
					'name'    => __( 'Earn Point(s) Notice Message for Variable Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_message_variation_single_product_guest',
					'std'     => 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])',
					'default' => 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_variation_single_product_guest',
				),
				array(
					'name'   => __( 'Show/Hide Earn Point(s) Message for Variable Products' , 'rewardsystem' ),
					'id'     => 'rs_enable_display_earn_message_for_variation_single_product',
					'type'   => 'checkbox',
					'newids' => 'rs_enable_display_earn_message_for_variation_single_product',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Variable Products in Related Products Field' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_shop_archive_variable_related_products',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_shop_archive_variable_related_products',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variable Products in Related Products Field' , 'rewardsystem' ),
					'id'      => 'rs_message_in_variable_related_products',
					'std'     => 'Earn [variationrewardpoints] Reward Points',
					'default' => 'Earn [variationrewardpoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_in_variable_related_products',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_points_notice_for_variable_product_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_cart_total_based_points_in_product_page',
					'desc' => __('<h3>Product Purchase Points - Based on Cart Total</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Product Purchase based on Cart Total[Fixed Reward Points] - Logged in User' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_fixed_cart_total_based_product_purchase',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_fixed_cart_total_based_product_purchase',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Product Purchase based on Cart Total[Fixed Reward Points] - Guest' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_fixed_cart_total_based_product_purchase',
					'std'     => 'By purchasing this product, you can earn a fixed amount of points. You will come to know the earn points information once you add the product to cart.',
					'default' => 'By purchasing this product, you can earn a fixed amount of points. You will come to know the earn points information once you add the product to cart.',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_fixed_cart_total_based_product_purchase',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Product Purchase based on Cart Total[Percentage of Cart Total] - Logged in User' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_percent_cart_total_based_product_purchase',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_percent_cart_total_based_product_purchase',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Product Purchase based on Cart Total[Percentage of Cart Total] - Guest' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_percent_cart_total_based_product_purchase_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_percent_cart_total_based_product_purchase_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_percent_cart_total_based_product_purchase',
					'std'     => 'By purchasing this product, you can earn points based on a percentage of cart total. You will come to know the earn points information once you add the product to cart.',
					'default' => 'By purchasing this product, you can earn points based on a percentage of cart total. You will come to know the earn points information once you add the product to cart.',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_percent_cart_total_based_product_purchase',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_cart_total_based_points_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_points_notice_for_simple_product_in_product_page',
					'desc' => __('<h3>Buying Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Buying Point(s) Message as Notice for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_single_product',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_single_product',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Buying Point(s) Notice Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_for_single_product_point_rule',
					'std'     => 'Purchase this Product and Earn [buypoints] Reward Points ([buypointvalue])',
					'default' => 'Purchase this Product and Earn [buypoints] Reward Points ([buypointvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_for_single_product_point_rule',
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message for Simple Products - Logged in Users (Buying Reward Points)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_buy_points_message_for_simple_in_product',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_buy_points_message_for_simple_in_product',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Simple Products - Guests (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_simple_in_product_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_simple_in_product_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Simple Products (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_in_product_page_for_simple',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_in_product_page_for_simple',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Buying Points in Related Products Field' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_related_product_buying_point',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_related_product_buying_point',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Buying Points in Related Products Field' , 'rewardsystem' ),
					'id'      => 'rs_message_related_product_buying_point',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_related_product_buying_point',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_points_notice_for_simple_product_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_points_notice_for_variable_product_in_product_page',
					'desc' => __('<h3>Buying Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Buying Point(s) Message as Notice for Variable Products - Logged in Users' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_variable_product',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_variable_product',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Buying Point(s) Notice Message for Variable Products' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_for_variation_products',
					'std'     => 'Purchase this Product and Earn [variationbuyingpoint] Reward Points ([variationbuyingpointvalue])',
					'default' => 'Purchase this Product and Earn [variationbuyingpoint] Reward Points ([variationbuyingpointvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_for_variation_products',
				),
				array(
					'name'     => __( 'Show/Hide Earn Point(s) Message as Notice for Variable Products - Logged in Users (Buying Reward Points)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_show_hide_buy_points_message_for_variable_in_product',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_show_hide_buy_points_message_for_variable_in_product',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message as Notice for Variable Products - Guests (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_variable_in_product_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_variable_in_product_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variations of Variable Product (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_in_product_page_for_variable',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_in_product_page_for_variable',
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Variable Products in Related Products Field (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_related_product_buying_point_variable_product',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_related_product_buying_point_variable_product',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Variable Products in Related Products Field (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_message_related_product_buying_point_variable_product',
					'std'     => 'Earn [buypoints] Reward Points',
					'default' => 'Earn [buypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_related_product_buying_point_variable_product',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_points_notice_for_variable_product_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_referral_points_notice_for_simple_product_in_product_page',
					'desc' => __('<h3>Referral Purchase Points - Simple</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Referral Earn Point(s) Message as Notice for Simple Products - Logged in Users' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_single_product_referral',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_single_product_referral',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Referral Earn Point(s) Message as Notice for Simple Products - Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_single_product_guest_referral',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_single_product_guest_referral',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Referral Earn Point(s) Notice Message for Simple Products' , 'rewardsystem' ),
					'id'      => 'rs_message_for_single_product_point_rule_referral',
					'std'     => 'By Purchasing this Product, Referrer([rsreferredusername]) will earn [rsrefferalpoints] reward points ([referralequalamount])',
					'default' => 'By Purchasing this Product, Referrer([rsreferredusername]) will earn [rsrefferalpoints] reward points ([referralequalamount])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_single_product_point_rule_referral',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_referral_points_notice_for_simple_product_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_referral_points_notice_for_variable_product_in_product_page',
					'desc' => __('<h3>Referral Purchase Points - Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Referral Earn Point(s) Message as Notice for Variable Products - Logged in Users' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_variable_product_referral',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_variable_product_referral',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Referral Earn Point(s) Notice Message for Variable Products' , 'rewardsystem' ),
					'id'      => 'rs_message_for_variation_products_referral',
					'std'     => 'By Purchasing this Product, Referrer([rsreferredusername]) will earn [variationreferralpoints] reward points ([variationreferralpointsamount])',
					'default' => 'By Purchasing this Product, Referrer([rsreferredusername]) will earn [variationreferralpoints] reward points ([variationreferralpointsamount])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_variation_products_referral',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_referral_points_notice_for_variable_product_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_product_review_notice_in_product_page',
					'desc' => __('<h3>Product Review Points</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Product review - Logged in Users' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_product_review',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_product_review',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Product review - Guest User' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_product_review_for_guest_user',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_product_review_for_guest_user',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Product Review' , 'rewardsystem' ),
					'id'      => 'rs_message_for_product_review',
					'std'     => 'Earn [productreviewpoint] Reward Points for Product Review',
					'default' => 'Earn [productreviewpoint] Reward Points for Product Review',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_product_review',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_product_review_notice_in_product_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_out_of_stock_notice_in_product_page',
					'desc' => __('<h3>Out of Stock Product - Simple/Variable</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Message for Out of Stock Products in Single Product Page (Applicable for Simple and Variable products )' , 'rewardsystem' ),
					'id'      => 'rs_message_outofstockproducts_product_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_message_outofstockproducts_product_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_out_of_stock_notice_in_product_page',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_single__product_page_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Cart Page Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_cart_page_msg',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_position_for_notice_in_cart_page',
					'desc' => __('<h3>Message(s) Position in Cart</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Position to display the points messages' , 'rewardsystem' ),
					'id'      => 'rs_message_before_after_cart_table',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_message_before_after_cart_table',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Before' , 'rewardsystem' ),
						'2' => __( 'After' , 'rewardsystem' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_position_for_notice_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_in_cart_page_for_guest',
					'desc' => __('<h3>Earn Point Notice - Guest</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_guest',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_guest',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Guests' , 'rewardsystem' ),
					'id'      => 'rs_message_for_guest_in_cart',
					'std'     => 'Earn Reward Points for Product Purchase, Product Review and Sign up, etc [loginlink]',
					'default' => 'Earn Reward Points for Product Purchase, Product Review and Sign up, etc [loginlink]',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_guest_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_in_cart_page_for_guest',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_in_cart_page',
					'desc' => __('<h3>Product Purchase Points - Based on Product Total</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for each Product' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_each_products',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_each_products',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product' , 'rewardsystem' ),
					'id'      => 'rs_message_product_in_cart',
					'std'     => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
					'default' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_product_in_cart',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_total_points',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_total_points',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message for Total Points that can be Earned' , 'rewardsystem' ),
					'id'      => 'rs_message_total_price_in_cart',
					'std'     => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
					'default' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_total_price_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_pp_point_notice_based_on_cart_total_in_cart_page',
					'desc' => __('<h3>Product Purchase Points - Based on Cart Total</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show Cart Total Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_cart_total_based_points',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_cart_total_based_points',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Cart Total Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_cart_total_based_points',
					'std'     => 'Complete this order and Earn <strong>[carttotalbasedrewardpoints]</strong> Reward Points([equalvalueforcarttotal])',
					'default' => 'Complete this order and Earn <strong>[carttotalbasedrewardpoints]</strong> Reward Points([equalvalueforcarttotal])',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_cart_total_based_points',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_pp_point_notice_based_on_cart_total_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_pp_point_notice_based_on_range_based_in_cart_page',
					'desc' => __('<h3>Product Purchase Points - Based on Range Based</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show Range Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_range_based_points_in_cart',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_range_based_points_in_cart',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Range Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_range_based_points',
					'std'     => 'Complete this order and Earn <strong>[rangebasedrewardpoints]</strong> Reward Points([equalvalueforrangebased])',
					'default' => 'Complete this order and Earn <strong>[rangebasedrewardpoints]</strong> Reward Points([equalvalueforrangebased])',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_range_based_points',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_pp_point_notice_based_on_range_based_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_point_notice_in_cart_page',
					'desc' => __('<h3>Buying Points Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for each Product (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_each_products',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_each_products',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_product_in_cart',
					'std'     => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points ([buypointvalues])',
					'default' => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points ([buypointvalues])',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_product_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_point_notice_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_referral_point_notice_in_cart_page',
					'desc' => __( '<h3>Referral Purchase Points Notice - Based on Product Total</h3><br><br>' , 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referral' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_total_points_referrel',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_total_points_referrel',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Referral Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_referral_point_message_product_in_cart',
					'std'     => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[referralpoints]</strong> reward points',
					'default' => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[referralpoints]</strong> reward points',
					'type'    => 'textarea',
					'newids'  => 'rs_referral_point_message_product_in_cart',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referred User', 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_product_total_referred_msg_in_cart',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_product_total_referred_msg_in_cart',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show', 'rewardsystem' ),
						'2' => __( 'Hide', 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Referred Reward Points)', 'rewardsystem' ),
					'id'      => 'rs_product_total_referred_msg_in_cart',
					'std'     => 'Purchase this product <strong>[titleofproduct]</strong> & earn <strong>[referredpoints]</strong> for getting referred',
					'default' => 'Purchase this product <strong>[titleofproduct]</strong> & earn <strong>[referredpoints]</strong> for getting referred',
					'type'    => 'textarea',
					'newids'  => 'rs_product_total_referred_msg_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_referral_point_notice_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_referral_point_notice_in_cart_page_based_cart_total',
					'desc' => '<h3>Referral Purchase Points Notice - Based on Cart Total</h3><br><br>',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referral', 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_cart_total_points_referrel_cart',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_cart_total_points_referrel_cart',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show', 'rewardsystem' ),
						'2' => __( 'Hide', 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for order (Referral Reward Points)', 'rewardsystem' ),
					'id'      => 'rs_referer_point_message_cart_total_based_in_cart_page',
					'std'     => 'By completing this order, Referrer([rsreferredusername]) will earn <strong>[referrerpoints]</strong> reward points',
					'default' => 'By completing this order, Referrer([rsreferredusername]) will earn <strong>[referrerpoints]</strong> reward points',
					'type'    => 'textarea',
					'newids'  => 'rs_referer_point_message_cart_total_based_in_cart_page',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referred', 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_cart_total_referred_msg_in_cart',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_cart_total_referred_msg_in_cart',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show', 'rewardsystem' ),
						'2' => __( 'Hide', 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Referred Reward Points)', 'rewardsystem' ),
					'id'      => 'rs_cart_total_referred_msg_in_cart',
					'std'     => 'By Purchasing this order & earn <strong>[referredpoints]</strong> points for getting referred',
					'default' => 'By Purchasing this order & earn <strong>[referredpoints]</strong> points for getting referred',
					'type'    => 'textarea',
					'newids'  => 'rs_cart_total_referred_msg_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_referral_point_notice_in_cart_page_based_cart_total',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_available_point_notice_in_cart_page',
					'desc' => __('<h3>Available Points Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show Available Reward Points before or after Redeemed Points Message' , 'rewardsystem' ),
					'id'      => 'rs_available_pts_before_after_redeemed_pts_cart',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_available_pts_before_after_redeemed_pts_cart',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'After' , 'rewardsystem' ),
						'2' => __( 'Before' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Available Reward Points' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_my_rewards',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_my_rewards',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Available Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_in_cart',
					'std'     => 'My Reward Points [userpoints] ([userpoints_value])',
					'default' => 'My Reward Points [userpoints] ([userpoints_value])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_available_point_notice_in_cart_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_redeemed_point_notice_in_cart_page',
					'desc' => __('<h3>Balance Point Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Redeemed Points Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_redeem_points',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_redeem_points',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Redeemed Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_redeemed_in_cart',
					'std'     => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points ([balanceprice])',
					'default' => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points ([balanceprice])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_redeemed_in_cart',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_redeemed_point_notice_in_cart_page',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_cart_page_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Checkout Page Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_checkout_page_msg',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_in_checkout_page_for_guest',
					'desc' => __('<h3>Earn Point Notice - Guest</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for Guests' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_guest_checkout_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_guest_checkout_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for Guests' , 'rewardsystem' ),
					'id'      => 'rs_message_for_guest_in_checkout',
					'std'     => 'Earn Reward Points for Product Purchase, Product Review and Signup, etc [loginlink]',
					'default' => 'Earn Reward Points for Product Purchase, Product Review and Signup, etc [loginlink]',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_guest_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_in_checkout_page_for_guest',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_in_checkout_page',
					'desc' => __('<h3>Product Purchase Points - Based on Product Total</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for each Product' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_each_products_checkout_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_each_products_checkout_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product' , 'rewardsystem' ),
					'id'      => 'rs_message_product_in_checkout',
					'std'     => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
					'default' => 'Purchase [titleofproduct] and Earn <strong>[rspoint]</strong> Reward Points ([carteachvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_product_in_checkout',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_total_points_checkout_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_total_points_checkout_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message for Total Points that can be Earned' , 'rewardsystem' ),
					'id'      => 'rs_message_total_price_in_checkout',
					'std'     => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
					'default' => 'Complete the Purchase and Earn <strong>[totalrewards]</strong> Reward Points ([totalrewardsvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_total_price_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_based_on_cart_total_in_checkout_page',
					'desc' => __('<h3>Product Purchase Points - Based on Cart Total</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show Cart Total Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_cart_total_based_points_in_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_cart_total_based_points_in_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Cart Total Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_cart_total_based_points_in_checkout',
					'std'     => 'Complete this order and Earn <strong>[carttotalbasedrewardpoints]</strong> Reward Points([equalvalueforcarttotal])',
					'default' => 'Complete this order and Earn <strong>[carttotalbasedrewardpoints]</strong> Reward Points([equalvalueforcarttotal])',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_cart_total_based_points_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_based_on_range_based_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_earn_point_notice_based_on_range_based_in_checkout_page',
					'desc' => __('<h3>Product Purchase Points - Based on Range Based</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show Range Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_enable_msg_for_range_based_points_in_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_enable_msg_for_range_based_points_in_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Range Based Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_range_based_points_in_checkout',
					'std'     => 'Complete this order and Earn <strong>[rangebasedrewardpoints]</strong> Reward Points([equalvalueforrangebased])',
					'default' => 'Complete this order and Earn <strong>[rangebasedrewardpoints]</strong> Reward Points([equalvalueforrangebased])',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_range_based_points_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_earn_point_notice_based_on_range_based_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_referral_point_notice_in_checkout_page',
					'desc' => __( '<h3>Referral Purchase Points Notice - Based on product total</h3><br><br>' , 'rewardsystem' ),
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referral' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_total_points_referrel_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_total_points_referrel_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Referral Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_referral_point_message_product_in_checkout',
					'std'     => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[referralpoints]</strong> reward points',
					'default' => 'By Purchasing [titleofproduct], Referrer([rsreferredusername]) will earn <strong>[referralpoints]</strong> reward points',
					'type'    => 'textarea',
					'newids'  => 'rs_referral_point_message_product_in_checkout',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referred User', 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_product_total_referred_msg_in_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_product_total_referred_msg_in_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show', 'rewardsystem' ),
						'2' => __( 'Hide', 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Referred Reward Points)', 'rewardsystem' ),
					'id'      => 'rs_product_total_referred_msg_in_checkout',
					'std'     => 'Purchase this product <strong>[titleofproduct]</strong> & earn <strong>[referredpoints]</strong> for getting referred',
					'default' => 'Purchase this product <strong>[titleofproduct]</strong> & earn <strong>[referredpoints]</strong> for getting referred',
					'type'    => 'textarea',
					'newids'  => 'rs_product_total_referred_msg_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_referral_point_notice_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_referral_point_notice_in_checkout_page_based_cart_total',
					'desc' => '<h3>Referral Purchase Points Notice - Based on Cart Total</h3><br><br>',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referral' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_cart_total_points_referrel_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_cart_total_points_referrel_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for order (Referral Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_referer_point_message_cart_total_based_in_checkout_page',
					'std'     => 'By completing this order, Referrer([rsreferredusername]) will earn <strong>[referrerpoints]</strong> reward points',
					'default' => 'By completing this order, Referrer([rsreferredusername]) will earn <strong>[referrerpoints]</strong> reward points',
					'type'    => 'textarea',
					'newids'  => 'rs_referer_point_message_cart_total_based_in_checkout_page',
				),
				array(
					'name'    => __( 'Show/Hide Total Points that can be Earned for referred', 'rewardsystem' ),
					'id'      => 'rs_show_or_hide_cart_total_referred_msg_in_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_or_hide_cart_total_referred_msg_in_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show', 'rewardsystem' ),
						'2' => __( 'Hide', 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Referred Reward Points)', 'rewardsystem' ),
					'id'      => 'rs_cart_total_referred_msg_in_checkout',
					'std'     => 'By Purchasing this order, and earn <strong>[referredpoints]</strong> reward points',
					'default' => 'By Purchasing this order, and earn <strong>[referredpoints]</strong> reward points',
					'type'    => 'textarea',
					'newids'  => 'rs_cart_total_referred_msg_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_referral_point_notice_in_checkout_page_based_cart_total',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_buy_point_notice_in_checkout_page',
					'desc' => __('<h3>Buying Points Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Earn Point(s) Message for each Product (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_buy_point_message_for_each_products_checkout_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_buy_point_message_for_each_products_checkout_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earn Point(s) Message for each Product (Buying Reward Points)' , 'rewardsystem' ),
					'id'      => 'rs_buy_point_message_product_in_checkout',
					'std'     => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points ([buypointvalue])',
					'default' => 'Purchase [titleofproduct] and Earn <strong>[buypoint]</strong> Reward Points ([buypointvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_buy_point_message_product_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_buy_point_notice_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_available_point_notice_in_checkout_page',
					'desc' => __('<h3>Available Points Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show Available Reward Points before or after Redeemed Points Message' , 'rewardsystem' ),
					'id'      => 'rs_available_pts_before_after_redeemed_pts_checkout',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_available_pts_before_after_redeemed_pts_checkout',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'After' , 'rewardsystem' ),
						'2' => __( 'Before' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Show/Hide Available Reward Points' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_my_rewards_checkout_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_my_rewards_checkout_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Available Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_in_checkout',
					'std'     => 'My Reward Points [userpoints] ([userpoints_value])',
					'default' => 'My Reward Points [userpoints] ([userpoints_value])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_available_point_notice_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_balance_point_notice_in_checkout_page',
					'desc' => __('<h3>Balance Points Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Redeemed Points Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_redeem_points_checkout_page',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_redeem_points_checkout_page',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Redeemed Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_redeemed_in_checkout',
					'std'     => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points ([balanceprice])',
					'default' => '[redeempoints] Reward Points Redeemed. Balance [redeemeduserpoints] Reward Points ([balanceprice])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_redeemed_in_checkout',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_balance_point_notice_in_checkout_page',
				),
				array(
					'type' => 'title',
					'id'   => 'rs_gateway_point_notice_in_checkout_page',
					'desc' => __('<h3>Gateway Points Notice</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'Show/Hide Payment Gateway Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_payment_gateway_reward_points',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_payment_gateway_reward_points',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Payment Gateway Reward Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_payment_gateway_reward_points',
					'std'     => 'Use this [paymentgatewaytitle] and Earn [paymentgatewaypoints] Reward Points',
					'default' => 'Use this [paymentgatewaytitle] and Earn [paymentgatewaypoints] Reward Points',
					'type'    => 'textarea',
					'newids'  => 'rs_message_payment_gateway_reward_points',
				),
				array(
					'name'    => __( 'Message to display when using Selected Payment Gateway to restrict earn points' , 'rewardsystem' ),
					'id'      => 'rs_restriction_msg_for_selected_gateway',
					'type'    => 'textarea',
					'std'     => 'You cannot earn points if you use [paymentgatewaytitle] Gateway',
					'default' => 'You cannot earn points if you use [paymentgatewaytitle] Gateway',
					'newids'  => 'rs_restriction_msg_for_selected_gateway',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_gateway_point_notice_in_checkout_page',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_checkout_page_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Cart and Checkout Page Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_cart_checkout_page_msg',
				),
				array(
					'name'    => __( 'Show/Hide First Purchase Points that can be Earned' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_first_purchase_points',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_first_purchase_points',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message for First Purchase Points that can be Earned' , 'rewardsystem' ),
					'id'      => 'rs_message_for_first_purchase',
					'std'     => 'Complete the Purchase and Earn <strong>[fppoint]</strong> Reward Points for First Purchase ([fppointvalue])',
					'default' => 'Complete the Purchase and Earn <strong>[fppoint]</strong> Reward Points for First Purchase ([fppointvalue])',
					'type'    => 'textarea',
					'newids'  => 'rs_message_for_first_purchase',
				),
				array(
					'name'    => __( 'Show/Hide Reward Points Redeeming Success Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_redeem',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_redeem',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Reward Points Redeeming Success Message - Manual' , 'rewardsystem' ),
					'id'      => 'rs_success_coupon_message',
					'std'     => 'Reward Points Successfully Added',
					'default' => 'Reward Points Successfully Added',
					'type'    => 'text',
					'newids'  => 'rs_success_coupon_message',
				),
				array(
					'name'    => __( 'Reward Points Redeeming Success Message - Automatic' , 'rewardsystem' ),
					'id'      => 'rs_automatic_success_coupon_message',
					'std'     => 'AutoReward Points Successfully Added',
					'default' => 'AutoReward Points Successfully Added',
					'type'    => 'text',
					'newids'  => 'rs_automatic_success_coupon_message',
				),
				array(
					'name'    => __( 'Redeemed Points Removal Message' , 'rewardsystem' ),
					'id'      => 'rs_remove_redeem_points_message',
					'std'     => 'Reward Points has been removed.',
					'default' => 'Reward Points has been removed.',
					'type'    => 'text',
					'newids'  => 'rs_remove_redeem_points_message',
				),
				array(
					'name'     => __( 'Error Message for Maximum Redeeming Threshold Value' , 'rewardsystem' ),
					'desc'     => __( 'Message which will be displayed when the user redeem points more than the Threshold Limit' , 'rewardsystem' ),
					'id'       => 'rs_errmsg_for_max_discount_type',
					'std'      => 'Maximum Discount has been Limited to [percentage] %',
					'default'  => 'Maximum Discount has been Limited to [percentage] %',
					'type'     => 'textarea',
					'newids'   => 'rs_errmsg_for_max_discount_type',
					'class'    => 'rs_errmsg_for_max_discount_type',
					'desc_tip' => true,
				),
				array(
					'name'    => __( 'Product Purchase Reward Points Earning Prevented Error Message due to Redeeming' , 'rewardsystem' ),
					'id'      => 'rs_errmsg_for_redeeming_in_order',
					'std'     => 'Since,You Redeemed Your Reward Points in this Order, You Cannot Earn Reward Points For this Order',
					'default' => 'Since,You Redeemed Your Reward Points in this Order, You Cannot Earn Reward Points For this Order',
					'type'    => 'textarea',
					'newids'  => 'rs_errmsg_for_redeeming_in_order',
					'class'   => 'rs_errmsg_for_redeeming_in_order',
				),
				array(
					'name'    => __( 'Product Purchase Reward Points Earning Prevented Error Message due to Coupon usage' , 'rewardsystem' ),
					'id'      => 'rs_errmsg_for_coupon_in_order',
					'std'     => 'Since You have used Coupon in this Order, You Cannot Earn Reward Points For this Order',
					'default' => 'Since You have used Coupon in this Order, You Cannot Earn Reward Points For this Order',
					'type'    => 'textarea',
					'newids'  => 'rs_errmsg_for_coupon_in_order',
					'class'   => 'rs_errmsg_for_coupon_in_order',
				),
				array(
					'name'    => __( 'Show/Hide Points/Coupon Redeeming Restriction Message for Point Priced Products' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_errmsg_for_point_price_coupon',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_errmsg_for_point_price_coupon',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Points/Coupon Redeeming Restriction Message for Point Priced Products' , 'rewardsystem' ),
					'id'      => 'rs_errmsg_for_redeem_in_point_price_prt',
					'std'     => 'Points not Redeem for Point Price Product',
					'default' => 'Points not Redeem for Point Price Product',
					'type'    => 'textarea',
					'newids'  => 'rs_errmsg_for_redeem_in_point_price_prt',
					'class'   => 'rs_errmsg_for_redeem_in_point_price_prt',
				),
				array(
					'name'    => __( 'Show/Hide Points Calculation Caution Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_notice_for_redeeming',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_notice_for_redeeming',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Points Calculation Caution Message' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_redeem_when_tax_enabled',
					'std'     => 'Actual Points which can be Redeemed may differ based on Tax Configuration',
					'default' => 'Actual Points which can be Redeemed may differ based on Tax Configuration',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_redeem_when_tax_enabled',
					'class'   => 'rs_msg_for_redeem_when_tax_enabled',
				),
				array(
					'name'    => __( 'First Product Purchase Points Error Message' , 'rewardsystem' ),
					'id'      => 'rs_minimum_order_total_for_first_purchase_error',
					'std'     => 'Minimum Order Total required to Earn First Purchase Point is [order_total_value]',
					'default' => 'Minimum Order Total required to Earn First Purchase Point is [order_total_value]',
					'type'    => 'textarea',
					'newids'  => 'rs_minimum_order_total_for_first_purchase_error',
					'class'   => 'rs_minimum_order_total_for_first_purchase_error',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_cart_checkout_page_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_payment_plan_compatible_start',
				),
				array(
					'name' => __( 'SUMO Payment Plans Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_paymentplans_message_settings',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_paymentplans_message_settings' ),
				array(
					'type' => 'rs_payment_plan_compatible_close',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'My Reward Table Customization Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_my_reward_label_settings',
				),
				array(
					'type'   => 'title',
					'id'     => 'rs_my_rewards_settings_my_account',
					'newids' => 'rs_my_rewards_settings_my_account',
					'desc'   => __('<h3>My Account Page Reward Table Settings</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'My Rewards Table in My Account' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_table',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_table',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Points Log should be displayed in' , 'rewardsystem' ),
					'id'      => 'rs_points_log_sorting',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_points_log_sorting',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Ascending Order' , 'rewardsystem' ),
						'2' => __( 'Descending Order' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Search Box in My Rewards Table' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_search_box_in_my_rewards_table',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_search_box_in_my_rewards_table',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'S.No Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_s_no',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_s_no',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Points Expiry Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_expire',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_expire',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Username Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_user_name_hide',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_user_name_hide',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Reward For Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_reward_for_hide',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_reward_for_hide',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earned Points Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_earned_points_hide',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_earned_points_hide',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Redeemed Points Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_redeemed_points_hide',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_redeemed_points_hide',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Total Points Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_total_points_hide',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_total_points_hide',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earned Date Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_earned_date_hide',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_earned_date_hide',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Page Size in My Rewards Table' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_page_size_my_rewards',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_page_size_my_rewards',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Number of entries to display in each pagination' , 'rewardsystem' ),
					'id'      => 'rs_number_of_page_size_in_myaccount',
					'std'     => '5',
					'default' => '5',
					'newids'  => 'rs_number_of_page_size_in_myaccount',
					'type'    => 'number',
				),
				array(
					'name'    => __( 'Number of pagination to display in Rewards Table' , 'rewardsystem' ),
					'id'      => 'rs_numbers_to_display_pagination',
					'std'     => '',
					'default' => '',
					'newids'  => 'rs_numbers_to_display_pagination',
					'type'    => 'number',
				),
				array(
					'name'     => __( 'Reward Table Position' , 'rewardsystem' ),
					'id'       => 'rs_reward_table_position',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_reward_table_position',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'After My Account' , 'rewardsystem' ),
						'2' => __( 'Before My Account' , 'rewardsystem' ),
					),
					'desc'     => __( 'This option controls the Reward Table Display Position in My Account Page' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Display Reward Points Label Position' , 'rewardsystem' ),
					'id'       => 'rs_reward_point_label_position',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_reward_point_label_position',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Before Points' , 'rewardsystem' ),
						'2' => __( 'After Points' , 'rewardsystem' ),
					),
					'desc'     => __( 'This option controls the Reward Points Label Display Position' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Total Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Current Points in My Account Page' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_total',
					'std'      => 'Total Points: ',
					'default'  => 'Total Points:',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_total',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Display Currency Value of Total Points' , 'rewardsystem' ),
					'id'       => 'rs_reward_currency_value',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_reward_currency_value',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'desc'     => __( 'This option controls whether the Currency Value of the Earned Points has to be displayed next to Earned Points' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Earned & Redeemed Points Duration' , 'rewardsystem' ),
					'id'       => 'rs_show_or_hide_date_filter',
					'std'      => '2',
					'default'  => '2',
					'newids'   => 'rs_show_or_hide_date_filter',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'desc'     => __( 'By selecting "Show", users can check their points earned and redeemed during the specified date/month.' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'My Rewards Label' , 'rewardsystem' ),
					'desc'     => __( 'My Rewards Label Secion' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_title',
					'std'      => 'My Rewards',
					'default'  => 'My Rewards',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_title',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'S.No Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the S.No Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_sno_label',
					'std'      => 'S.No',
					'default'  => 'S.No',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_sno_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Username Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Username Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_userid_label',
					'std'      => 'Username',
					'default'  => 'Username',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_userid_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Reward for Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Reward for Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_reward_for_label',
					'std'      => 'Reward for',
					'default'  => 'Reward for',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_reward_for_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Earned Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Earned Points Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_points_earned_label',
					'std'      => 'Earned Points',
					'default'  => 'Earned Points',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_points_earned_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeemed Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Redeemed Points Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_redeem_points_label',
					'std'      => 'Redeemed Points',
					'default'  => 'Redeemed Points',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_redeem_points_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Total Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Total Points Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_total_points_label',
					'std'      => 'Total Points',
					'default'  => 'Total Points',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_total_points_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Earned Date Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Earned Date Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_date_label',
					'std'      => 'Earned Date',
					'default'  => 'Earned Date',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_date_label',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Points Expires On' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Points Expires On Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_points_expired_label',
					'std'      => 'Points Expires On',
					'default'  => 'Points Expires On',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_points_expired_label',
					'desc_tip' => true,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'rs_my_rewards_settings_my_account',
				),
				array(
					'type' => 'title',
					'desc' => __('<h3>My Reward Table Shortcode Settings</h3><br><br>', 'rewardsystem'),
				),
				array(
					'name'    => __( 'My Rewards Table in Shortcode' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_table_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_table_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Points Log should be displayed in' , 'rewardsystem' ),
					'id'      => 'rs_points_log_sorting_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_points_log_sorting_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Ascending Order' , 'rewardsystem' ),
						'2' => __( 'Descending Order' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Search Box in My Rewards Table' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_search_box_in_my_rewards_table_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_search_box_in_my_rewards_table_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'S.No Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_s_no_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_s_no_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Points Expiry Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_expire_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_expire_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Username Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_user_name_hide_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_user_name_hide_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Reward For Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_reward_for_hide_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_reward_for_hide_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earned Points Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_earned_points_hide_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_earned_points_hide_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Redeemed Points Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_redeemed_points_hide_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_redeemed_points_hide_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Total Points Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_total_points_hide_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_total_points_hide_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Earned Date Column' , 'rewardsystem' ),
					'id'      => 'rs_my_reward_points_earned_date_hide_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_my_reward_points_earned_date_hide_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Page Size in My Rewards Table' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_page_size_my_rewards_shortcode',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_page_size_my_rewards_shortcode',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Number of entries to display in each pagination' , 'rewardsystem' ),
					'id'      => 'rs_number_of_page_size_in_myrewards_shortcode',
					'std'     => '5',
					'default' => '5',
					'newids'  => 'rs_number_of_page_size_in_myrewards_shortcode',
					'type'    => 'number',
				),
				array(
					'name'    => __( 'Number of pagination to display in Rewards Table' , 'rewardsystem' ),
					'id'      => 'rs_numbers_to_display_pagination_shortcode',
					'std'     => '',
					'default' => '',
					'newids'  => 'rs_numbers_to_display_pagination_shortcode',
					'type'    => 'number',
				),
				array(
					'name'     => __( 'Display Reward Points Label Position' , 'rewardsystem' ),
					'id'       => 'rs_reward_point_label_position_shortcode',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_reward_point_label_position_shortcode',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Before Points' , 'rewardsystem' ),
						'2' => __( 'After Points' , 'rewardsystem' ),
					),
					'desc'     => __( 'This option controls the Reward Points Label Display Position' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Total Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Current Points in My Account Page' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_total_shortcode',
					'std'      => 'Total Points: ',
					'default'  => 'Total Points:',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_total_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Display Currency Value of Total Points' , 'rewardsystem' ),
					'id'       => 'rs_reward_currency_value_shortcode',
					'std'      => '1',
					'default'  => '1',
					'newids'   => 'rs_reward_currency_value_shortcode',
					'type'     => 'select',
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
					'desc'     => __( 'This option controls whether the Currency Value of the Earned Points has to be displayed next to Earned Points' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'My Rewards Label' , 'rewardsystem' ),
					'desc'     => __( 'My Rewards Label Secion' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_title_shortcode',
					'std'      => 'My Rewards',
					'default'  => 'My Rewards',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_title_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'S.No Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the S.No Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_sno_label_shortcode',
					'std'      => 'S.No',
					'default'  => 'S.No',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_sno_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Username Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Username Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_userid_label_shortcode',
					'std'      => 'Username',
					'default'  => 'Username',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_userid_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Reward for Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Reward for Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_rewarder_label_shortcode',
					'std'      => 'Reward for',
					'default'  => 'Reward for',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_rewarder_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Earned Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Earned Points Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_points_earned_label_shortcode',
					'std'      => 'Earned Points',
					'default'  => 'Earned Points',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_points_earned_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Redeemed Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Redeemed Points Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_redeem_points_label_shortcode',
					'std'      => 'Redeemed Points',
					'default'  => 'Redeemed Points',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_redeem_points_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Total Points Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Total Points Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_total_points_label_shortcode',
					'std'      => 'Total Points',
					'default'  => 'Total Points',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_total_points_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Earned Date Label' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Earned Date Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_date_label_shortcode',
					'std'      => 'Earned Date',
					'default'  => 'Earned Date',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_date_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Points Expires On' , 'rewardsystem' ),
					'desc'     => __( 'Label used for displaying the Points Expires On Column Name in My Rewards Table' , 'rewardsystem' ),
					'id'       => 'rs_my_rewards_points_expired_label_shortcode',
					'std'      => 'Points Expires On',
					'default'  => 'Points Expires On',
					'type'     => 'text',
					'newids'   => 'rs_my_rewards_points_expired_label_shortcode',
					'desc_tip' => true,
				),
				array(
					'type' => 'reward_table_sorting',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_my_reward_label_settings' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Guest Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_message_guest',
				),
				array(
					'name'    => __( 'Message Displayed for Guests' , 'rewardsystem' ),
					'id'      => 'rs_message_shortcode_guest_display',
					'std'     => 'Please Login to View the Contents of this Page',
					'default' => 'Please Login to View the Contents of this Page',
					'type'    => 'text',
					'newids'  => 'rs_message_shortcode_guest_display',
					'class'   => 'rs_message_shortcode_guest_display',
				),
				array(
					'name'     => __( 'Login Name Label' , 'rewardsystem' ),
					'id'       => 'rs_message_shortcode_login_name',
					'std'      => 'Login',
					'default'  => 'Login',
					'type'     => 'text',
					'newids'   => 'rs_message_shortcode_login_name',
					'class'    => 'rs_message_shortcode_login_name',
					'desc'     => __( 'This label will be used as Hyperlink text' , 'rewardsystem' ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( '[my_userpoints_value] Shortcode Label' , 'rewardsystem' ),
					'id'       => 'rs_label_shortcode',
					'std'      => 'My Points',
					'default'  => 'My Points',
					'type'     => 'text',
					'newids'   => 'rs_label_shortcode',
					'class'    => 'rs_label_shortcode',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_message_guest' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Unsubscription Link Text Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_unsub_link',
				),
				array(
					'name'     => __( 'Unsubscribe Link Message' , 'rewardsystem' ),
					'desc'     => __( 'This message will be displayed in emails sent through SUMO Reward Points' , 'rewardsystem' ),
					'id'       => 'rs_unsubscribe_link_for_email',
					'std'      => 'If you want to unsubscribe from SUMO Reward Points Emails,click here...{rssitelinkwithid}',
					'default'  => 'If you want to unsubscribe from SUMO Reward Points Emails,click here...{rssitelinkwithid}',
					'type'     => 'textarea',
					'newids'   => 'rs_unsubscribe_link_for_email',
					'class'    => 'rs_unsubscribe_link_for_email',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => '_rs_unsub_link' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Cart Error Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_cart_error_msg',
				),
				array(
					'name'    => __( 'Error Message displayed when Normal Product is added to cart - Point Price Product is already in cart' , 'rewardsystem' ),
					'id'      => 'rs_errmsg_for_normal_product_with_point_price',
					'std'     => 'Cannot add normal product with point pricing product',
					'default' => 'Cannot add normal product with point pricing product',
					'type'    => 'textarea',
					'newids'  => 'rs_errmsg_for_normal_product_with_point_price',
					'class'   => 'rs_errmsg_for_normal_product_with_point_price',
				),
				array(
					'name'    => __( 'Error Message displayed when Point Price Product is added to cart - Normal Product is already in cart' , 'rewardsystem' ),
					'id'      => 'rs_errmsg_for_point_price_product_with_normal',
					'std'     => 'Cannot Purchase Point Pricing Product with Normal product',
					'default' => 'Cannot Purchase Point Pricing Product with Normal product',
					'type'    => 'textarea',
					'newids'  => 'rs_errmsg_for_point_price_product_with_normal',
					'class'   => 'rs_errmsg_for_point_price_product_with_normal',
				),
				array(
					'name'    => __( 'Error Message displayed in product page when guest users try to add point price product to cart' , 'rewardsystem' ),
					'id'      => 'rs_point_price_product_added_to_cart_guest_errmsg',
					'std'     => 'Only registered users can purchase this product. Click the link to create an account ([loginlink]).',
					'default' => 'Only registered users can purchase this product. Click the link to create an account ([loginlink]).',
					'type'    => 'textarea',
					'newids'  => 'rs_point_price_product_added_to_cart_guest_errmsg',
					'class'   => 'rs_point_price_product_added_to_cart_guest_errmsg',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_cart_error_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Action Points Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_enable_option',
				),
				array(
					'name'    => __( 'Message to display for Signup' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_account_signup',
					'std'     => 'Earn [rssignuppoints] Reward Points by registering in the site',
					'default' => 'Earn [rssignuppoints] Reward Points by registering in the site',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_account_signup',
					'class'   => 'rs_msg_for_account_signup',
				),
				array(
					'name'    => __( 'Message to display for Product Review' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_product_review',
					'std'     => 'Earn [rsreviewpoints] Reward Points by Reviewing a Product',
					'default' => 'Earn [rsreviewpoints] Reward Points by Reviewing a Product',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_product_review',
					'class'   => 'rs_msg_for_product_review',
				),
				array(
					'name'    => __( 'Message to display for Blog Post Creation' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_post_creation',
					'std'     => 'Earn [rspostcreationpoints] Reward Points by creating the blog post',
					'default' => 'Earn [rspostcreationpoints] Reward Points by creating the blog post',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_post_creation',
					'class'   => 'rs_msg_for_post_creation',
				),
				array(
					'name'    => __( 'Message to display for Blog Post Comment' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_post_review',
					'std'     => 'Earn [rspostpoints] Reward Points by commenting the blog post',
					'default' => 'Earn [rspostpoints] Reward Points by commenting the blog post',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_post_review',
					'class'   => 'rs_msg_for_post_review',
				),
				array(
					'name'    => __( 'Message to display for Page Comment' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_page_comment',
					'std'     => 'Earn [rspagecommentpoints] Reward Points by commenting the page',
					'default' => 'Earn [rspagecommentpoints] Reward Points by commenting the page',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_page_comment',
					'class'   => 'rs_msg_for_page_comment',
				),
				array(
					'name'    => __( 'Message to display for Product Creation' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_create_product',
					'std'     => 'Earn [rsproductcreatepoints] Reward Points for Creating a Product',
					'default' => 'Earn [rsproductcreatepoints] Reward Points for Creating a Product',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_create_product',
					'class'   => 'rs_msg_for_create_product',
				),
				array(
					'name'    => __( 'Message to display for Daily Login' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_daily_login',
					'std'     => 'Earn [rsloginpoints] Reward Points by login once per day',
					'default' => 'Earn [rsloginpoints] Reward Points by login once per day',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_daily_login',
					'class'   => 'rs_msg_for_daily_login',
				),
				array(
					'name'    => __( 'Message to display for Product Purchase' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_product_puchase',
					'std'     => 'You can earn points for purchasing the products in site which contain points',
					'default' => 'You can earn points for purchasing the products in site which contain points',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_product_puchase',
					'class'   => 'rs_msg_for_product_puchase',
				),
				array(
					'name'    => __( 'Message to display for Buying Reward Points' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_buying_reward_points',
					'std'     => 'You can buy the points for purchasing the products in site which contain points',
					'default' => 'You can buy the points for purchasing the products in site which contain points',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_buying_reward_points',
					'class'   => 'rs_msg_for_buying_reward_points',
				),
				array(
					'name'    => __( 'Message to display for Referral Product Purchase' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_referral_system_product_purcase',
					'std'     => 'You can earn referral product purchase points when referred person makes the purchase which contains referral points',
					'default' => 'You can earn referral product purchase points when referred person makes the purchase which contains referral points',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_referral_system_product_purcase',
					'class'   => 'rs_msg_for_referral_system_product_purcase',
				),
				array(
					'name'    => __( 'Message to display for Referral Signup' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_referral_system_login',
					'std'     => 'Referral will earn [rsreferralpoints] reward points when referred person register in the site',
					'default' => 'Referral will earn [rsreferralpoints] reward points when referred person register in the site',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_referral_system_login',
					'class'   => 'rs_msg_for_referral_system_login',
				),
				array(
					'name'    => __( 'Message to display for Getting Referred Product Purchase' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_getting_refer_product_purchase',
					'std'     => 'You can earn points for being referred when purchasing the products which contain getting referred product purchasing points',
					'default' => 'You can earn points for being referred when purchasing the products which contain getting referred product purchasing points',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_getting_refer_product_purchase',
					'class'   => 'rs_msg_for_getting_refer_product_purchase',
				),
				array(
					'name'    => __( 'Message to display for Social Promotion Action for Product' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_social_promotion',
					'std'     => 'You can earn points by performing social action for the product which contain points',
					'default' => 'You can earn points by performing social action for the product which contain points',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_social_promotion',
					'class'   => 'rs_msg_for_social_promotion',
				),
				array(
					'name'    => __( 'Message to display for Social Promotion Action for Post/Page' , 'rewardsystem' ),
					'id'      => 'rs_msg_for_social_promotion_for_post',
					'std'     => 'You can earn points by performing social action for the post/page which contain points',
					'default' => 'You can earn points by performing social action for the post/page which contain points',
					'type'    => 'textarea',
					'newids'  => 'rs_msg_for_social_promotion_for_post',
					'class'   => 'rs_msg_for_social_promotion_for_post',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_enable_option' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Signup/Login Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_other_msg',
				),
				array(
					'name'    => __( 'Show/Hide Signup Points Message in My Account' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_sign_up',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_sign_up',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message to display for Signup Points' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_for_sign_up',
					'std'     => 'Earn [rssignuppoints] Reward Points for registering in the site',
					'default' => 'Earn [rssignuppoints] Reward Points for registering in the site',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_for_sign_up',
				),
				array(
					'name'    => __( 'Show/Hide Daily Login Points Message in My Account' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_daily_login',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_daily_login',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Daily Login Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_for_daily_login',
					'std'     => 'Earn [rsdailyloginpoints] Reward Points for Login once per day',
					'default' => 'Earn [rsdailyloginpoints] Reward Points for Login once per day',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_for_daily_login',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_other_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Page Comment Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_other_page_comment_msg',
				),
				array(
					'name'    => __( 'Show/Hide Page Comment Points Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_page_comment',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_page_comment',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message to display for Page Comment Points' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_for_page_comment',
					'std'     => 'Earn [rspagecommentpoints] Reward Points by commenting the page',
					'default' => 'Earn [rspagecommentpoints] Reward Points by commenting the page',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_for_page_comment',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_other_page_comment_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Blog Post Create/Blog Post Comment Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_other_blog_create_comment_msg',
				),
				array(
					'name'    => __( 'Show/Hide Blog Post Creation Points Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_blog_create',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_blog_create',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Message to display for Blog Creation Points' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_for_blog_creation',
					'std'     => 'Earn [rspostcreationpoints] Reward Points by creating the blog post',
					'default' => 'Earn [rspostcreationpoints] Reward Points by creating the blog post',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_for_blog_creation',
				),
				array(
					'name'    => __( 'Show/Hide Blog Post Comment Points Message' , 'rewardsystem' ),
					'id'      => 'rs_show_hide_message_for_post_comment',
					'std'     => '1',
					'default' => '1',
					'newids'  => 'rs_show_hide_message_for_post_comment',
					'type'    => 'select',
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ),
						'2' => __( 'Hide' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Blog Post Comment Points Message' , 'rewardsystem' ),
					'id'      => 'rs_message_user_points_for_blog_comment',
					'std'     => 'Earn [rspostpoints] Reward Points by commenting the blog post',
					'default' => 'Earn [rspostpoints] Reward Points by commenting the blog post',
					'type'    => 'textarea',
					'newids'  => 'rs_message_user_points_for_blog_comment',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_other_blog_create_comment_msg' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Shortcodes used in Messages' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_shortcode_in_messages',
				),
				array(
					'type' => 'title',
					'desc' => __('<b>Single Product Page - Simple Product</b><br><br>
                        <b>[rewardpoints]</b> - To display the current points earned<br><br>
                        <b>[equalamount]</b> - To display currency value equivalent of earn points<br><br>
                        <b>Single Product Page - Variable Product</b><br><br>
                        <b>[variationrewardpoints]</b> - To display points that can be earned<br><br>
                        <b>[variationpointsvalue]</b> - To display currency value equivalent of points that can be earned<br><br>
                        <b>Cart/Checkout Page</b><br><br>
                        <b>[loginlink]</b> - To display login link for guests<br><br>
                        <b>[rspoint]</b> - To display earning points for each product<br><br>
                        <b>[carteachvalue]</b> - To display currency value equivalent of earning points for each product<br><br>
                        <b>[totalrewards]</b> - To display total earning points<br><br>
                        <b>[totalrewardsvalue]</b> - To display currency value equivalent of total earning points<br><br>
                        <b>[balanceprice]</b> - To display currency value equivalent of balance points while redeeming<br><br>
                        <b>[userpoints]</b> - To display total available points<br><br>
                        <b>[userpoints_value]</b> - To display currency value equivalent of total available points<br><br>
                        <b>[my_userpoints_value]</b> - To display currency value equivalent of total available points with label<br><br>
                        <b>[redeempoints]</b> - To display points redeemed<br><br>
                        <b>[redeemeduserpoints]</b> - To display available points after redeeming<br><br>
                        <b>{rssitelinkwithid}</b> - To display unsubscribe link from emails<br><br>
                        <b>[paymentgatewaytitle]</b> - To display payment gateway title in Checkout<br><br>
                        <b>[paymentgatewaypoints]</b> - To display sumo reward points payment gateway points in Checkout<br><br>
                        <b>[percentage]</b> - To display maximum threshold value to redeem<br><br>
                        <b>[rs_referrer_name]</b> - To display referrer name<br><br>
                        <b>[redeeming_threshold_value]</b> -  To display the points to be redeemed in the order<br><br>
                        <b>SignUp/Login Page</b><br><br>
                        <b>[rssignuppoints]</b> - To display SignUp Points in login page<br><br>
                        <b>[rsdailyloginpoints]</b> - To display daily Login points in login page' , 'rewardsystem'),
				),
								array(
					'type' => 'title',
					'desc' => __('<b>Note:</b> <br/>We recommend don’t use the above shortcodes anywhere on your site. It will give the value only on the place where we have predefined.<br/> Please check by using the shortcodes available in the <b>Shortcodes </b> tab which will give the value globally.', 'rewardsystem'),
					'id'   => 'rs_shortcode_note_messages',
				),
				array( 'type' => 'sectionend', 'id' => 'rs_shortcode_in_messages' ),
				array(
					'type' => 'rs_wrapper_end',
				),
					) ) ;
		}

		/**
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
		 */
		public static function reward_system_register_admin_settings() {

			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
			$sorted_data = array_filter( isset($_REQUEST[ 'sorted_list' ]) ? wc_clean(wp_unslash($_REQUEST[ 'sorted_list' ])):array() ) ;
			update_option( 'sorted_settings_list' , $sorted_data ) ;
			if ( isset( $_REQUEST[ 'rs_image_url_upload' ] ) ) {
				update_option( 'rs_image_url_upload' , wc_clean(wp_unslash(( $_REQUEST[ 'rs_image_url_upload' ] )))) ;
			}
		}

		/**
		 * Initialize the Default Settings by looping this function
		 */
		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function reset_message_tab() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

		/*
		 * Function For Upload Your own Gift
		 */

		public static function rs_add_upload_your_gift_voucher() {
			?>
			<table class="form-table">
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label for="rs_image_url_upload"><?php esc_html_e( 'Upload your own Gift Icon' , 'rewardsystem' ) ; ?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rs_image_url_upload" name="rs_image_url_upload" value="<?php echo esc_url(get_option( 'rs_image_url_upload' )) ; ?>"/>
						<input type="submit" id="rs_image_upload_button" class="rs_upload_button" name="rs_image_upload_button" value="<?php esc_html_e('Upload Image', 'rewardsystem'); ?>"/>
					</td>
				</tr>
			</table>
			<?php
		}

		public static function reward_table_sorting() {
			?>
			<table class="form-table fp-srp-points-log">
				<h3><?php esc_html_e( 'My Reward Points Table Sorting' , 'rewardsystem' ) ; ?></h3>
				<tbody class="sortable">
					<?php
					$DefaultColumn = array(
						'username',
						'reward_for',
						'earned_points',
						'redeemed_points',
						'points_expiry',
						'total_points',
						'earned_date',
							) ;
					$SortedColumn  = srp_check_is_array( get_option( 'sorted_settings_list' ) ) ? get_option( 'sorted_settings_list' ) : $DefaultColumn ;
					$Labels        = array(
						'username'        => __( 'Username Column' , 'rewardsystem' ),
						'reward_for'      => __( 'Reward For Column' , 'rewardsystem' ),
						'earned_points'   => __( 'Earned Points Column' , 'rewardsystem' ),
						'redeemed_points' => __( 'Redeemed Points Column' , 'rewardsystem' ),
						'points_expiry'   => __( 'Points Expiry Column' , 'rewardsystem' ),
						'total_points'    => __( 'Total Points Column' , 'rewardsystem' ),
						'earned_date'     => __( 'Earned Date Column' , 'rewardsystem' ),
							) ;
					foreach ( $SortedColumn as $Column ) {
						?>
						<tr>
							<th class="post_sort_handle"><?php echo esc_html($Labels[ $Column ] ); ?></th>
							<td class="post_sort_handle">
								<input type="hidden" name="sorted_list[<?php echo esc_attr($Column) ; ?>]" value="<?php echo esc_attr($Column); ?>">
								<img src="<?php echo esc_url(SRP_PLUGIN_DIR_URL) ; ?>/assets/images/drag-icon.png"/>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}

	RSMessage::init() ;
}
