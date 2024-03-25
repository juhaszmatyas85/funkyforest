<?php

class pisol_fsnw_min{
    function __construct(){
        $shipping_obj = new pisol_fsnw_shippingzone();
        $this->min_order = $this->currencyConvertedMinimumAmount($shipping_obj->free_shipping_minimum_order);
        $this->total = $this->calculateCartTotal($shipping_obj);
        $this->missing_amount = $this->missingAmount();
        $this->is_min_amount_and_coupon = $shipping_obj->is_min_amount_and_coupon;
        $this->is_min_amount_or_coupon = $shipping_obj->is_min_amount_or_coupon;
    }

    function calculateCartTotal($shipping_obj){
        /**
         * this code is similar to woocommerce free_shipping method code
         * https://github.com/woocommerce/woocommerce/blob/c15488d8402d149a1a6551d73057d31a0730bddb/includes/shipping/free-shipping/class-wc-shipping-free-shipping.php
         */
        if(!isset(WC()->cart)) return 0;
        
        $total = WC()->cart->get_displayed_subtotal();
        
        if ( WC()->cart->display_prices_including_tax() ) {
            $total = $total - WC()->cart->get_discount_tax();
        }

        if ( 'no' === $shipping_obj->ignore_discounts ) {
            $total = $total - WC()->cart->get_discount_total();
        }

        $total = self::round( $total, wc_get_price_decimals() );

        return $total;
    }

    public static function round( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ) : float {
		if ( ! is_numeric( $val ) ) {
			$val = floatval( $val );
		}
		return round( $val, $precision, $mode );
	}

    function missingAmount(){
        if($this->min_order === false) return false;
        
        if($this->min_order > 0){
            $missing_amount = $this->min_order - $this->total;
        }else{
            $missing_amount = 0;
        }
        return $missing_amount;
    }

    function getMinOrder(){
        return $this->min_order;
    }

    function getCartTotal(){
        return  $this->total;
    }

    function getMissingAmount(){
        return  $this->missing_amount;
    }

    
    function currencyConvertedMinimumAmount($min_order){
        global $WOOCS;

        /**
         * Integration for the plugin 
         * https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
         */
        if(function_exists('wcpbc_the_zone') && is_object(wcpbc_the_zone())){
            /**
             * wc_price_based_country_shipping_exchange_rate, the plugin has the option whether you want to use the 
             * currency conversion on shipping rate or not
             */
            if ( 'yes' === get_option( 'wc_price_based_country_shipping_exchange_rate', 'no' ) ) {
            $converted_min = wcpbc_the_zone()->get_exchange_rate_price($min_order);
            }else{
               return $min_order;
            }
            return $converted_min;
        }elseif(isset($WOOCS) && is_object($WOOCS) && method_exists($WOOCS, 'woocs_exchange_value' )){
            /**
             * support for currency switcher 
             * https://wordpress.org/plugins/woocommerce-currency-switcher/
             */
            return $WOOCS->woocs_exchange_value($min_order);
        }else{
            return $min_order; 
        }
    }
}