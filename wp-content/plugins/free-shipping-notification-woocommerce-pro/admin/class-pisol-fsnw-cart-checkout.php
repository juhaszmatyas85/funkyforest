<?php

class Pisol_fsnw_Option_Cart_Checkout{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'cart-checkout';

    private $tab_name = "Cart & Checkout";

    private $setting_key = 'pi_fsnw_basic_setting_cart_checkout';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        

        add_action('woocommerce_init', array($this, 'initialize'));
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),2);

    }

    function initialize(){
        
        
            $this->settings = array(
                array('field'=>'pi_fsnw_inside_cart_total', 'label'=>__('Show free shipping notification in cart subtotal area'),'type'=>'switch', 'default'=>0,   'desc'=>__('Show free shipping notification in cart subtotal area')),

                array('field'=>'pi_fsnw_inside_cart_position', 'label'=>__('Position on the cart page'),'type'=>'select', 'default'=>'woocommerce_cart_totals_before_shipping', 'value'=>array('woocommerce_cart_totals_before_shipping'=>'Before shipping block', 'woocommerce_cart_totals_after_shipping'=>'After shipping block')),

                array('field'=>'pi_fsnw_inside_checkout_total', 'label'=>__('Show free shipping notification in checkout subtotal area'),'type'=>'switch', 'default'=>0,   'desc'=>__('Show free shipping notification in checkout subtotal area')),

                array('field'=>'pi_fsnw_inside_checkout_position', 'label'=>__('Position on the checkout page'),'type'=>'select', 'default'=>'woocommerce_review_order_before_shipping', 'value'=>array('woocommerce_review_order_before_shipping'=>'Before shipping block', 'woocommerce_review_order_after_shipping'=>'After shipping block')),

                array('field'=>'pi_fsnw_cc_progress_bar_text', 'label'=>__('Text shown inside the progress bar'),'type'=>'text', 'default'=>'Free shipping',   'desc'=>''),

                array('field'=>'pi_fsnw_cc_progress_bar_bg_color', 'label'=>__('Progress bar background color'),'type'=>'color', 'default'=>"#ff0000",   'desc'=>''),
               
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

new Pisol_fsnw_Option_Cart_Checkout($this->plugin_name);