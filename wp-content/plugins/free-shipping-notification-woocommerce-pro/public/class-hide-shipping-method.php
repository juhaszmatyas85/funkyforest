<?php

class Pisol_fsnw_hide_shipping_method{
    function __construct(){
        $this->to_hide = get_option('pi_fsnw_hide_other_shipping','dont_hide');

        if($this->to_hide != 'dont_hide'){
        add_filter( 'woocommerce_package_rates', array($this,'hideShippingMethods') , 10, 1 );
        }
    }

    function hideShippingMethods( $available_methods ){
        

        switch( $this->to_hide ){
            case 'hide_all':
                if($this->isThereFreeShipping( $available_methods )){
                    $methods = $this->getFreeMethods( $available_methods );
                    return $methods;
                }
            break;

            case 'hide_all_exclude_local_pickup':
                if($this->isThereFreeShippingExcludeLocalPickup( $available_methods )){
                    $methods = $this->getFreeMethodsAndLocalPickup( $available_methods );
                    return $methods;
                }
            break;
        }

        return $available_methods;
    }

    function isThereFreeShipping( $available_methods ){
        foreach($available_methods as $key => $method){
            $cost = $method->get_cost( );
            if( $cost == 0 ){
                return true;
            }
        }
        return false;
    }

    function isThereFreeShippingExcludeLocalPickup( $available_methods ){
        foreach($available_methods as $key => $method){
            $cost = $method->get_cost( );
            $method_id = $method->get_method_id( );
            if( $cost == 0  && $method_id != 'local_pickup'){
                return true;
            }
        }
        return false;
    }


    function getFreeMethods( $available_methods ){
        foreach($available_methods as $key => $method){
            $cost = $method->get_cost( );
            if( $cost != 0 ){
                unset($available_methods[$key]);
            }
        }
        return $available_methods;
    }

    function getFreeMethodsAndLocalPickup( $available_methods ){
        foreach($available_methods as $key => $method){
            $cost = $method->get_cost( );
            $method_id = $method->get_method_id( );
            if( $cost != 0 && $method_id != 'local_pickup'){
                unset($available_methods[$key]);
            }
        }
        return $available_methods;
    }
}

new Pisol_fsnw_hide_shipping_method();