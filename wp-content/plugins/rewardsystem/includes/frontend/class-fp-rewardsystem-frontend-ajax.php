<?php

/*
 * Frontend Ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_Rewardsystem_Frontend_Ajax' ) ) {

	/**
	 * FP_Rewardsystem_Frontend_Ajax Class
	 */
	class FP_Rewardsystem_Frontend_Ajax {

		/**
		 * FP_Rewardsystem_Frontend_Ajax Class initialization
		 */
		public static function init() {
			add_action( 'wp_ajax_fblikecallback', array( __CLASS__, 'award_points_for_fblike' ) );
			add_action( 'wp_ajax_fbsharecallback', array( __CLASS__, 'award_points_for_fbshare' ) );
			add_action( 'wp_ajax_twittertweetcallback', array( __CLASS__, 'award_points_for_tweet' ) );
			add_action( 'wp_ajax_twitterfollowcallback', array( __CLASS__, 'award_points_for_twitter_follow' ) );
			add_action( 'wp_ajax_instagramcallback', array( __CLASS__, 'award_points_for_instagram_follow' ) );
			add_action( 'wp_ajax_vklikecallback', array( __CLASS__, 'award_points_for_vk_like' ) );
			add_action( 'wp_ajax_gpluscallback', array( __CLASS__, 'award_points_for_gplus_share' ) );
			add_action( 'wp_ajax_okrucallback', array( __CLASS__, 'award_points_for_ok_share' ) );
			add_action( 'wp_ajax_nopriv_unset_referral', array( __CLASS__, 'unset_generated_referral_link' ) );
			add_action( 'wp_ajax_unset_referral', array( __CLASS__, 'unset_generated_referral_link' ) );
			add_action( 'wp_ajax_nopriv_generate_referral_link', array( __CLASS__, 'generate_referral_link' ) );
			add_action( 'wp_ajax_generate_referral_link', array( __CLASS__, 'generate_referral_link' ) );
			add_action( 'wp_ajax_nopriv_rs_refer_a_friend_ajax', array( __CLASS__, 'mail_referral_link_to_friends' ) );
			add_action( 'wp_ajax_rs_refer_a_friend_ajax', array( __CLASS__, 'mail_referral_link_to_friends' ) );
			add_action( 'wp_ajax_unsetproduct', array( __CLASS__, 'unset_removed_product_id' ) );
			add_action( 'wp_ajax_cancelcashback', array( __CLASS__, 'cancel_cashback_request' ) );
			add_action( 'wp_ajax_cashbackrequest', array( __CLASS__, 'cashback_request' ) );
			add_action( 'wp_ajax_redeemvouchercode', array( __CLASS__, 'redeem_gift_voucher' ) );
			add_action( 'wp_ajax_messageforbooking', array( __CLASS__, 'message_for_booking' ) );
			add_action( 'wp_ajax_subscribemail', array( __CLASS__, 'unsunscribe_or_subscribe_mail' ) );
			add_action( 'wp_ajax_savenominee', array( __CLASS__, 'save_nominee' ) );
			add_action( 'wp_ajax_rewardgatewaymsg', array( __CLASS__, 'reward_gateway_msg' ) );
			add_action( 'wp_ajax_nopriv_getvariationpoints', array( __CLASS__, 'points_for_variation_in_product_page' ) );
			add_action( 'wp_ajax_getvariationpoints', array( __CLASS__, 'points_for_variation_in_product_page' ) );
			add_action( 'wp_ajax_remove_sumo_coupon', array( __CLASS__, 'remove_coupon' ) );
			add_action( 'wp_ajax_send_referral_email', array( __CLASS__, 'rs_send_mail_for_referral_link' ) );
			add_action( 'wp_ajax_unsubscribe_user', array( __CLASS__, 'rs_user_email_unsubscribe' ) );
			add_action( 'wp_ajax_rp_apply_coupon', array( __CLASS__, 'srp_rp_apply_coupon' ) );
		}

		public static function insert_points_for_social_actions( $PostId, $UserID, $Action, $State, $Type ) {
			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'fblike' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'fb_like_count_per_day', get_option( 'rs_enable_fblike_restriction' ), get_option( 'rs_no_of_fblike_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'fb_like',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPFL';
				} else {
					$Points = get_option( 'rs_global_social_facebook_reward_points_post' );
					$Slug   = 'RPFLP';
				}
				$RevisedSlug = 'RVPFRPFL';
				update_product_count_for_social_action( $UserID, 'fb_like_count_per_day', $PostId );
			} elseif ( 'fbshare' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'fb_share_count_per_day', get_option( 'rs_enable_fbshare_restriction' ), get_option( 'rs_no_of_fbshare_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'fb_share',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPFS';
				} else {
					$Points = get_option( 'rs_global_social_facebook_share_reward_points_post' );
					$Slug   = 'RPFSP';
				}
				$RevisedSlug = '';
				update_product_count_for_social_action( $UserID, 'fb_share_count_per_day', $PostId );
			} elseif ( 'tweet' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'twitter_tweet_count_per_day', get_option( 'rs_enable_tweet_restriction' ), get_option( 'rs_no_of_tweet_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'twitter_tweet',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPTT';
				} else {
					$Points = get_option( 'rs_global_social_twitter_reward_points_post' );
					$Slug   = 'RPTTP';
				}
				$RevisedSlug = '';
				update_product_count_for_social_action( $UserID, 'twitter_tweet_count_per_day', $PostId );
			} elseif ( 'twitter_follow' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'twitter_follow_count_per_day', get_option( 'rs_enable_twitter_follow_restriction' ), get_option( 'rs_no_of_twitter_follow_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'twitter_follow',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPTF';
				} else {
					$Points = get_option( 'rs_global_social_twitter_follow_reward_points_post' );
					$Slug   = 'RPTFP';
				}
				$RevisedSlug = '';
				update_product_count_for_social_action( $UserID, 'twitter_follow_count_per_day', $PostId );
			} elseif ( 'instagram_follow' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'instagram_count_per_day', get_option( 'rs_enable_instagram_restriction' ), get_option( 'rs_no_of_instagram_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'instagram',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPIF';
				} else {
					$Points = get_option( 'rs_global_social_instagram_reward_points_post' );
					$Slug   = 'RPIFP';
				}
				$RevisedSlug = '';
				update_product_count_for_social_action( $UserID, 'instagram_count_per_day', $PostId );
			} elseif ( 'vk_like' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'vk_like_count_per_day', get_option( 'rs_enable_vk_restriction' ), get_option( 'rs_no_of_vk_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'vk_like',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPVL';
				} else {
					$Points = get_option( 'rs_global_social_vk_reward_points_post' );
					$Slug   = 'RPVLP';
				}
				$RevisedSlug = 'RVPFRPVL';
				update_product_count_for_social_action( $UserID, 'vk_like_count_per_day', $PostId );
			} elseif ( 'gplus_share' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'gplus_share_count_per_day', get_option( 'rs_enable_gplus_restriction' ), get_option( 'rs_no_of_gplus_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'g_plus',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPGPOS';
				} else {
					$Points = get_option( 'rs_global_social_google_reward_points_post' );
					$Slug   = 'RPGPOSP';
				}
				$RevisedSlug = 'RVPFRPGPOS';
				update_product_count_for_social_action( $UserID, 'gplus_share_count_per_day', $PostId );
			} elseif ( 'ok_share' == $Action ) {
				if ( 'product' == $Type ) {
					$AwardPoints = allow_points_for_social_action( $UserID, 'ok_follow_count_per_day', get_option( 'rs_enable_ok_restriction' ), get_option( 'rs_no_of_ok_count' ) );
					if ( ! $AwardPoints ) {
						return;
					}

					$args   = array(
						'productid'    => $PostId,
						'item'         => array( 'qty' => '1' ),
						'socialreward' => 'yes',
						'rewardfor'    => 'ok_follow',
					);
					$Points = check_level_of_enable_reward_point( $args );
					$Slug   = 'RPOK';
				} else {
					$Points = get_option( 'rs_global_social_ok_follow_reward_points_post' );
					$Slug   = 'RPOKP';
				}
				$RevisedSlug = '';
				update_product_count_for_social_action( $UserID, 'ok_follow_count_per_day', $PostId );
			}

			if ( 'on' == $State ) {
				if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
					$new_obj->check_point_restriction( $Points, 0, $Slug, $UserID, '', '', $PostId, 0, '' );
				} else {
					$valuestoinsert = array(
						'pointstoinsert'    => $Points,
						'event_slug'        => $Slug,
						'user_id'           => $UserID,
						'product_id'        => $PostId,
						'totalearnedpoints' => $Points,
					);
					$new_obj->total_points_management( $valuestoinsert );
				}
			} else {
				$valuestoinsert = array(
					'pointsredeemed'    => $Points,
					'event_slug'        => $RevisedSlug,
					'user_id'           => $UserID,
					'product_id'        => $PostId,
					'totalredeempoints' => $Points,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
		}

		/* Award Points for Liking Product/Page/Post */

		public static function award_points_for_fblike() {
			check_ajax_referer( 'fb-like', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsfacebooklikes_post' : '_rsfacebooklikes';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					} else {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsfacebookunlikes_post' : '_rsfacebookunlikes';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'fblike', $State, $Type );
						} else {
							$Content = 'post_or_page_unlike';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'fblike', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_facebook_like.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_facebook_like' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'fb_like',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_facebook_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[facebook_like_reward_points]', $Points, get_option( 'rs_succcess_message_for_facebook_like' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_facebook_unlike' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_fblike' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for Sharing Product/Page/Post */

		public static function award_points_for_fbshare() {
			check_ajax_referer( 'fb-share', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsfacebookshare_post' : '_rsfacebookshare';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'fbshare', $State, $Type );
						} else {
							$Content = 'post_or_page_unshare';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'fbshare', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_facebook_share.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_facebook_share' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'fb_share',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_facebook_share_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[facebook_share_reward_points]', $Points, get_option( 'rs_succcess_message_for_facebook_share' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_facebook_share' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_fbshare' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for Tweeting Product/Page/Post */

		public static function award_points_for_tweet() {
			check_ajax_referer( 'twitter-tweet', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rstwittertweet_post' : '_rstwittertweet';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'tweet', $State, $Type );
						} else {
							$Content = 'tweeted';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'tweet', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_tweet.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_tweet' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'twitter_tweet',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_twitter_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[twitter_tweet_reward_points]', $Points, get_option( 'rs_succcess_message_for_twitter_share' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_twitter_unshare' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_tweet' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for Twitter Following Product/Page/Post */

		public static function award_points_for_twitter_follow() {
			check_ajax_referer( 'twitter-follow', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rstwitterfollow_post' : '_rstwitterfollow';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'twitter_follow', $State, $Type );
						} else {
							$Content = 'followed';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'twitter_follow', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_twitter_follow.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_twitter_follow' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'twitter_follow',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_twitter_follow_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[twitter_follow_reward_points]', $Points, get_option( 'rs_succcess_message_for_twitter_follow' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_twitter_unfollow' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_twitter_follow' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for Instagram Following Product/Page/Post */

		public static function award_points_for_instagram_follow() {
			check_ajax_referer( 'instagram-follow', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsinstagram_post' : '_rsinstagram';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'instagram_follow', $State, $Type );
						} else {
							$Content = 'instagramfollowed';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'instagram_follow', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_instagram_follow.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_instagram_follow' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'instagram',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_instagram_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[instagram_reward_points]', $Points, get_option( 'rs_succcess_message_for_instagram' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_instagram' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_instagram' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for VK.Com Like Product/Page/Post */

		public static function award_points_for_vk_like() {
			check_ajax_referer( 'vk-like', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsvklike_post' : '_rsvklike';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					} else {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsvkunlikes_post' : '_rsvkunlikes';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'vk_like', $State, $Type );
						} else {
							$Content = 'vkliked';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'vk_like', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_vk_like.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_vk_like' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'vk_like',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_vk_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[vk_reward_points]', $Points, get_option( 'rs_succcess_message_for_vk' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_vk' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_vk' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for Gplus Share Product/Page/Post */

		public static function award_points_for_gplus_share() {
			check_ajax_referer( 'gplus-share', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsgoogleshares_post' : '_rsgoogleshares';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					} else {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsgoogleplusunlikes_post' : '_rsgoogleplusunlikes';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'gplus_share', $State, $Type );
						} else {
							$Content = 'gplusshared';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'gplus_share', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_gplus_share.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_gplus_share' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'g_plus',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_google_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[google_share_reward_points]', $Points, get_option( 'rs_succcess_message_for_google_share' ) ),
					'unsuccess_msg' => get_option( 'rs_unsucccess_message_for_google_unshare' ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_gplus' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Award Points for OK.ru Share Product/Page/Post */

		public static function award_points_for_ok_share() {
			check_ajax_referer( 'okru-share', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['postid'] ) || ! isset( $_POST['state'] ) || ! isset( $_POST['type'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$Content     = '';
				$UserID      = get_current_user_id();
				$BanningType = check_banning_type( $UserID );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					$PostId         = absint( $_POST['postid'] );
					$State          = sanitize_text_field( $_POST['state'] );
					$LikedPostIds[] = $PostId;
					$Type           = sanitize_text_field( $_POST['type'] );
					if ( 'on' == $State ) {
						$MetaKey = ( 'postorpage' == $Type ) ? '_rsokfollow_post' : '_rsokfollow';
						$OldData = get_user_meta( $UserID, $MetaKey, true );
					}
					if ( ! empty( $OldData ) ) {
						if ( ! in_array( $PostId, $OldData ) ) {
							$MergedData = array_merge( (array) $OldData, $LikedPostIds );
							update_user_meta( $UserID, $MetaKey, $MergedData );
							self::insert_points_for_social_actions( $PostId, $UserID, 'ok_share', $State, $Type );
						} else {
							$Content = 'okrushared';
						}
					} else {
						update_user_meta( $UserID, $MetaKey, $LikedPostIds );
						self::insert_points_for_social_actions( $PostId, $UserID, 'ok_share', $State, $Type );
					}
										/**
										 * Hook:fp_reward_point_for_okru_share.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_okru_share' );
				}
				$args             = array(
					'productid'    => $PostId,
					'item'         => array( 'qty' => '1' ),
					'socialreward' => 'yes',
					'rewardfor'    => 'ok_follow',
				);
				$PointsForProduct = check_level_of_enable_reward_point( $args );
				$Points           = ( 'postorpage' == $Type ) ? get_option( 'rs_global_social_ok_follow_reward_points_post' ) : $PointsForProduct;
				$data             = array(
					'content'       => $Content,
					'success_msg'   => str_replace( '[ok_share_reward_points]', $Points, get_option( 'rs_succcess_message_for_ok_follow' ) ),
					'unsuccess_msg' => do_shortcode( get_option( 'rs_unsucccess_message_for_ok_unfollow' ) ),
					'restrictmsg'   => get_option( 'rs_restriction_message_for_ok' ),
				);
				wp_send_json_success( $data );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Unset Generated Referral Link in Generate Referral Link Table */

		public static function unset_generated_referral_link() {
			check_ajax_referer( 'unset-referral', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['unsetarray'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$UserId              = get_current_user_id();
				$ListofGeneratedLink = get_option( 'arrayref' . $UserId );
				unset( $ListofGeneratedLink[ wc_clean( wp_unslash( $_POST['unsetarray'] ) ) ] );
				update_option( 'arrayref' . $UserId, $ListofGeneratedLink );
				wp_send_json_success();
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Generated Referral Link  */

		public static function generate_referral_link() {
			check_ajax_referer( 'generate-referral', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['url'] ) || empty( $_POST['url'] ) ) {
				throw new exception( esc_html( 'Invalid URL', 'rewardsystem' ) );
			}

			try {
				$UserId      = get_current_user_id();
				$UserInfo    = get_userdata( $UserId );
				$Username    = is_object( $UserInfo ) ? $UserInfo->user_login : 'Guest';
				$KeyforQuery = '1' == get_option( 'rs_generate_referral_link_based_on_user' ) ? $Username : $UserId;
				$RefURL      = ( 'yes' == get_option( 'rs_restrict_referral_points_for_same_ip' ) ) ? add_query_arg(
					array(
						'ref' => $KeyforQuery,
						'ip'  => base64_encode( get_referrer_ip_address() ),
					),
					esc_url_raw( $_POST['url'] )
				) : add_query_arg( array( 'ref' => $KeyforQuery ), esc_url_raw( $_POST['url'] ) );
				$OldData     = get_option( 'arrayref' . $UserId );
				$DateFormat  = get_option( 'date_format' );
				$arrayref[]  = $RefURL . ',' . date_i18n( $DateFormat );
				if ( srp_check_is_array( $OldData ) ) {
					$arrayref = array_unique( array_merge( $OldData, $arrayref ), SORT_REGULAR );
				}

				update_option( 'arrayref' . $UserId, $arrayref );
				wp_send_json_success();
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/* Send Referral Link for Friend */

		public static function mail_referral_link_to_friends() {
			check_ajax_referer( 'send-mail', 'sumo_security' );

			if ( ! isset( $_POST ) ) {
				throw new Exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$referred_email                       = isset( $_POST['friendemail'] ) ? sanitize_email( $_POST['friendemail'] ) : '';
								$referred_friend_name = isset( $_POST['friendname'] ) ? wc_clean( $_POST['friendname'] ) : '';
				$Name                                 = explode( ',', stripslashes( $referred_friend_name ) );
				$Email                                = explode( ',', stripslashes( $referred_email ) );
				$Subject                              = isset( $_POST['friendsubject'] ) ? wp_kses_post( $_POST['friendsubject'] ) : '';
				$Heading                              = get_option( 'rs_heading_field', 'Referral Link' );
				$Message                              = isset( $_POST['friendmessage'] ) ? wp_kses_post( $_POST['friendmessage'] ) : '';
				$UserInfo                             = get_userdata( get_current_user_id() );
				$RefUsername                          = $UserInfo->user_login;
				$RefFirstname                         = $UserInfo->first_name;
				$RefLastname                          = $UserInfo->last_name;
				$RefEmail                             = $UserInfo->user_email;
				if ( ! srp_check_is_array( $Email ) ) {
					throw new Exception( esc_html( 'There is no Email-Id', 'rewardsystem' ) );
				}

				if ( ! self::validate_referred_email_person( $referred_email ) ) {
					throw new Exception( 'referred_email_error' );
				}

				foreach ( $Email as $key => $to ) {
					$FrndMsg             = $Message;
					$ReplaceFrndName     = str_replace( '[rs_your_friend_name]', $Name[ $key ], $FrndMsg );
					$ReplaceRefUsername  = str_replace( '[rs_user_name]', $RefUsername, $ReplaceFrndName );
					$ReplaceRefEmail     = str_replace( '[rs_referrer_email_id]', $RefEmail, $ReplaceRefUsername );
					$ReplaceRefLastname  = str_replace( '[rs_referrer_last_name]', $RefLastname, $ReplaceRefEmail );
					$ReplaceRefFirstname = str_replace( '[rs_referrer_first_name]', $RefFirstname, $ReplaceRefLastname );

					$KeyForQuery = '1' == get_option( 'rs_generate_referral_link_based_on_user' ) ? $RefUsername : get_current_user_id();
					$query       = ( 'yes' == get_option( 'rs_restrict_referral_points_for_same_ip' ) ) ? array(
						'ref' => $KeyForQuery,
						'ip'  => base64_encode( get_referrer_ip_address() ),
					) : array( 'ref' => $KeyForQuery );
					$url         = esc_url_raw( add_query_arg( $query, get_option( 'rs_referral_link_refer_a_friend_form' ) ) );
					$url_in_html = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $url ), esc_url( $url ) );
					$message     = str_replace( $url, $url_in_html, $ReplaceRefFirstname );

					add_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );
					ob_start();
					wc_get_template( 'emails/email-header.php', array( 'email_heading' => $Heading ) );
					echo wp_kses_post( wpautop( stripslashes( $message ) ) );
					wc_get_template( 'emails/email-footer.php' );
					$woo_rs_msg                            = ob_get_clean();
					$headers                               = "MIME-Version: 1.0\r\n";
					$headers                              .= "Content-Type: text/html; charset=UTF-8\r\n";
					FPRewardSystem::$rs_from_name          = $RefUsername;
					FPRewardSystem::$rs_from_email_address = $RefEmail;
					add_filter( 'woocommerce_email_from_address', 'rs_alter_from_email_of_woocommerce', 10, 2 );
					add_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
					if ( '1' == get_option( 'rs_select_mail_function' ) ) {
						mail( $to, $Subject, $woo_rs_msg, $headers );
					} elseif ( (float) WC_VERSION <= (float) ( '2.2.0' ) ) {
							wp_mail( $to, $Subject, $woo_rs_msg, $headers );
					} else {
						$mailer = WC()->mailer();
						$mailer->send( $to, $Subject, $woo_rs_msg, $headers );
					}
					remove_filter( 'woocommerce_email_from_address', 'rs_alter_from_email_of_woocommerce', 10, 2 );
					remove_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
					remove_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );
				}
				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/* Validate referred email person */

		public static function validate_referred_email_person( $referred_person_email ) {

			$referral_system_log = get_option( 'rs_referral_log', array() );
			if ( ! srp_check_is_array( $referral_system_log ) ) {
				return true;
			}

			$referred_user_ids    = array();
			$referred_user_emails = array();

			foreach ( $referral_system_log as $referrer_user_id => $referral_system_data ) {

				$referred_users_data = array_keys( $referral_system_data );
				foreach ( $referred_users_data as $referred_user_id_or_email ) {

					if ( absint( $referred_user_id_or_email ) ) {
						$referred_user_ids[] = $referred_user_id_or_email;
					} else {
						$referred_user_emails[] = $referred_user_id_or_email;
					}
				}
			}

			// Validate Guest Referred emails.
			if ( in_array( $referred_person_email, $referred_user_emails ) ) {
				return false;
			}

			$referred_user = get_user_by( 'email', $referred_person_email );
			if ( is_object( $referred_user ) ) {
				$referred_user_id = $referred_user->ID;
				// Validate already Referred UserIds.
				if ( in_array( $referred_user_id, $referred_user_ids ) ) {
					return false;
				}
			}

			return true;
		}

		/* Unset Removed Product Id */

		public static function unset_removed_product_id() {
			check_ajax_referer( 'unset-product', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['key_to_remove'] ) ) {
				throw new exception( esc_html( 'Invalid URL', 'rewardsystem' ) );
			}

			try {
				$KeyToRemove = wc_clean( wp_unslash( $_POST['key_to_remove'] ) );
				$ListofIds   = array_filter( array_unique( (array) get_user_meta( get_current_user_id(), 'listsetofids', true ) ) );
				$Key         = array_search( $KeyToRemove, $ListofIds );
				unset( $ListofIds[ $Key ] );

				update_user_meta( get_current_user_id(), 'listsetofids', array_unique( $ListofIds ) );
				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function cancel_cashback_request() {
			check_ajax_referer( 'unset-product', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['status'] ) || ! isset( $_POST['id'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				global $wpdb;
				$wpdb->update( "{$wpdb->prefix}sumo_reward_encashing_submitted_data", array( 'status' => 'Cancelled' ), array( 'id' => absint( $_POST['id'] ) ) );
								$UserData = $wpdb->get_results( $wpdb->prepare( "SELECT userid,pointstoencash FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE id = %d", absint( $_POST['id'] ) ), ARRAY_A );
				foreach ( $UserData as $Data ) {
					$UserId         = $Data['userid'];
					$PointstoReturn = $Data['pointstoencash'];
				}
				$table_args = array(
					'user_id'           => $UserId,
					'pointstoinsert'    => $PointstoReturn,
					'checkpoints'       => 'RCBRP',
					'totalearnedpoints' => $PointstoReturn,
				);
				RSPointExpiry::insert_earning_points( $table_args );
				RSPointExpiry::record_the_points( $table_args );
				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function cashback_request() {
			check_ajax_referer( 'fp-cashback-request', 'sumo_security' );

			try {
				global $wpdb;
				$CashbackTable = "{$wpdb->prefix}sumo_reward_encashing_submitted_data";
				if ( isset( $_POST['wallet'] ) ) {
					$PaymentDetail = wc_clean( wp_unslash( ( $_POST['wallet'] ) ) );
				} elseif ( isset( $_POST['custom_payment_details'] ) ) {
					$PaymentDetail = wc_clean( wp_unslash( ( $_POST['custom_payment_details'] ) ) );
				} else {
					$PaymentDetail = isset( $_POST['payment_method'] ) ? wc_clean( wp_unslash( ( $_POST['payment_method'] ) ) ) : '';
				}
				$UserId                   = get_current_user_id();
				$UserInfo                 = get_user_by( 'id', $UserId );
				$Username                 = $UserInfo->user_login;
				$Points                   = isset( $_POST['points'] ) ? wc_clean( wp_unslash( $_POST['points'] ) ) : 0;
				$PointsData               = new RS_Points_Data( $UserId );
				$AvailablePoints          = $PointsData->total_available_points();
				$percentvalue             = RS_Rewardsystem_Shortcodes::get_cashback_value_based_on_user_role( $UserId );
				$currency_value           = isset( $_POST['converted_value'] ) ? wc_clean( wp_unslash( $_POST['converted_value'] ) ) : '';
				$converted_currency_value = ( 'yes' == get_option( 'rs_enable_user_role_based_reward_points_for_redeem_cashback' ) ) ? $currency_value * $percentvalue : $currency_value;
				$Reason                   = isset( $_POST['reason'] ) ? sanitize_text_field( $_POST['reason'] ) : '';
				$PaymentMethod            = isset( $_POST['payment_method'] ) ? sanitize_text_field( $_POST['payment_method'] ) : '';
				$PayPalEmail              = isset( $_POST['paypal_email'] ) ? sanitize_email( $_POST['paypal_email'] ) : '';
				update_user_meta( $UserId, 'rs_cashback_previous_payment_method', $PaymentDetail );
				update_user_meta( $UserId, 'rs_paypal_payment_details', $PayPalEmail );
				if ( isset( $_POST['custom_payment_details'] ) ) {
					update_user_meta( $UserId, 'rs_custom_payment_details', sanitize_text_field( $_POST['custom_payment_details'] ) );
				}

				$wpdb->insert(
					$CashbackTable,
					array(
						'userid'                => $UserId,
						'userloginname'         => $Username,
						'pointstoencash'        => $Points,
						'encashercurrentpoints' => $AvailablePoints,
						'reasonforencash'       => $Reason,
						'encashpaymentmethod'   => $PaymentMethod,
						'paypalemailid'         => $PayPalEmail,
						'otherpaymentdetails'   => isset( $_POST['custom_payment_details'] ) ? sanitize_text_field( $_POST['custom_payment_details'] ) : '',
						'status'                => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '',
						'pointsconvertedvalue'  => $converted_currency_value,
						'date'                  => gmdate( 'Y-m-d H:i:s' ),
					)
				);
				$redeempoints = RSPointExpiry::perform_calculation_with_expiry( $Points, $UserId );
				$table_args   = array(
					'user_id'     => $UserId,
					'usedpoints'  => $Points,
					'checkpoints' => 'CBRP',
				);
				RSPointExpiry::record_the_points( $table_args );

				/* Send mail for Admin - Start */
				if ( 'yes' == get_option( 'rs_email_notification_for_Admin_cashback' ) ) {
					$Message          = get_option( 'rs_email_message_for_cashback' );
					$CashbackReplaced = str_replace( '[username]', $Username, str_replace( '[_rs_point_for_cashback]', $Points, $Message ) );
					$PointsReplaced   = str_replace( '[rs_current_user_point]', $AvailablePoints, $CashbackReplaced );
					$PointsReplaced   = str_replace( '[rs_currency_value]', wc_price( $currency_value ), $PointsReplaced );
					if ( 'encash_through_paypal_method' == $PaymentDetail ) {
						$EmailMsg = str_replace( '[payment_details]', $PayPalEmail, str_replace( '[rs_payment_gateway]', 'Paypal Payment', $PointsReplaced ) );
					} elseif ( 'encash_through_custom_payment' == $PaymentMethod ) {
						$EmailMsg = str_replace( '[payment_details]', $PaymentDetail, str_replace( '[rs_payment_gateway]', 'Custom Payment', $PointsReplaced ) );
					} else {
						$EmailMsg = str_replace( '[rs_payment_gateway]', 'Wallet Payment', $PointsReplaced );
					}
					self::send_mail_for_admin( $EmailMsg );
				}
				/* Send mail for Admin - End */

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function send_mail_for_admin( $EmailMsg ) {
			$EmailSub   = get_option( 'rs_email_subject_message_for_cashback' );
			$AdminEmail = 'woocommerce' == get_option( 'rs_mail_sender_for_admin_for_cashback' ) ? get_option( 'admin_email' ) : get_option( 'rs_from_email_for_admin_cashback' );
			$AdminName  = 'woocommerce' == get_option( 'rs_mail_sender_for_admin_for_cashback' ) ? get_bloginfo( 'name', 'display' ) : get_option( 'rs_from_name_for_admin_cashback' );
			if ( '' != $AdminName && '' != $AdminEmail ) {
				$headers      .= "MIME-Version: 1.0\r\n";
				$headers      .= "Content-Type: text/html; charset=UTF-8\r\n";
				$headers      .= 'Reply-To: ' . $AdminName . ' <' . $AdminEmail . ">\r\n";
				$AdminEmailMsg = do_shortcode( $EmailMsg );
				ob_start();
				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $EmailSub ) );
				echo wp_kses_post( $AdminEmailMsg );
				wc_get_template( 'emails/email-footer.php' );
				$woo_temp_msg = ob_get_clean();
				if ( (float) WC()->version <= (float) ( '2.2.0' ) ) {
										wp_mail( $AdminEmail, $EmailSub, $AdminEmailMsg, $headers );
				} else {
					$mailer = WC()->mailer();
					$mailer->send( $AdminEmail, $EmailSub, $woo_temp_msg, $headers );
				}
			}
		}

		public static function redeem_gift_voucher() {
			check_ajax_referer( 'fp-redeem-voucher', 'sumo_security' );

			if ( ! isset( $_POST ) ) {
				throw new exception( esc_html( 'Invalid Code', 'rewardsystem' ) );
			}

			try {
				$newone      = array();
				$UserId      = get_current_user_id();
				$BanningType = check_banning_type( $UserId );
				if ( 'earningonly' != $BanningType && 'both' != $BanningType ) {
					global $wpdb;
					$VoucherCode                     = isset( $_POST['vouchercode'] ) ? trim( wc_clean( wp_unslash( $_POST['vouchercode'] ) ) ) : '';
										$VoucherData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rsgiftvoucher WHERE vouchercode = %s", $VoucherCode ), ARRAY_A );
					if ( ! srp_check_is_array( $VoucherData ) ) {
						throw new exception( addslashes( get_option( 'rs_invalid_voucher_code_error_message' ) ) );
					} else {
						$Date          = date_i18n( get_option( 'date_format' ) );
						$Date          = strtotime( $Date );
						$ExpDate       = $VoucherData[0]['voucherexpiry'];
						$VoucherUsedBy = isset( $VoucherData[0]['memberused'] ) && ( '' != $VoucherData[0]['memberused'] ) ? unserialize( $VoucherData[0]['memberused'] ) : array();
						if ( ! in_array( $UserId, $VoucherUsedBy ) ) {
							if ( '' != $ExpDate && 'Never' != $ExpDate ) {
								$ExpiryDate = strtotime( $ExpDate );
								if ( $ExpiryDate >= $Date ) {
									$Content = self::voucher_code_usage( $UserId, $VoucherData, $VoucherCode, $ExpDate );
								} else {
									throw new exception( addslashes( get_option( 'rs_voucher_code_expired_error_message' ) ) );
								}
							} else {
								// Coupon Never Expired
								$Content = self::voucher_code_usage( $UserId, $VoucherData, $VoucherCode, $ExpDate );
							}
						} else {
							throw new exception( addslashes( get_option( 'rs_voucher_code_used_error_message' ) ) );
						}
					}
										/**
										 * Hook:fp_reward_point_for_using_gift_voucher.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_reward_point_for_using_gift_voucher' );
				} else {
					throw new exception( addslashes( get_option( 'rs_banned_user_redeem_voucher_error' ) ) );
				}
				wp_send_json_success( array( 'content' => $Content ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function insert_points_for_voucher( $UserId, $VoucherData, $VoucherCode, $VoucherPoints ) {
			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $VoucherPoints, $pointsredeemed = 0, $event_slug     = 'RPGV', $UserId, $nomineeid      = '', $referrer_id    = '', $product_id     = '', $variationid    = '', $VoucherCode );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $VoucherPoints,
					'event_slug'        => 'RPGV',
					'user_id'           => $UserId,
					'reasonindetail'    => $VoucherCode,
					'totalearnedpoints' => $VoucherPoints,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
			$Msg = str_replace( '[giftvoucherpoints]', $VoucherPoints, get_option( 'rs_voucher_redeem_success_message' ) );
			return addslashes( $Msg );
		}

		public static function voucher_code_usage( $UserId, $VoucherData, $VoucherCode, $ExpDate ) {
			global $wpdb;
			$CodeUsage      = $VoucherData[0]['voucher_code_usage'];
			$CreatedVoucher = $VoucherData[0]['vouchercreated'];
			$VoucherUsedBy  = '' != isset( $VoucherData[0]['memberused'] ) ? unserialize( $VoucherData[0]['memberused'] ) : array();
			$VoucherPoints  = $VoucherData[0]['points'];
			if ( ! empty( $CodeUsage ) ) {
				if ( '1' == $CodeUsage ) {
					if ( ! srp_check_is_array( $VoucherUsedBy ) ) {
						$Content = self::insert_points_for_voucher( $UserId, $VoucherData, $VoucherCode, $VoucherPoints );
						$UsedBy  = serialize( array( $UserId ) );
						$wpdb->update(
							"{$wpdb->prefix}rsgiftvoucher",
							array(
								'points'         => $VoucherPoints,
								'vouchercode'    => $VoucherCode,
								'vouchercreated' => $CreatedVoucher,
								'voucherexpiry'  => $ExpDate,
								'memberused'     => $UsedBy,
							),
							array( 'id' => $VoucherData[0]['id'] )
						);
						return $Content;
					} else {
						throw new exception( esc_html(addslashes( get_option( 'rs_voucher_code_used_error_message' ) )) );
					}
				} else {
					$UsageLimit      = $VoucherData[0]['voucher_code_usage_limit'];
					$UsageLimitValue = $VoucherData[0]['voucher_code_usage_limit_val'];
					$UsedBy          = array( $UserId );
					$OldData         = '' != $VoucherData[0]['memberused'] ? unserialize( $VoucherData[0]['memberused'] ) : array();
					$UsageCount      = count( $OldData );
					if ( '1' == $UsageLimit && ! empty( $UsageLimitValue ) ) {
						if ( $UsageCount < $UsageLimitValue ) {
							$Content        = self::insert_points_for_voucher( $UserId, $VoucherData, $VoucherCode, $VoucherPoints );
							$MergedData     = array_merge( $UsedBy, $OldData );
							$SerializedData = serialize( $MergedData );
							$wpdb->update(
								"{$wpdb->prefix}rsgiftvoucher",
								array(
									'points'         => $VoucherPoints,
									'vouchercode'    => $VoucherCode,
									'vouchercreated' => $CreatedVoucher,
									'voucherexpiry'  => $ExpDate,
									'memberused'     => $SerializedData,
								),
								array( 'id' => $VoucherData[0]['id'] )
							);
							return $Content;
						} else {
							throw new exception( esc_html(addslashes( get_option( 'rs_voucher_code_used_error_message' ) )) );
						}
					} else {
						$Content        = self::insert_points_for_voucher( $UserId, $VoucherData, $VoucherCode, $VoucherPoints );
						$MergedData     = array_merge( $UsedBy, $OldData );
						$SerializedData = serialize( $MergedData );
						$wpdb->update(
							"{$wpdb->prefix}rsgiftvoucher",
							array(
								'points'         => $VoucherPoints,
								'vouchercode'    => $VoucherCode,
								'vouchercreated' => $CreatedVoucher,
								'voucherexpiry'  => $ExpDate,
								'memberused'     => $SerializedData,
							),
							array( 'id' => $VoucherData[0]['id'] )
						);
						return $Content;
					}
				}
			} else {
				$Content = self::insert_points_for_voucher( $UserId, $VoucherData, $VoucherCode, $VoucherPoints );
				$UsedBy  = serialize( array( $UserId ) );
				$wpdb->update(
					"{$wpdb->prefix}rsgiftvoucher",
					array(
						'points'         => $VoucherPoints,
						'vouchercode'    => $VoucherCode,
						'vouchercreated' => $CreatedVoucher,
						'voucherexpiry'  => $ExpDate,
						'memberused'     => $UsedBy,
					),
					array( 'id' => $VoucherData[0]['id'] )
				);
				return $Content;
			}
		}

		public static function message_for_booking() {
			check_ajax_referer( 'booking-msg', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['form'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$PostValue = array();
				parse_str( wc_clean( wp_unslash( $_POST['form'] ) ), $PostValue );
				$BookingId  = $PostValue['add-to-cart'];
				$ProductObj = srp_product_object( $BookingId );
				if ( ! $ProductObj ) {
					die( esc_html( wp_send_json_success( array( 'sumorewardpoints' => 0 ) ) ) );
				}

				if ( 'booking' != srp_product_type( $BookingId ) ) {
					die( esc_html( wp_send_json_success( array( 'sumorewardpoints' => 0 ) ) ) );
				}

				$BookingForm = new WC_Booking_Form( $ProductObj );
				$Cost        = $BookingForm->calculate_booking_cost( $PostValue );
				if ( is_wp_error( $Cost ) ) {
					die( esc_html( wp_send_json_success( array( 'sumorewardpoints' => 0 ) ) ) );
				}

				$args   = array(
					'productid'   => $BookingId,
					'variationid' => '',
					'item'        => array( 'qty' => '1' ),
				);
				$Points = check_level_of_enable_reward_point( $args );
				die( esc_html( wp_send_json_success( array( 'sumorewardpoints' => round_off_type( $Points ) ) ) ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function unsunscribe_or_subscribe_mail() {
			check_ajax_referer( 'fp-subscribe-mail', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['subscribe'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				update_user_meta( get_current_user_id(), 'unsub_value', wc_clean( wp_unslash( $_POST['subscribe'] ) ) );
				$Content = 'yes' == wc_clean( wp_unslash( $_POST['subscribe'] ) ) ? esc_html( 'Successfully Unsubscribed...', 'rewardsystem' ) : esc_html( 'Successfully Subscribed...', 'rewardsystem' );
				wp_send_json_success( array( 'content' => $Content ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function save_nominee() {
			check_ajax_referer( 'fp-save-nominee', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['selectedvalue'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				update_user_meta( get_current_user_id(), 'rs_selected_nominee', wc_clean( wp_unslash( $_POST['selectedvalue'] ) ) );
				update_user_meta( get_current_user_id(), 'rs_enable_nominee', 'yes' );
				$Content = esc_html( 'Nominee Saved', 'rewardsystem' );
				wp_send_json_success( array( 'content' => $Content ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function reward_gateway_msg() {
			check_ajax_referer( 'fp-gateway-msg', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['gatewayid'] ) || ! isset( $_POST['gatewaytitle'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$gateway_id    = wc_clean( wp_unslash( $_POST['gatewayid'] ) );
				$GatewayPoints = points_for_payment_gateways( '', get_current_user_id(), $gateway_id );
				$GatewayPoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $GatewayPoints );

				$MsgToDisplay  = '';
				$default_value = ( 'yes' == get_option( 'rs_disable_point_if_reward_points_gateway', 'no' ) ) ? array( 'reward_gateway' ) : array();
				if ( in_array( $gateway_id, get_option( 'rs_select_payment_gateway_for_restrict_reward', $default_value ) ) ) :
					$MsgToDisplay = str_replace( '[paymentgatewaytitle]', '<b>' . sanitize_text_field( $_POST['gatewaytitle'] ) . '</b>', get_option( 'rs_restriction_msg_for_selected_gateway', 'You cannot earn points if you use [paymentgatewaytitle] Gateway' ) );
				endif;

				$gateway_reward_message = '1' == get_option( 'rs_show_hide_message_payment_gateway_reward_points' ) ? str_replace( array( '[paymentgatewaytitle]', '[paymentgatewaypoints]' ), array( '<b>' . sanitize_text_field( $_POST['gatewaytitle'] ) . '</b>', '<b>' . $GatewayPoints . '</b>' ), get_option( 'rs_message_payment_gateway_reward_points' ) ) : '';
				wp_send_json_success(
					array(
						'rewardpoints'         => $GatewayPoints,
						'title'                => sanitize_text_field( $_POST['gatewaytitle'] ),
						'restrictedmsg'        => $MsgToDisplay,
						'earn_gateway_message' => $gateway_reward_message,
					)
				);
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function points_for_variation_in_product_page() {
			check_ajax_referer( 'variation-msg', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['variationid'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
								$variation_id = absint( $_POST['variationid'] );
				$VarObj                       = new WC_Product_Variation( $variation_id );
				$ParentId                     = get_parent_id( $VarObj );
				if ( isset( $_COOKIE['rsreferredusername'] ) ) {
										$cookie_name = wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) );
					$UserInfo                        = get_user_by( 'login', $cookie_name );
					$RefUserid                       = is_object( $UserInfo ) ? $UserInfo->ID : $cookie_name;
					$args                            = array(
						'productid'     => $ParentId,
						'variationid'   => $variation_id,
						'item'          => array( 'qty' => '1' ),
						'referred_user' => $RefUserid,
					);
					$RefPoints                       = check_level_of_enable_reward_point( $args );
					$RefPoints                       = empty( $RefUserid ) ? $RefPoints : RSMemberFunction::earn_points_percentage( $RefUserid, (float) $RefPoints );
					$RefCurrencyValue                = round_off_type_for_currency( redeem_point_conversion( $RefPoints, $RefUserid, 'price' ) );
					$ValueToFind                     = array( '[rsreferredusername]', '[variationreferralpoints]', '[variationreferralpointsamount]' );
					$ValueToReplace                  = array( $cookie_name, $RefPoints, wc_price( $RefCurrencyValue ) );
					$Refmsg                          = str_replace( $ValueToFind, $ValueToReplace, get_option( 'rs_message_for_variation_products_referral' ) );
				} else {
					$Refmsg = '';
				}
				$Userid       = get_current_user_id();
				$banning_type = check_banning_type( $Userid );
				if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
					wp_send_json_success( array( 'showmsg' => false ) );
				}

				$restrictpoints = block_points_for_salepriced_product( 0, $variation_id );
				if ( 'yes' == $restrictpoints ) {
					wp_send_json_success( array( 'showmsg' => false ) );
				}

				if ( '2' == get_option( 'rs_message_outofstockproducts_product_page' ) ) {
					if ( ! $VarObj->is_in_stock() ) {
						wp_send_json_success( array( 'showmsg' => false ) );
					}
				}

				$args = array(
					'productid'   => $ParentId,
					'variationid' => $variation_id,
					'item'        => array( 'qty' => '1' ),
				);

				/* Product Purchase Messages */
				$VarPoints                  = check_level_of_enable_reward_point( $args );
				$VarEarnMsg                 = '';
				$VarPurchaseMsg             = '';
				$min_quantity_error_message = '';
				if ( $VarPoints ) {
					$VarPoints      = empty( $Userid ) ? $VarPoints : RSMemberFunction::earn_points_percentage( $Userid, (float) $VarPoints );
					$VarPointsValue = redeem_point_conversion( $VarPoints, $Userid, 'price' );
					$VarPoints      = round_off_type( $VarPoints );
					$message        = is_user_logged_in() ? get_option( 'rs_message_for_single_product_variation' ) : get_option( 'rs_earn_message_single_product_variation_guest', 'Earn [variationrewardpoints] Reward Points' );
					$VarEarnMsg     = str_replace( '[variationrewardpoints]', $VarPoints, $message );
					$VarEarnMsg     = str_replace( '[variationpointsvalue]', wc_price( round_off_type_for_currency( $VarPointsValue ) ), $VarEarnMsg );
					$message        = is_user_logged_in() ? get_option( 'rs_message_for_variation_products' ) : get_option( 'rs_message_variation_single_product_guest', 'Purchase this Product and Earn [variationrewardpoints] Reward Points ([variationpointsvalue])' );
					$VarPurchaseMsg = str_replace( '[variationrewardpoints]', $VarPoints, $message );
					$VarPurchaseMsg = str_replace( '[variationpointsvalue]', wc_price( round_off_type_for_currency( $VarPointsValue ) ), $VarPurchaseMsg );

					$min_quantity = rs_get_minimum_quantity_based_on_product_total( $ParentId, $variation_id );
					if ( $min_quantity && $min_quantity > 1 ) {
						$variation                  = wc_get_product( $variation_id );
						$product_title              = is_object( $variation ) ? $variation->get_name() : '';
						$message                    = get_option( 'rs_minimum_quantity_error_message', 'Minimum <b>{min_quantity}</b> quantities required to earn points by purchasing <b>{product_name}</b>' );
						$min_quantity_error_message = str_replace( array( '{product_name}', '{min_quantity}' ), array( $product_title, $min_quantity ), $message );
					}
				}

				/* Buy Now Messages */
				$EnableBuyPoint = get_post_meta( $variation_id, '_rewardsystem_buying_reward_points', true );
				$BuyPoints      = get_post_meta( $variation_id, '_rewardsystem_assign_buying_points', true );
				$BuyPoints      = empty( $Userid ) ? $BuyPoints : RSMemberFunction::earn_points_percentage( $Userid, (float) $BuyPoints );
				$BuyPointsValue = redeem_point_conversion( $BuyPoints, $Userid, 'price' );
				$BuyPointsValue = get_woocommerce_currency_symbol() . number_format( (float) round_off_type_for_currency( $BuyPointsValue ), get_option( 'woocommerce_price_num_decimals' ) );
				$BuyMsg         = ( '1' == $EnableBuyPoint && ! empty( $BuyPoints ) ) ? str_replace( array( '[buypoints]', '[buypointvalue]' ), array( $BuyPoints, $BuyPointsValue ), get_option( 'rs_buy_point_message_in_product_page_for_variable' ) ) : '';
				$BuyingMsg      = ( '1' == $EnableBuyPoint && ! empty( $BuyPoints ) ) ? str_replace( array( '[variationbuyingpoint]', '[variationbuyingpointvalue]' ), array( $BuyPoints, $BuyPointsValue ), get_option( 'rs_buy_point_message_for_variation_products' ) ) : '';
				$ShowMsg        = empty( $Userid ) ? ( get_option( 'rs_show_hide_message_for_variable_in_single_product_page_guest' ) == '1' ) : true;

				wp_send_json_success(
					array(
						'showbuypoint'               => $EnableBuyPoint,
						'showmsg'                    => $ShowMsg,
						'refmsg'                     => $Refmsg,
						'earnpointmsg'               => $VarEarnMsg,
						'purchasemsg'                => $VarPurchaseMsg,
						'buymsg'                     => $BuyMsg,
						'buying_msg'                 => $BuyingMsg,
						'min_quantity_error_message' => $min_quantity_error_message,
					)
				);
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		// Remove Coupon from Checkout

		public static function remove_coupon() {
			if ( ! isset( $_REQUEST ) || ! isset( $_REQUEST['coupon'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				$coupon = wc_clean( $_REQUEST['coupon'] );
				if ( false !== strpos( $coupon, 'sumo_' ) || false !== strpos( $coupon, 'auto_redeem_' ) ) {
					$sumo_coupon = true;
				} else {
					$sumo_coupon = false;
				}

				wp_send_json_success( array( 'showredeemfield' => $sumo_coupon ) );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		// Send Referral Link email.
		public static function rs_send_mail_for_referral_link() {
			check_ajax_referer( 'email-referral', 'sumo_security' );
			try {
				$receiver_mail_ids = isset( $_REQUEST['receiver_ids'] ) ? wc_clean( wp_unslash( $_REQUEST['receiver_ids'] ) ) : '';
				$ref_link          = isset( $_REQUEST['ref_link'] ) ? wc_clean( $_REQUEST['ref_link'] ) : '';
				$message           = get_option( 'rs_email_referral_message', 'Hi,<br/>Please make use of this link([site_referral_url]) & earn points for the actions offered[eg. Account Signup, Product Purchase, Referring Others etc] in the site.<br/><br/><br/>Regards.' );
				$Email_subject     = get_option( 'rs_email_referral_subject', 'Referral Link' );
				$admin_email_id    = get_option( 'admin_email' );
				$receiver_mails    = '' !== $receiver_mail_ids ? explode( ',', str_replace( ' ', '', $receiver_mail_ids ) ) : array();
				$error             = '';

				$UserInfo     = get_userdata( get_current_user_id() );
				$RefUsername  = $UserInfo->user_login;
				$RefFirstname = $UserInfo->first_name;
				$RefLastname  = $UserInfo->last_name;
				$RefEmail     = $UserInfo->user_email;
				$ref_link     = "<a href='$ref_link' target='_blank'>$ref_link</a>";
				$message      = str_replace(
					array(
						'[site_referral_url]',
						'[rs_referrer_name]',
						'[rs_referrer_first_name]',
						'[rs_referrer_last_name]',
						'[rs_referrer_email_id]',
					),
					array(
						$ref_link,
						$RefUsername,
						$RefFirstname,
						$RefLastname,
						$RefEmail,
					),
					$message
				);

				if ( ! srp_check_is_array( $receiver_mails ) && '' === $admin_email_id && '' == $message && '' == $ref_link ) {
					throw new exception( esc_html( 'Something went wrong!', 'rewardsystem' ) );
				}

				foreach ( $receiver_mails as $receiver_mail ) {
					if ( is_email( $receiver_mail ) ) {
						add_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );
						ob_start();
						wc_get_template( 'emails/email-header.php', array( 'email_heading' => $Email_subject ) );
						echo wp_kses_post( $message );
						wc_get_template( 'emails/email-footer.php' );
						$woo_temp_msg     = ob_get_clean();
						$message_headers  = "MIME-Version: 1.0\r\n";
						$message_headers .= "From: \" <{$admin_email_id}>\n" . 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n";
						$message_headers .= 'Reply-To: <' . $receiver_mail . ">\r\n";
						add_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
						if ( WC_VERSION <= (float) ( '2.2.0' ) ) {
							wp_mail( $receiver_mail, $Email_subject, $message, $message_headers );
						} else {
							$mailer = WC()->mailer();
							$mailer->send( $receiver_mail, $Email_subject, $woo_temp_msg, $message_headers );
						}
						remove_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 1 );
					} else {
						$error .= '{' . $receiver_mail . '} ';
					}
				}

				if ( '' !== $error ) {
					throw new exception( esc_html( 'Invalid Email!', 'rewardsystem' ) );
				}

				wp_send_json_success( esc_html( 'Mail Shared Successfully!', 'rewardsystem' ) );
			} catch ( Exception $e ) {
				wp_send_json_error(
					array(
						'error' => $e->getMessage(),
						'ids'   => $error,
					)
				);
			}
		}

		// User Email Unsubscribe.
		public static function rs_user_email_unsubscribe() {
			check_ajax_referer( 'user-email-unsubscribe', 'sumo_security' );

			try {
				$user_id = isset( $_POST['user_id'] ) && ( '' !== $_POST['user_id'] ) ? wc_clean( wp_unslash( $_POST['user_id'] ) ) : '';

				if ( '' === $user_id ) {
					throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
				}

				update_user_meta( $user_id, 'unsub_value', 'yes' );
				wp_send_json_success();

			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		public static function srp_rp_apply_coupon() {
			check_ajax_referer( 'rp-apply-coupon', 'sumo_security' );

			try {
				// Check if applied coupons.
				$AppliedCoupons = WC()->cart->get_applied_coupons();

				if ( srp_check_is_array( $AppliedCoupons ) ) {
					$UserInfo   = get_user_by( 'id', get_current_user_id() );
					$Username   = $UserInfo->user_login;
					$Redeem     = 'sumo_' . strtolower( "$Username" );
					$AutoRedeem = 'auto_redeem_' . strtolower( "$Username" );
					if ( 'yes' === get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) ) {
						foreach ( $AppliedCoupons as $Code ) {
							$CouponObj         = new WC_Coupon( $Code );
							$CouponObj         = srp_coupon_obj( $CouponObj );
							$CouponId          = $CouponObj['coupon_id'];
							$CheckIfSUMOCoupon = get_post_meta( $CouponId, 'sumo_coupon_check', true );
							if ( 'yes' == $CheckIfSUMOCoupon ) {
								$cart_redeem     = 'hide';
								$checkout_redeem = 'hide';
							}
						}
					}

					if ( in_array( $Redeem, $AppliedCoupons ) || in_array( $AutoRedeem, $AppliedCoupons ) ) {
						$redeem_field = array(
							'coupon_field'    => ( '5' == get_option( 'rs_show_hide_redeem_field' ) ) ? 'hide' : 'show',
							'cart_redeem'     => 'hide',
							'checkout_redeem' => 'hide',
						);
					} elseif ( '5' === get_option( 'rs_show_hide_redeem_field' ) ) {
							$redeem_field = array(
								'coupon_field'    => 'hide',
								'cart_redeem'     => 'hide',
								'checkout_redeem' => 'hide',
							);
					} else {
						$redeem_field = array(
							'coupon_field'    => 'show',
							'cart_redeem'     => 'show',
							'checkout_redeem' => 'show',
						);
					}

					wp_send_json( $redeem_field );
				}

				$HideRedeemField = get_option( 'rs_show_redeeming_field' );
				if ( '1' === get_option( 'rs_show_hide_redeem_field' ) ) {
					$coupon_field = 'show';
					if ( 2 !== $HideRedeemField && check_if_discount_applied() ) {
						$cart_redeem     = 'hide';
						$checkout_redeem = 'hide';
					} else {
						$cart_redeem     = 'show';
						$checkout_redeem = 'show';
					}
				} elseif ( '2' === get_option( 'rs_show_hide_redeem_field' ) ) {
					$coupon_field = 'hide';
					if ( 2 == $HideRedeemField && check_if_discount_applied() ) {
						$cart_redeem     = 'hide';
						$checkout_redeem = 'hide';
					} else {
						$cart_redeem     = 'show';
						$checkout_redeem = 'show';
					}
				} elseif ( '3' === get_option( 'rs_show_hide_redeem_field' ) ) {
					$coupon_field    = 'show';
					$cart_redeem     = 'hide';
					$checkout_redeem = 'hide';
				} elseif ( '4' == get_option( 'rs_show_hide_redeem_field' ) ) {
					$coupon_field    = 'hide';
					$cart_redeem     = 'hide';
					$checkout_redeem = 'hide';
				} else {
					$coupon_field    = 'show';
					$cart_redeem     = 'show';
					$checkout_redeem = 'show';
				}

				wp_send_json(
					array(
						'coupon_field'    => $coupon_field,
						'cart_redeem'     => $cart_redeem,
						'checkout_redeem' => $checkout_redeem,
					)
				);

			} catch ( Exception $ex ) {
				wp_send_json_error();
			}
		}
	}

	FP_Rewardsystem_Frontend_Ajax::init();
}
