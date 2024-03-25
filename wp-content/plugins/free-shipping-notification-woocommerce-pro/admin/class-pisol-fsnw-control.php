<?php

class Class_Pi_Fsnw_Control{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'control';

    private $tab_name = "Control shipping bar";

    private $setting_key = 'pi_fsnw_control_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

        $this->settings = array(
           

            array('field'=>'pi_fsnw_show_all', 'label'=>__('Show Free shipping notification on all the pages of a website'),'type'=>'switch', 'default'=>1,   'desc'=>__('')),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>"Show Free shipping notification on the selected page", 'type'=>"setting_category"),
            array('field'=>'pi_fsnw_show_front_page', 'label'=>__('Show on front page of the site (is_front_page)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            array('field'=>'pi_fsnw_show_is_product', 'label'=>__('Show on single product page (is_product)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            array('field'=>'pi_fsnw_show_is_cart', 'label'=>__('Show on cart page (is_cart)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            array('field'=>'pi_fsnw_show_is_checkout', 'label'=>__('Show on checkout page (is_checkout)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            array('field'=>'pi_fsnw_show_is_shop', 'label'=>__('Show on shop page (is_shop)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            array('field'=>'pi_fsnw_show_is_product_category', 'label'=>__('Show on product category page (is_product_category)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            array('field'=>'pi_fsnw_show_is_product_tag', 'label'=>__('Show on product tag page (is_product_tag)'),'type'=>'switch', 'default'=>0,   'desc'=>__('')),
            
        );
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),6);

       
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
        <div id="pi_control">
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_fsnw($setting, $this->setting_key);
            }
        ?>
        </div>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    
}

new Class_Pi_Fsnw_Control($this->plugin_name);