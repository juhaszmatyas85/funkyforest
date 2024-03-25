<?php

class Pisol_fsnw_Popup{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'popup';

    private $tab_name = "Popup setting";

    private $setting_key = 'pi_fsnw_popup_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        

        add_action('woocommerce_init', array($this, 'initialize'));
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),5);

    }

    function initialize(){
        
        
        $this->settings = array(
                array('field'=>'pi_fsnw_enabled_popup', 'label'=>__('Enable Free shipping message in popup'),'type'=>'switch', 'default'=>0,   'desc'=>__('This will show the free shipping information in a popup that will come up when user adds product to cart or update cart')),
                array('field'=>'pi_fsnw_popup_background_color', 'label'=>__('Popup background color'),'type'=>'color', 'default'=>"#cccccc",   'desc'=>__('Background color of popup')),

                array('field'=>'pi_fsnw_popup_background_color_reached', 'label'=>__('Popup background color when user qualified for free shipping'),'type'=>'color', 'default'=>"#cccccc",   'desc'=>__('Background color of the popup changes to this when user is qualified for free shipping')),

                array('field'=>'pi_fsnw_popup_text_color', 'label'=>__('Popup text color'),'type'=>'color', 'default'=>"#ffffff",   'desc'=>__('Text color of popup')),
                array('field'=>'pi_fsnw_popup_foreground_color', 'label'=>__('Popup canvas background color'),'type'=>'color', 'default'=>"#ffffff",   'desc'=>__('background color of popup box surrounding area')),
                array('field'=>'pi_fsnw_popup_close_color', 'label'=>__('Popup close button color'),'type'=>'color', 'default'=>"#ffffff",   'desc'=>__('close button color')),

                array('field'=>'pi_fsnw_popup_width', 'label'=>__('Popup Width for desktop (in %)'),'type'=>'number', 'default'=>50, 'min'=>0, 'max'=>100, 'desc'=>__('Popup width for the desktop')),
                array('field'=>'pi_fsnw_popup_mobile_width', 'label'=>__('Popup Width for mobile (in %)'),'type'=>'number', 'default'=>100, 'min'=>0, 'max'=>100, 'desc'=>__('Popup width for the desktop')),

                array('field'=>'pi_fsnw_popup_closing_option', 'label'=>__('Popup closing button behaviour'),'type'=>'select', 'default'=>'normal', 'value'=>array('normal'=>'Close popup normally (it will reopen when cart content change/shipping zone change)', 'close_for_page'=>'If closed it will not reopen on that page until page refresh')),
            );

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

    

    
}

new Pisol_fsnw_Popup($this->plugin_name);