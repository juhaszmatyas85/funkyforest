<?php

class pisol_fsn_popup_msg{
    function __construct(){
        add_action( 'wp_enqueue_scripts', array( $this,  'addJs') );
        add_action( 'wp_enqueue_scripts', array( $this,  'addInlineCss') );
    }

    function addJs(){
        $values = array(
            'enabled'=> get_option('pi_fsnw_enabled_popup',0),
            'initial_load'=> $this->initialLoad(),
            'disable_refresh_fragment' => $this->disableRefreshFragment(),
            'closing_option'=>get_option('pi_fsnw_popup_closing_option','normal'),
            'lang'=>get_locale()
        );

        wp_enqueue_script( 'pisol-fsnw-magnifypopup', plugin_dir_url( __FILE__ ) . 'js/jquery.magnific-popup.min.js', array('jquery'), PISOL_FSNW_VERSION, false );
        wp_enqueue_style( 'pisol-fsnw-magnifypopup', plugin_dir_url( __FILE__ ) . 'css/magnific-popup.css');
        wp_localize_script('pisol-fsnw-magnifypopup', 'pisol_fsnw_popup', $values);
    }

    function initialLoad(){
        if(is_product()){
            if(isset($_POST['add-to-cart']) && isset($_POST['quantity'])){
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * refresh fragment is only use full on cart and checkout page, on remaining    * page it causes popup on initial load
     */
    function disableRefreshFragment(){
        if(is_cart() || is_checkout()){
            return apply_filters('pisol_fsnw_disable_refresh_fragment', false);
        }
        return apply_filters('pisol_fsnw_disable_refresh_fragment', true);
    }
    

    function addInlineCss(){
        $background_color = get_option('pi_fsnw_popup_background_color','#cccccc');
        $background_color_reached = get_option('pi_fsnw_popup_background_color_reached','#cccccc');
        $text_color = get_option('pi_fsnw_popup_text_color','#ffffff');
        $fg_color = get_option('pi_fsnw_popup_foreground_color','#ffffff');
        $close_color = get_option('pi_fsnw_popup_close_color','#ffffff');
        $width = get_option('pi_fsnw_popup_width','50');
        $mobile_width = get_option('pi_fsnw_popup_mobile_width','100');
        $pi_fsnw_mobile_breakpoint = (int)get_option('pi_fsnw_mobile_breakpoint',768);
        $css = "
        .mfp-bg{
            background-color:{$fg_color} !important;
        }

        .mfp-close{
            color:{$close_color} !important;
        }

        .pisol-popup{
            background-color:{$background_color} !important;
            color:{$text_color} !important;
            width: {$width}% !important;
        }

        .pisol-popup.requirement-completed{
            background-color:{$background_color_reached} !important;
        }

        @media (max-width:{$pi_fsnw_mobile_breakpoint}px){
            .pisol-popup{
                width: {$mobile_width}% !important;
            }
        }
        ";
        wp_register_style( 'pi-fsnw-popup-dummy', false );
        wp_enqueue_style( 'pi-fsnw-popup-dummy' );
        /*wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css');*/
        wp_add_inline_style('pi-fsnw-popup-dummy' , $css );
    }
}

add_action('wp_loaded', function( ){
    new pisol_fsn_popup_msg();
});