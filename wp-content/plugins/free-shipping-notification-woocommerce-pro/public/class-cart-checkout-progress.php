<?php

class pisol_fsnw_cart_checkout_progress_bar{
    function __construct(){
        $this->enable_cart = get_option('pi_fsnw_inside_cart_total',0);
        $this->enable_checkout = get_option('pi_fsnw_inside_checkout_total',0);
        $this->cart_position = get_option('pi_fsnw_inside_cart_position','woocommerce_cart_totals_before_shipping');
        $this->checkout_position = get_option('pi_fsnw_inside_checkout_position','woocommerce_review_order_before_shipping');
        if(!empty($this->enable_cart)){
            add_action($this->cart_position, array($this, 'cartNotificationBar'));
        }

        if(!empty($this->enable_checkout)){
            add_action($this->checkout_position, array($this, 'checkoutNotificationBar'));

            /**
             * needed as when shipping method changes shipping method based discount rule get applied based on the old shipping method
             */
            add_filter('woocommerce_update_order_review_fragments', array(__CLASS__, 'detectChangeShippingMethod'));
        }

        
    }

    function template($message, $type = ''){

        $progress_title = get_option('pi_fsnw_cc_progress_bar_text','Free shipping');

        if($this->min_order === false) return;

        if($this->min_order !== false){
            if($this->min_order > 0 ){
                $percent = ($this->total / $this->min_order)*100;
            }else{
                $percent = 100;
            }
        }else{
            $percent = false;
        }

        if($percent > 100) $percent = 100;


        if(empty($type)){
        printf('<div class="pi-fsnw-cart-message">%s</div>',$message);
        printf('<div class="pi-fsnw-container"><div class="pi-inner-content">%s</div>
        <div class="pi-fsnw-container-progress" style="height:24px;width:%s%%"></div></div>', $progress_title, $percent);
        }else{
            echo '<tr id="pi-fsnw-row"><td colspan="2">';
            printf('<div class="pi-fsnw-cart-message">%s</div>',$message);
            printf('<div class="pi-fsnw-container"><div class="pi-inner-content">%s</div>
            <div class="pi-fsnw-container-progress" style="height:24px;width:%s%%"></div></div>', $progress_title, $percent);
            echo '</td></tr>';
        }
    }

    function cartNotificationBar(){
        $this->notificationBar('table');
    }

    function checkoutNotificationBar(){
        if(!is_ajax()) return;
        $this->notificationBar('table');
    }

    function notificationBar($type = ''){
            $this->initializeFunction();
            $final_message = $this->createMessage();

            if($this->hideMessageTillCouponAdded()){
                $this->min_order = false;
                $final_message = "";
            }

            $this->template($final_message, $type);
    }

    function initializeFunction(){
        $obj = new pisol_fsnw_min();
        $this->min_order = $obj->getMinOrder();
        $this->total = $obj->getCartTotal();
        $this->missing_amount = $obj->getMissingAmount();
        $this->is_min_amount_and_coupon = $obj->is_min_amount_and_coupon;
        $this->is_min_amount_or_coupon = $obj->is_min_amount_or_coupon;
        $this->free_shipping_coupon_added = pisol_fsnw_ajax::isFreeShippingCouponAdded();

        if($this->is_min_amount_or_coupon && $this->free_shipping_coupon_added){
            $this->missing_amount = 0;
            $this->min_order = $this->total;
        }
    }

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

        $final_message = pisol_fsnw_ajax::searchReplace($message, $values);
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

    function hideMessageTillCouponAdded(){
        $hide_msg_till_coupon_added_for_AND_condition = get_option('pi_fsn_dont_show_till_coupon_added',1);
        
        if($this->is_min_amount_and_coupon && $this->free_shipping_coupon_added == false &&  !empty($hide_msg_till_coupon_added_for_AND_condition)){
            return true;
        }
        return false;
    }

    static function detectChangeShippingMethod($values){
        $chosen_method = WC()->session->get( 'chosen_shipping_methods' );
        $values['old_method'] = self::oldShippingMethod();
        $values['new_method'] = (is_array($chosen_method) && !empty($chosen_method)) ? $chosen_method[0] : false;
        return $values;
    }

    static function oldShippingMethod(){
        $shipping_method = false;
        if(isset($_POST['post_data'])){
            parse_str($_POST['post_data'], $values);
            $shipping_method = isset($values['shipping_method']) && is_array($values['shipping_method']) ? $values['shipping_method'][0] : false;
        }
        return $shipping_method;
    }
}

new pisol_fsnw_cart_checkout_progress_bar();