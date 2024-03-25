<?php

class pi_fsnw_common{

    public static function getMessage($variable, $default){
        $message = get_option($variable, $default);
        
        return apply_filters('pi_fsnw_message_filters',$message, $variable);
    }
    
}