<?php

class Pisol_fsnw_Option{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'default';

    private $tab_name = "Basic setting";

    private $setting_key = 'pi_fsnw_basic_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        

        add_action('woocommerce_init', array($this, 'initialize'));
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),1);

    }

    function initialize(){
        
        if($this->getShippingZones() == false){
            $this->settings = array(  array('field'=>'title', 'class'=> 'bg-light text-dark', 'class_title'=>'text-danger text-center font-weight-light h5', 'label'=>"You need to have a free shipping class, in at least one of the shipping zones, Then only you can use this plugin, So please set a Free shipping class first ", 'type'=>"setting_category")
        );
        }else{
            $this->settings = array(
                array('field'=>'pi_fsnw_enabled', 'label'=>__('Enable Free shipping notification'),'type'=>'switch', 'default'=>1,   'desc'=>__('Enable free shipping notification or disable it')),
                array('field'=>'pi_fsnw_default_shipping_zone', 'label'=>__('Select a default shipping zone'),'type'=>'select',  'desc'=>__('This will only show the shipping zone that has free shipping class enabled with "minimum order amount" OR "minimum order amount or a coupon" condition set'), 'value'=>$this->getShippingZones()),

                array('field'=>'pi_fsn_enable_shortcode', 'label'=>__('Add notification bar using shortcode [free_shipping_notification]'),'type'=>'switch', 'default'=>0,   'desc'=>__('Add notification bar using short code <strong>[free_shipping_notification]</strong><br>Bar Position option in the design Tab will not work when short code is used')),

                array('field'=>'pi_fsnw_dont_show_notification_till_zone_selected', 'label'=>__('Don\'t show notification till Zone is selected'),'type'=>'switch', 'default'=>0,   'desc'=>__('Auto detection using IP is done, if it cant find the zone then it will not show the default zone, or it will show the default zone')),
                array('field'=>'pi_fsnw_disable_mobile', 'label'=>__('Disable for mobile'),'type'=>'switch', 'default'=>0,   'desc'=>__('Disable notification bar for mobile')),
                array('field'=>'pi_fsnw_mobile_breakpoint', 'label'=>__('Mobile breakpoint in PX (pixels)'),'type'=>'number', 'default'=>768, 'min'=>0,  'desc'=>__('Breakpoint with for the mobile devices in pixels')),
                array('field'=>'pi_fsnw_close_button', 'label'=>__('Show close option'),'type'=>'switch', 'default'=>1,   'desc'=>__('It will show the close button on the bar')),

                array('field'=>'pi_fsnw_bar_closing_option', 'label'=>__('Bar closing button behaviour'),'type'=>'select', 'default'=>'normal', 'value'=>array('normal'=>'Close popup normally', 'close_completely'=>'If closed it will not reopen for that user on entire site')),

                array('field'=>'pi_fsnw_persistent_bar', 'label'=>__('Show the Free shipping bar continues'),'type'=>'switch', 'default'=>0,   'desc'=>__('Free shipping notification bar will be shown continues will not get hidden after some time')),
                array('field'=>'pi_fsnw_how_long_to_show', 'label'=>__('How long to show (unit in seconds)'),'type'=>'number', 'default'=>6,   'desc'=>__('Once page ha loaded, then after this many seconds notification will be shown, This is only applicable when Bar is not set to show continues'), 'min'=>1, 'step'=>1),
                array('field'=>'pi_fsnw_shipping_icon_img', 'label'=>__('Shipping icon'),'type'=>'image', 'desc'=>__('You can add this image in the top bar message using short code {icon}')),

                array('field'=>'title2', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Hide other shipping method when Free shipping is available", 'type'=>"setting_category"),
                array('field'=>'pi_fsnw_hide_other_shipping', 'label'=>__('When FREE shipping method available'),'type'=>'select',  'desc'=>__('Hide other shipping option when free shipping is available, this will avoid confusion amount your customer'), 'value'=>array('dont_hide'=>'Don\'t Hide Other methods','hide_all'=>"Hide all the shipping method which are not FREE", 'hide_all_exclude_local_pickup'=>"Show only Local pickup and other FREE shipping method"), 'default'=>'dont_hide'),

                array('field'=>'title2', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Circular progress", 'type'=>"setting_category"),
                array('field'=>'pi_fsnw_circular_progress_enabled', 'label'=>__('Show circular progress bar at footer'),'type'=>'switch', 'default'=>1,   'desc'=>__('Show the circular progress bar at the footer when top bar is closed')),
                array('field'=>'pi_fsnw_circular_progress_image', 'label'=>__('Change image inside circular progress image'),'type'=>'image',    'desc'=>__('Image that is shown inside the circular progress bar')),

                array('field'=>'pi_fsnw_circle_diameter', 'label'=>__('Set the Diameter'),'type'=>'number', 'min' => 60, 'default'=> 70, 'step'=>1,  'desc'=>__('Set the diameter of the circular progress shown at the bottom')),

                array('field'=>'title2', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Lineal progress line shown below the bar", 'type'=>"setting_category"),
                array('field'=>'pi_fsnw_linear_progress_enabled', 'label'=>__('Show linear progress inside the notification bar'),'type'=>'switch', 'default'=>1,   'desc'=>__('Show the linear progress inside the notification bar')),
                array('field'=>'pi_fsnw_linear_progress_color', 'label'=>__('Progress line color'),'type'=>'color', 'default'=>"#FF0000",   'desc'=>__('')),
                array('field'=>'pi_fsnw_linear_progress_background_color', 'label'=>__('Progress line background color'),'type'=>'color', 'default'=>"#cccccc",   'desc'=>__('Background color of the linear progress line')),
                array('field'=>'pi_fsnw_progress_bar_thickness', 'label'=>__('Select progress bar thickness'),'type'=>'select',  'desc'=>__(''), 'value'=>array('1'=>'1 px','2'=>'2 px','3'=>'3 px', '4'=>'4 px', '5'=> '5 px', '6' => '6 px', '7' => '7 px', '8' => '8 px', '9' => '9 px', '10'=> '10px'), 'default'=>'6'),
            );
        }

        $this->register_settings();

        if(PISOL_FSNW_DELETE_SETTING){
            $this->delete_settings();
        }
    }

    
    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }

    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
            <?php _e( $this->tab_name); ?> 
        </a>
        <?php
    }

    function tab_content(){
        
       ?>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_fsnw($setting, $this->setting_key);
            }
        ?>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    function getShippingZones(){
        $shipping_zones = WC_Shipping_Zones::get_zones( );
        $values = array(0 => "Select Free shipping zone");
        foreach($shipping_zones as $shipping_zone){
            $shipping_zone_id = $shipping_zone['zone_id'];

            if($this->checkFreeShippingAvailable($shipping_zone_id)){
                $shipping_zone_obj = WC_Shipping_Zones::get_zone($shipping_zone_id);
                $values[$shipping_zone_id] = $shipping_zone_obj->get_zone_name();
            }
        }
        if(count($values) == 1){
            return false;
        }
        
        return $values;
    }
    /*
    function checkFreeShippingAvailable($shipping_zone_id){
        $zone_obj = new WC_Shipping_Zone($shipping_zone_id);
        $methods = $zone_obj->get_shipping_methods(true);
        foreach($methods as $method){
            
            if($method->id == 'free_shipping'){
                
                $require = isset($method->instance_settings['requires']) ? $method->instance_settings['requires'] : "";
                $min_amount = isset($method->instance_settings['min_amount']) ? $method->instance_settings['min_amount'] : "";

                if(($require == 'min_amount' || $require == 'either' ) && $min_amount != ""){
                    return true;
                }

                if($require == ''){
                    return true;
                }

            }
        }
        return false;
    }
    */
    
    function checkFreeShippingAvailable($shipping_zone_id){
        global $wpdb;
        $wfspb_query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = %s AND is_enabled = %d AND zone_id = %d ORDER BY method_order ASC", 'free_shipping', 1, $shipping_zone_id );
        $zone_data   = $wpdb->get_results( $wfspb_query, OBJECT );

        if ( empty( $zone_data ) ) {
            return false;
        } else {
            return true;
        }

    }
    

    
}

new Pisol_fsnw_Option($this->plugin_name);