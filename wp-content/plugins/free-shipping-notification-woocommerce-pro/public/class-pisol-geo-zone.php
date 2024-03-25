<?php

class Pisol_woo_geo_location{
    public $shipping_zone;
    function __construct(){
        $this->shipping_zone = $this->getShippingZone();
    }

    protected function destination(){
        $geo_instance  = new WC_Geolocation();
        $user_ip  = $geo_instance->get_ip_address();
        $user_geodata = $geo_instance->geolocate_ip($user_ip);
        
        $destination['destination']['country'] =  $user_geodata['country'];
        $destination['destination']['state'] =  $user_geodata['state'];
        $destination['destination']['postcode'] = "";
        return $destination;
    }

    function getShippingZone(){
        $destination = $this->destination();
        $shipping_zone = WC_Shipping_Zones::get_zone_matching_package( $destination );
        return  $shipping_zone ;
    }

    function getShippingZoneId(){
        return $this->shipping_zone->get_id();
    }

    function getMethods(){
        $methods = $this->shipping_zone->get_shipping_methods( true );
        return $methods;
    }

    function hasFreeShippingMethod(){
        $methods = $this->getMethods();
        foreach($methods as $method){
            if($method->id == 'free_shipping'){
                return true;
            }
        }
        return false;
    }

    function getFreeShippingMethod(){
        $methods = $this->getMethods();
        foreach($methods as $method){
            if($method->id == 'free_shipping'){
                return 'free_shipping:'.$method->instance_id;
            }
        }
        return false;
    }
}