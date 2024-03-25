<?php

class Pisol_fsnw_Design{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'design';

    private $tab_name = "Design";

    private $setting_key = 'pi_fsnw_design_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        

        add_action('woocommerce_init', array($this, 'initialize'));
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),4);

       
        

        if(PISOL_FSNW_DELETE_SETTING){
            $this->delete_settings();
        }
    }

    function initialize(){
        
        
     $this->settings = array(
        array('field'=>'title1', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Positions of the notification bar", 'type'=>"setting_category"),
        array('field'=>'pi_fsnw_position', 'label'=>__('Bar position'),'type'=>'select', 'default'=> 'top', 'value'=>array('top'=>__('Top'), 'bottom'=>__('Bottom')),  'desc'=>''),
        array('field'=>'pi_fsnw_circular_position', 'label'=>__('Position of circular progress wheel'),'type'=>'select', 'default'=> 'bottom-right', 'value'=>array('bottom-right'=>__('Bottom Right'), 'bottom-left'=>__('Bottom Left'), 'top-left'=>__('Top Left'), 'top-right'=>__('Top Right')),  'desc'=>''),
       
        

        array('field'=>'title2', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Background color of the notification bar", 'type'=>"setting_category"),
        array('field'=>'pi_fsnw_background_color', 'label'=>__('Background color'),'type'=>'color', 'default'=>"#ee6443",   'desc'=>__('Background color of the popup')),
        array('field'=>'pi_fsnw_background_color_reached', 'label'=>__('Background color when user qualified for free shipping'),'type'=>'color', 'default'=>"#ee6443",   'desc'=>__('Background color of the bar changes to this when user is qualified for free shipping')),

        array('field'=>'title3', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Text color", 'type'=>"setting_category"),
        array('field'=>'pi_fsnw_font_color', 'label'=>__('Font color'),'type'=>'color', 'default'=>"#ffffff",   'desc'=>__('This font color will be used as general color of text inside message bar')),
        array('field'=>'pi_fsnw_shortcode_color', 'label'=>__('Shortcode font color'),'type'=>'color', 'default'=>"#000000",   'desc'=>__('This font color will be used as for text given by shortcodes like {minimum_order} {cart_total} etc.')),
        array('field'=>'pi_fsnw_link_color', 'label'=>__('Link color'),'type'=>'color', 'default'=>"#ffffff",   'desc'=>__('This font color will be used for any sort of link that you add to message')),
        array('field'=>'pi_fsnw_close_color', 'label'=>__('Close button color'),'type'=>'color', 'default'=>"#ffffff",   'desc'=>__('This font color will be used for close button')),

        array('field'=>'title4', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Font Weight", 'type'=>"setting_category"),
        array('field'=>'pi_fsnw_font_weight', 'label'=>__('Normal text font weight'),'type'=>'select','value'=>array('normal'=>'Normal','bold'=>'Bold','lighter'=>'Lighter'), 'default'=>'normal',   'desc'=>__('This is the font weight used for text in the popup')),
        array('field'=>'pi_fsnw_shortcode_weight', 'label'=>__('Shortcode text font weight'),'type'=>'select','value'=>array('normal'=>'Normal','bold'=>'Bold','lighter'=>'Lighter'), 'default'=>'bold',    'desc'=>__('This is the font weight used for the text that comes from shortcode like {cart_total} and other')),
        array('field'=>'pi_fsnw_link_weight', 'label'=>__('Link text font weight'),'type'=>'select','value'=>array('normal'=>'Normal','bold'=>'Bold','lighter'=>'Lighter'), 'default'=>'normal',    'desc'=>""),
        array('field'=>'pi_fsnw_close_weight', 'label'=>__('Close button font weight'),'type'=>'select','value'=>array('normal'=>'Normal','bold'=>'Bold','lighter'=>'Lighter'), 'default'=>'bold',    'desc'=>""),

        array('field'=>'title5', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Font Size", 'type'=>"setting_category"),
        array('field'=>'pi_fsnw_font_size', 'label'=>__('Normal text font size'),'type'=>'number','min'=>0, 'step'=>1, 'default'=>"16",   'desc'=>__('Font size in PX (pixels)')),
        array('field'=>'pi_fsnw_shortcode_size', 'label'=>__('Shortcode text font size'),'type'=>'number','min'=>0, 'step'=>1, 'default'=>"16",   'desc'=>__('Font size in PX (pixels)')),
        array('field'=>'pi_fsnw_link_size', 'label'=>__('Link text font size'),'type'=>'number','min'=>0, 'step'=>1, 'default'=>"16",   'desc'=>__('Font size in PX (pixels)')),
        array('field'=>'pi_fsnw_close_size', 'label'=>__('Close button size'),'type'=>'number','min'=>0, 'step'=>1, 'default'=>"22",   'desc'=>__('Close button is also a font, Font size in PX (pixels)')),
            
    );
        

        $this->register_settings();
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
        $values = array();
        foreach($shipping_zones as $shipping_zone){
            $shipping_zone_id = $shipping_zone['zone_id'];

            if($this->checkFreeShippingAvailable($shipping_zone_id)){
                $shipping_zone_obj = WC_Shipping_Zones::get_zone($shipping_zone_id);
                $values[$shipping_zone_id] = $shipping_zone_obj->get_zone_name();
            }
        }
        if(count($values) == 0){
            return false;
        }
        return $values;
    }

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

new Pisol_fsnw_Design($this->plugin_name);