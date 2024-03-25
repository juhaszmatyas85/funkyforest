<?php

class pisol_fsnw_ajax{
    
        function __construct(){
            $free_shipping_notification = get_option('pi_fsnw_enabled',1);
            $pi_fsnw_default_shipping_zone = get_option('pi_fsnw_default_shipping_zone',0);

            $this->enable_shortcode = get_option('pi_fsn_enable_shortcode',0);
    
            add_action( 'wp_ajax_get_cart_fsnw', array( $this, 'ajaxResponse' ) );
            add_action( 'wp_ajax_nopriv_get_cart_fsnw', array( $this, 'ajaxResponse' ) );
        }

        function initializeFunction(){
            $obj = new pisol_fsnw_min();
            $this->min_order = $obj->getMinOrder();
            $this->total = $obj->getCartTotal();
            $this->missing_amount = $obj->getMissingAmount();
            $this->is_min_amount_and_coupon = $obj->is_min_amount_and_coupon;
            $this->is_min_amount_or_coupon = $obj->is_min_amount_or_coupon;
            $this->free_shipping_coupon_added = self::isFreeShippingCouponAdded();

            if($this->is_min_amount_or_coupon && $this->free_shipping_coupon_added){
                $this->missing_amount = 0;
                $this->min_order = $this->total;
            }
        }

        static function isFreeShippingCouponAdded(){
            if(function_exists('WC') && isset(WC()->cart)){
                $coupons = WC()->cart->get_coupons();

                if ( $coupons ) {
                    foreach ( $coupons as $code => $coupon ) {
                        if ( $coupon->is_valid() && $coupon->get_free_shipping() ) {
                            return true;
                            break;
                        }
                    }
                }
            }
            return false;
        }

        function ajaxResponse(){
            if(! defined('DOING_AJAX')){
                define('DOING_AJAX', true);
            }
            $this->initializeFunction();
            $final_message = $this->createMessage();

            if($this->hideMessageTillCouponAdded()){
                $this->min_order = false;
                $final_message = "";
            }

            if($this->min_order !== false){
                if($this->min_order > 0 ){
                    $percent = ($this->total / $this->min_order)*100;
                }else{
                    $percent = 100;
                }
            }else{
                $percent = false;
            }

            $json = array('message_bar'=> $final_message, 'min_order'=> $this->min_order, 'total'=> $this->total, 'percent'=>$percent);
            $json = apply_filters('pisol_fsnw_final_ajax_filter', $json);
            echo json_encode($json);
            die;
        }

        /**
         * disable showing of message until free shipping coupon is added
         * for Free shipping for min amount AND Coupon
         * and setting of not to show until coupon is enabled
         */
        function hideMessageTillCouponAdded(){
            $hide_msg_till_coupon_added_for_AND_condition = get_option('pi_fsn_dont_show_till_coupon_added',1);
            
            if($this->is_min_amount_and_coupon && $this->free_shipping_coupon_added == false &&  !empty($hide_msg_till_coupon_added_for_AND_condition)){
                return true;
            }
            return false;
        }

         /**
         * This is must as it has initialize function 
         * that sets various variables
         */
        function createMessage(){
           
    
            $icon_id = get_option('pi_fsnw_shipping_icon_img',"");
            if($icon_id != ""){
                $img_url = wp_get_attachment_image($icon_id, 'full', true);
            }else{
                $img_url = '<img src="'.plugin_dir_url( __FILE__ )."img/icon.svg".'">';
            }
    
            $values = array(
                'minimum_order' => wc_price($this->min_order),
                'cart_total' => wc_price($this->total),
                'missing_amount' => wc_price($this->missing_amount),
                'icon'=> $img_url
            );
            $message = $this->getMessageText();
    
            $final_message = self::searchReplace($message, $values);
            return $final_message;
        }
       
        function getMessageText(){
            $cart_page_id = wc_get_page_id( 'checkout' );
            $cart_page_url = $cart_page_id ? get_permalink( $cart_page_id ) : '';
    
            if($this->min_order === false){
                return "";
            }
    
            if($this->min_order > 0){
                $percent  = ( $this->total / $this->min_order ) *100;
            }else{
                $percent = 100;
            }
            $message = "";
            if($percent == 0 && $this->min_order > 0){
                if($this->is_min_amount_and_coupon){
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_exact_0_and','Free shipping for order above {minimum_order} with a coupon code');
                }else{
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_exact_0','Free shipping for order above {minimum_order}');
                }
            }elseif($percent > 0 && $percent <= 50 && $this->min_order > 0){
                if($this->is_min_amount_and_coupon){
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_0_and','You have purchased {cart_total} of {minimum_order}, Buy {missing_amount} worth products more and add the coupon code to get the free shipping');
                }else{
                     $message = pi_fsnw_common::getMessage('pi_fsnw_message_0','You have purchased {cart_total} of {minimum_order}, Buy {missing_amount} worth products more to get the free shipping');
                }
            }elseif($percent >  50 && $percent < 100 && $this->min_order > 0){
                if($this->is_min_amount_and_coupon){
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_50_and','You are almost there, Buy {missing_amount} worth products more and add the coupon code to get the free shipping');
                }else{
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_50','You are almost there, Buy {missing_amount} worth products more to get the free shipping');
                }
            }elseif($percent >= 100 && $this->min_order > 0){
                if($this->is_min_amount_and_coupon){
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_100_and','You are now qualified for the Free shipping, go to <a href="'.$cart_page_url.'">Checkout</a> and add the free shipping coupon code');
                }else{
                    $message = pi_fsnw_common::getMessage('pi_fsnw_message_100','You are now qualified for the Free shipping, go to <a href="'.$cart_page_url.'">Checkout</a>');
                }
            }elseif($percent >= 100 && $this->min_order === 0){
                $message = pi_fsnw_common::getMessage('pi_fsnw_normal_free_shipping_message','Free Shipping');
            }
            return $message;
        }
    
        static function searchReplace($message, $values){
    
            foreach($values as $key => $value){
                $open_tag = '<span class="pisol_shortcodes pisol_'.$key.'">';
                $close_tag = '</span>';
                $message = str_replace('{'.$key.'}', $open_tag.$value.$close_tag, $message);
            }
            return $message;
        }
    
}

new pisol_fsnw_ajax();