<?php

class Pisol_fsnw_Message{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'message';

    private $tab_name = "Message";

    private $setting_key = 'pi_fsnw_message_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        

        add_action('woocommerce_init', array($this, 'initialize'));
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

       
        

        
    }

    function initialize(){
        $cart_page_id = wc_get_page_id( 'checkout' );
        $cart_page_url = $cart_page_id ? get_permalink( $cart_page_id ) : '';
        
        $this->settings = array(
            array('field'=>'title1', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Message used when free shipping is for minimum amount OR Coupon", 'type'=>"setting_category"),

            array('field'=>'pi_fsnw_message_exact_0', 'label'=>__('When the user has not done any purchase till now'),'type'=>'textarea', 'default'=>'Free shipping for order above {minimum_order}',   'desc'=>__('User cart is empty<br> WPML Variable: pi_fsnw_message_exact_0')),

            array('field'=>'pi_fsnw_message_0', 'label'=>__('When the user has not reached the target amount needed for free shipping (0% to 50%) completion'),'type'=>'textarea', 'default'=>'You have purchased {cart_total} of {minimum_order}, Buy {missing_amount} worth products more to get the free shipping',   'desc'=>__('Use short codes like {cart_total}, {minimum_order}, {missing_amount} <br> WPML Variable: pi_fsnw_message_0')),

            array('field'=>'pi_fsnw_message_50', 'label'=>__('When the user has not reached 50% the target amount needed for free shipping (50% to 100%) completion'),'type'=>'textarea', 'default'=>'You are almost there, Buy {missing_amount} worth products more to get the free shipping',   'desc'=>__('Use short codes like {cart_total}, {minimum_order}, {missing_amount}<br> WPML Variable: pi_fsnw_message_50')),
            
            array('field'=>'pi_fsnw_message_100', 'label'=>__('When the user has purchased the required amount'),'type'=>'textarea', 'default'=>'You are now qualified for the Free shipping, go to <a href="'.$cart_page_url.'">Checkout</a> ',   'desc'=>__('Use short codes like {cart_total}, {minimum_order}, {missing_amount}<br> WPML Variable: pi_fsnw_message_100')),

            array('field'=>'title1', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Message for normal Free shipping", 'type'=>"setting_category"),

            array('field'=>'pi_fsnw_normal_free_shipping_message', 'label'=>__('Message for Free shipping for all without purchase restriction'),'type'=>'textarea', 'default'=>'Free Shipping',   'desc'=>__('This message will be shown when you are offering Free shipping without any minimum requirement <br> WPML Variable: pi_fsnw_normal_free_shipping_message')),

            array('field'=>'title1', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h6', 'label'=>"Message used when free shipping is for minimum amount And Coupon", 'type'=>"setting_category"),

            array('field'=>'pi_fsn_dont_show_till_coupon_added', 'label'=>__('Don\'t show notification bar till coupon is added'),'type'=>'switch', 'default'=>1,   'desc'=>__('Free shipping notification will not be shown till customer has added a free shipping coupon and free shipping is available on min amount AND Free shipping coupon')),

            array('field'=>'pi_fsnw_message_exact_0_and', 'label'=>__('When the user has not done any purchase till now'),'type'=>'textarea', 'default'=>'Free shipping for order above {minimum_order} with a coupon code',   'desc'=>__('User cart is empty<br> WPML Variable: pi_fsnw_message_exact_0_and')),

            array('field'=>'pi_fsnw_message_0_and', 'label'=>__('When the user has not reached the target amount needed for free shipping (0% to 50%) completion'),'type'=>'textarea', 'default'=>'You have purchased {cart_total} of {minimum_order}, Buy {missing_amount} worth products more and add the coupon code to get the free shipping',   'desc'=>__('Use short codes like {cart_total}, {minimum_order}, {missing_amount}<br> WPML Variable: pi_fsnw_message_0_and')),

            array('field'=>'pi_fsnw_message_50_and', 'label'=>__('When the user has not reached 50% the target amount needed for free shipping (50% to 100%) completion'),'type'=>'textarea', 'default'=>'You are almost there, Buy {missing_amount} worth products more and add the coupon code to get the free shipping',   'desc'=>__('Use short codes like {cart_total}, {minimum_order}, {missing_amount}<br> WPML Variable: pi_fsnw_message_50_and')),

            array('field'=>'pi_fsnw_message_100_and', 'label'=>__('When the user has purchased the required amount'),'type'=>'textarea', 'default'=>'You are now qualified for the Free shipping, go to <a href="'.$cart_page_url.'">Checkout</a> and add the free shipping coupon code',   'desc'=>__('Use short codes like {cart_total}, {minimum_order}, {missing_amount}<br> WPML Variable: pi_fsnw_message_100_and')),
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

new Pisol_fsnw_Message($this->plugin_name);