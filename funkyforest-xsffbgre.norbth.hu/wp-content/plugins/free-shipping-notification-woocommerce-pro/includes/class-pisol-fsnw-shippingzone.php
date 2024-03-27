<?php 

class pisol_fsnw_shippingzone{

    public $selected_shipping_zone;
    public $free_shipping_class;
    public $free_shipping_minimum_order;
    public $is_min_amount_and_coupon = false;
    public $ignore_discounts;

    function __construct(){
        /* we get the selected shipping zone */
        $this->selected_shipping_zone = $this->getUserSelectedClass();

        $this->show_default_shipping_zone_when_no_zone_selected = get_option('pi_fsnw_dont_show_notification_till_zone_selected',0);

        $this->show_default_shipping_zone_when_no_zone_selected = ($this->show_default_shipping_zone_when_no_zone_selected == "" ? 0 : $this->show_default_shipping_zone_when_no_zone_selected);
        /* if user has not yet selected a shipping zone then we use default shipping zone from setting */
        if($this->show_default_shipping_zone_when_no_zone_selected == 0){
            $this->selected_shipping_zone = $this->selected_shipping_zone === false ? $this->getDefaultShippingZone() : $this->selected_shipping_zone;
        }else{
            $this->selected_shipping_zone = $this->selected_shipping_zone;
        }

        $this->free_shipping_class = $this->getFreeShippingClass();
        $this->free_shipping_minimum_order = $this->getFreeShippingMinimum();
        $this->is_min_amount_and_coupon = $this->minAmountAndCoupon();
        $this->is_min_amount_or_coupon = $this->minAmountORCoupon();
        $this->ignore_discounts = $this->ignoreDiscounts();
    }

    function checkDefaultShippingZone(){
        $default  = get_option('pi_fsnw_default_shipping_zone', false);
        if($default == "" || $default == false ){
            return false;
        }
        return true;
    }

     function getDefaultShippingZone(){
        if($this->checkDefaultShippingZone()) return get_option('pi_fsnw_default_shipping_zone',false);
        return false;        
    }

    function getUserSelectedClass(){
        global $woocommerce;
        $geo_zone = new Pisol_woo_geo_location();
       
        $geo_zone_methods = $geo_zone->getMethods();
       

		if( isset(WC()->session) || $geo_zone->hasFreeShippingMethod() || isset($_POST['shipping_method'][0]) ):

		if((!empty(WC()->session) && is_array(WC()->session->get( 'chosen_shipping_methods' ))) || (isset($_GET['wc-ajax']) && $_GET['wc-ajax'] == 'update_order_review') ) {
			if(isset($_POST['shipping_method'][0])){
				$selection  = $_POST['shipping_method'];
			}elseif(is_array(WC()->session->get( 'chosen_shipping_methods' ))){
				$selection = WC()->session->get( 'chosen_shipping_methods' );
			}else{
                $shipping_zone_id = $geo_zone->getShippingZoneId();
                $selection[0] = $geo_zone->getFreeShippingMethod();
            }
			if(isset($selection[0])):
                $val = explode(":",$selection[0]);
                if(isset($val[1])){
					$this->selected_shipping_method = (int)$val[1];
				}else{
                    $this->selected_shipping_method = false;
                    return false;
				}
                $method = WC_Shipping_Zones::get_shipping_method($this->selected_shipping_method );

                if($method == false) return false;

                $shipping_zones = WC_Shipping_Zones::get_zones( );
                if(!isset($shipping_zone_id)){
                    foreach($shipping_zones as $shipping_zone){
                        $shipping_zone_id = $shipping_zone['zone_id'];
                        $shipping_zone_obj = WC_Shipping_Zones::get_zone($shipping_zone_id);
                        $shipping_methods = $shipping_zone_obj->get_shipping_methods();
                        foreach($shipping_methods as $shipping_method){
                            $shipping_method->instance_id = $shipping_method->instance_id;
                            $method->instance_id = $method->instance_id;
                            if($shipping_method->instance_id == $method->instance_id){
                                return $shipping_zone_id;
                            }
                        }
                    }
                }else{
                    return $shipping_zone_id;
                }

			endif;
		}
        endif;

        return false;
		
	}

     function checkFreeShippingAvailable(){
			global $wpdb;
			$wfspb_query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = %s AND is_enabled = %d AND zone_id = %d ORDER BY method_order ASC", 'free_shipping', 1, $this->selected_shipping_zone );
			$zone_data   = $wpdb->get_results( $wfspb_query, OBJECT );

			if ( empty( $zone_data ) ) {
				return false;
			} else {
				return $zone_data;
			}

    }

     function getFreeShippingClass(){
        return $this->checkFreeShippingAvailable();
    }

    function ignoreDiscounts(){
        if ( $this->free_shipping_class ) {
            foreach($this->free_shipping_class as $shipping_class){
                $first_zone       = $shipping_class;
                $instance_id      = $first_zone->instance_id;
                $method_id        = $first_zone->method_id;
                $arr_method       = array( $method_id, $instance_id );
                $implode_method   = implode( "_", $arr_method );
                $free_option      = 'woocommerce_' . $implode_method . '_settings';
                $free_shipping_s  = get_option( $free_option );
                if(isset($free_shipping_s['ignore_discounts']) && $free_shipping_s['ignore_discounts'] == 'yes') return 'yes';
            }
        }
        return 'no';
    }
    /**
     * This can return 2 thing
     * number > 0 = there is minimum restriction
     * 0 = this means free shipping is available to all
     * false = there is no free shipping method
     */
     function getFreeShippingMinimum(){
        if ( $this->free_shipping_class ) {
            foreach($this->free_shipping_class as $shipping_class){
                $first_zone       = $shipping_class;
                $instance_id      = $first_zone->instance_id;
                $method_id        = $first_zone->method_id;
                $arr_method       = array( $method_id, $instance_id );
                $implode_method   = implode( "_", $arr_method );
                $free_option      = 'woocommerce_' . $implode_method . '_settings';
                $free_shipping_s  = get_option( $free_option );
                if(isset($free_shipping_s['min_amount']) && $free_shipping_s['min_amount'] > 0 && isset($free_shipping_s['requires']) && ($free_shipping_s['requires'] == "min_amount" || $free_shipping_s['requires'] == "either" || $free_shipping_s['requires'] == "both")){
                    $order_min_amount = $free_shipping_s['min_amount'];
                    return (float)$order_min_amount;
                }elseif(!isset($free_shipping_s['requires']) || $free_shipping_s['requires'] == ""){
                    $order_min_amount = 0;
                    return $order_min_amount;
                }
            }

            return false;
        } else {
            return false;
        }
    }

    function minAmountAndCoupon(){
        if ( $this->free_shipping_class ) {
            foreach($this->free_shipping_class as $shipping_class){
                $first_zone       = $shipping_class;
                $instance_id      = $first_zone->instance_id;
                $method_id        = $first_zone->method_id;
                $arr_method       = array( $method_id, $instance_id );
                $implode_method   = implode( "_", $arr_method );
                $free_option      = 'woocommerce_' . $implode_method . '_settings';
                $free_shipping_s  = get_option( $free_option );
                if(isset($free_shipping_s['min_amount']) && $free_shipping_s['min_amount'] > 0 && isset($free_shipping_s['requires']) && $free_shipping_s['requires'] == "both"){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }

    function minAmountOrCoupon(){
        if ( $this->free_shipping_class ) {
            foreach($this->free_shipping_class as $shipping_class){
                $first_zone       = $shipping_class;
                $instance_id      = $first_zone->instance_id;
                $method_id        = $first_zone->method_id;
                $arr_method       = array( $method_id, $instance_id );
                $implode_method   = implode( "_", $arr_method );
                $free_option      = 'woocommerce_' . $implode_method . '_settings';
                $free_shipping_s  = get_option( $free_option );
                if(isset($free_shipping_s['min_amount']) && $free_shipping_s['min_amount'] > 0 && isset($free_shipping_s['requires']) && $free_shipping_s['requires'] == "either"){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }


}