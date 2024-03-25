<?php

class pisl_fsnw_message{

    function __construct(){
        $free_shipping_notification = get_option('pi_fsnw_enabled',1);
        $pi_fsnw_default_shipping_zone = get_option('pi_fsnw_default_shipping_zone',0);

        $control_obj = new pisol_fsnw_control_display();

        $control = $control_obj->toShowHide();

        $this->enable_shortcode = get_option('pi_fsn_enable_shortcode',0);

        $this->initializeFunction();
        
        if($this->enable_shortcode == 1 && !$control){
            add_shortcode('free_shipping_notification', function(){ echo ""; });
        }

        if($free_shipping_notification == 1 /* && $pi_fsnw_default_shipping_zone != 0 */ && $control){

            if(empty($this->enable_shortcode)){
                add_action( 'wp_footer', array( $this, 'loadBarOnPage' ), 90 );
            }else{
                add_shortcode('free_shipping_notification', array( $this, 'shortCode' ) );
            }
            
            
        }

        add_action( 'wp_enqueue_scripts', array( $this,  'inlineCss') );
        add_action( 'wp_enqueue_scripts', array( $this,  'inlineJs') );
    }

    function initializeFunction(){
            
        $obj = new pisol_fsnw_min();
        $this->min_order = $obj->getMinOrder();
        $this->total = $obj->getCartTotal();
        $this->missing_amount = $obj->getMissingAmount();
    }

   
    
     /**
     * direct loading the message in the html on initial load
     * reduces load on server, we dont have to do ajax call untill user buy some thing or update cart
     */
    public function loadBarOnPage( ) {
        

		if ( ! is_admin() && $this->min_order !== false) {
            echo $this->messageTemplate("", true);
		}else{
            echo $this->messageTemplate("", true);
        }
    }

    function blankMessageTemplate(){
        $show_close_button = get_option('pi_fsnw_close_button',1);

        $message_template = "<div class='pisol-bar-container {$shortcode_class}' data-blank=\"true\">
                <div class='pisol-bar-message'></div>
                ".($show_close_button == 1 ? "<a href='javascript:void(0);' class='pisol-fsnw-close'>&times;</a>" : "")."
              </div>
        ";
        return $message_template;
    }

    function messageTemplate($message, $blank_container = false){

        $show_close_button = get_option('pi_fsnw_close_button',1);

        $pi_fsnw_circular_progress_enabled = get_option('pi_fsnw_circular_progress_enabled',1);

        $pi_fsnw_circular_progress_image = get_option('pi_fsnw_circular_progress_image', "");

        if($pi_fsnw_circular_progress_image != ""){
            $img_url = wp_get_attachment_url($pi_fsnw_circular_progress_image);
        }else{
            $img_url = plugin_dir_url( __FILE__ )."img/free-delivery.png";
        }

        if($blank_container == true){
            $data_attr = " data-blank='true' ";
        }else{
            $data_attr = "";
        }

        $shortcode_enabled = empty(get_option('pi_fsn_enable_shortcode',0)) ? '' : 'pisol-shortcode-bar-container';

        $message_template = "<div class='pisol-bar-container {$shortcode_enabled}' {$data_attr}>
                <div class='pisol-bar-message'>{$message}</div>
                ".($show_close_button == 1 ? "<a href='javascript:void(0);' class='pisol-fsnw-close'>&times;</a>" : "")."
              </div>
        ";

        if( $pi_fsnw_circular_progress_enabled == 1){
            $message_template .= "<div id='pi-progress-circle' style='background-image:url(".$img_url.");'></div>";
        }
        return $message_template;
    }
   

    public function shortCode(){
        ob_start();
        $this->loadBarOnPage();
        $bar = ob_get_contents();
        ob_end_clean();
        return $bar;
    }


    function inlineCss(){
        $pi_fsnw_position = esc_html(get_option("pi_fsnw_position",'top'));
        $pi_fsnw_circular_position = esc_html(get_option("pi_fsnw_circular_position",'bottom-right'));
        $pi_progress_circle = ' right:20px; bottom:20px; ';
        switch($pi_fsnw_circular_position){
            case 'bottom-right':
            $pi_progress_circle = ' right:20px; bottom:20px; ';
            break;
            case 'bottom-left':
            $pi_progress_circle = ' left:20px; bottom:20px; ';
            break;
            case 'top-left':
            $pi_progress_circle = ' left:20px; top:20px; ';
            break;
            case 'top-right':
            $pi_progress_circle = ' right:20px; top:20px; ';
            break;
        }
         

        $pi_fsnw_background_color = esc_html(get_option("pi_fsnw_background_color",'#ee6443'));
        $pi_fsnw_background_color_reached = esc_html(get_option("pi_fsnw_background_color_reached",'#ee6443'));
        $pi_fsnw_linear_progress_background_color = esc_html(get_option("pi_fsnw_linear_progress_background_color",'#cccccc'));
        $pi_fsnw_linear_progress_color = esc_html(get_option("pi_fsnw_linear_progress_color",'#ff0000'));
        $pi_fsnw_linear_progress_enabled = get_option("pi_fsnw_linear_progress_enabled",1) != "" ? 'block' : 'none';

        $pi_fsnw_font_color = esc_html(get_option("pi_fsnw_font_color",'#ffffff'));
        $pi_fsnw_shortcode_color = esc_html(get_option("pi_fsnw_shortcode_color",'#000000'));
        $pi_fsnw_link_color = esc_html(get_option("pi_fsnw_link_color",'#ffffff'));
        $pi_fsnw_close_color = esc_html(get_option("pi_fsnw_close_color",'#ffffff'));
        $pi_fsnw_cc_progress_bar_bg_color = esc_html(get_option("pi_fsnw_cc_progress_bar_bg_color",'#ff0000'));

        $pi_fsnw_font_weight = esc_html(get_option("pi_fsnw_font_weight",'normal'));
        $pi_fsnw_shortcode_weight = esc_html(get_option("pi_fsnw_shortcode_weight",'bold'));
        $pi_fsnw_link_weight = esc_html(get_option("pi_fsnw_link_weight",'normal'));
        $pi_fsnw_close_weight = esc_html(get_option("pi_fsnw_close_weight",'bold'));

        $pi_fsnw_font_size = esc_html(get_option("pi_fsnw_font_size",'16'));
        $pi_fsnw_shortcode_size = esc_html(get_option("pi_fsnw_shortcode_size",'16'));
        $pi_fsnw_link_size = esc_html(get_option("pi_fsnw_link_size",'16'));
        $pi_fsnw_close_size = esc_html(get_option("pi_fsnw_close_size",'22'));

        $pi_fsnw_disable_mobile = esc_html(get_option('pi_fsnw_disable_mobile',0));
        $pi_fsnw_mobile_breakpoint = (int)get_option('pi_fsnw_mobile_breakpoint',768);
        $pi_fsnw_progress_bar_thickness = $pi_fsnw_linear_progress_enabled == 'block' ? (int)get_option('pi_fsnw_progress_bar_thickness',6) : 0;
        
        $diameter = (int)get_option('pi_fsnw_circle_diameter', 70);
        $diameter = (int)(empty($diameter) ? 70 : $diameter);
        
        if($pi_fsnw_disable_mobile == 1){
            $disable_for_mobile = "
                @media(max-width:{$pi_fsnw_mobile_breakpoint}px){
                    .pisol-bar-container{
                        display:none !important;
                    }
                }
            ";
        }else{
            $disable_for_mobile = '';
        }

        
        
        $css = "
            #pi-progress-circle{
                width: {$diameter}px;
                height: {$diameter}px;
            }

           .pisol-bar-container{
                padding-bottom: calc(10px + {$pi_fsnw_progress_bar_thickness}px) !important;
           }

            .pisol-bar-container, .pisol-bar-container.ui-widget-content{
                {$pi_fsnw_position}: 0px !important;
                background-color:{$pi_fsnw_background_color} !important;
                color:{$pi_fsnw_font_color};
                font-weight:{$pi_fsnw_font_weight};
                font-size:{$pi_fsnw_font_size}px;
            }

            .pisol-bar-container.requirement-completed{
                background-color:$pi_fsnw_background_color_reached !important;
            }

            .pisol_icon img{
                width:40px;
                height:auto;
            }

            .pisol-bar-container a{
                color:{$pi_fsnw_link_color};
                font-weight:{$pi_fsnw_link_weight};
                font-size:{$pi_fsnw_link_size}px;
            }

            .pisol_shortcodes{
                color:{$pi_fsnw_shortcode_color};
                font-weight:{$pi_fsnw_shortcode_weight};
                font-size:{$pi_fsnw_shortcode_size}px;
            }

            .pisol-bar-container a.pisol-fsnw-close{
                color:{$pi_fsnw_close_color};
                font-weight:{$pi_fsnw_close_weight};
                font-size:{$pi_fsnw_close_size}px;
            }

            .ui-progressbar-value{
                display:{$pi_fsnw_linear_progress_enabled} !important;
                background:{$pi_fsnw_linear_progress_color} !important;
                z-index:2;
            }

            .pisol-bar-container:after{
                display:{$pi_fsnw_linear_progress_enabled} !important;
                background:{$pi_fsnw_linear_progress_background_color} !important;
                z-index:1;
            }

            #pi-progress-circle{
                {$pi_progress_circle}
            }

            .pisol-bar-container:after,.ui-progressbar-value{
                height: {$pi_fsnw_progress_bar_thickness}px !important;
            }

            .pi-fsnw-container-progress{
                background:{$pi_fsnw_cc_progress_bar_bg_color};
            }
        ";

        $shortcode_or_not_css = $this->cssBasedOnShortCodeOrFixed();

        $css = $css.$disable_for_mobile.$shortcode_or_not_css;
        wp_register_style( 'pi-fsnw-dummy', false );
        wp_enqueue_style( 'pi-fsnw-dummy' );
        /*wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css');*/
        wp_add_inline_style('pi-fsnw-dummy' , $css );
    }

    function cssBasedOnShortCodeOrFixed(){

        if($this->enable_shortcode == 1){
            $css = '
                .pisol-bar-container.ui-progressbar, .pisol-bar-container{
                    height:auto !important;
                }

                .pisol-bar-container{
                    position:relative;
                    width:100%;
                    display: none;
                    padding:10px 0;
                    padding-left:30px;
                    padding-right:30px;
                    text-align:center !important;
                    border-radius:0 !important;
                    border:0 !important;
                }

                .pisol-bar-message{
                    position:relative;
                    z-index:3;
                }

            ';
        }else{
            $css = '
            .pisol-bar-container.ui-progressbar, .pisol-bar-container{
                height:auto !important;
            }
            
            .pisol-bar-container{
                position:fixed;
                left:0px;
                width:100%;
                z-index:999999999999;
                text-align:center !important;
                padding:10px 0;
                display: none;
                padding-left:30px;
                padding-right:30px;
                border-radius:0 !important;
                border:0 !important;
            }

            ';

            
        }

        return $css;
    }

    static function getLanguageCode(){
        if(defined('ICL_LANGUAGE_CODE')){
            return ICL_LANGUAGE_CODE;
        }

        if(function_exists('pll_current_language')){
            return pll_current_language();
        }

        $lang_code = explode('_',get_locale());
        $lang_code_slug = $lang_code[0];
        return $lang_code_slug;
    }

    function inlineJs(){

        $bar_close_behaviour = get_option('pi_fsnw_bar_closing_option', 'normal');
        $lang_code_slug = self::getLanguageCode();
        $diameter = (int)get_option('pi_fsnw_circle_diameter', 70);
        $diameter = (int)(empty($diameter) ? 70 : $diameter);
        if($this->min_order !== false){
            if($this->min_order > 0){
                $percent = ($this->total / $this->min_order)*100;
            }else{
                $percent = 100;
            }
            $values = array(
                'ajax_url'=>add_query_arg('lang',$lang_code_slug, admin_url('admin-ajax.php')),
                'showContinues' =>(bool)(empty(get_option('pi_fsnw_persistent_bar',0)) ? false : true),
                'howLongToShow' =>(int)(((int)get_option('pi_fsnw_how_long_to_show',6))*1000),
                'percent'=> $percent,
                'bar_close_behaviour'=> $bar_close_behaviour,
                'diameter'=> $diameter
            );
        }else{
            $values = array(
                'ajax_url'=>add_query_arg('lang',$lang_code_slug, admin_url('admin-ajax.php')),
                'showContinues' =>(bool)(empty(get_option('pi_fsnw_persistent_bar',0)) ? false : true),
                'howLongToShow' =>(int)(((int)get_option('pi_fsnw_how_long_to_show',6))*1000),
                'percent'=> 0,
                'bar_close_behaviour'=> $bar_close_behaviour,
                'diameter'=> $diameter
            );
        }

        wp_enqueue_script( 'jquery-ui-progressbar');
        wp_enqueue_script( 'pisol-fsnw-circle', plugin_dir_url( __FILE__ ) . 'js/circle-progress.min.js', array( 'jquery' ), PISOL_FSNW_VERSION, false );
        wp_enqueue_script( 'pisol-fsnw', plugin_dir_url( __FILE__ ) . 'js/pisol-fsnw-public.js', array('pisol-fsnw-circle', 'jquery','jquery-ui-progressbar' ), PISOL_FSNW_VERSION, false );

        wp_localize_script('pisol-fsnw', 'pisol_fsnw', $values);
    }

   

}