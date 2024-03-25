<?php

class Class_Pisol_fsnw_Translate{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'translate';

    private $tab_name = "Translate";

    private $setting_key = 'pi_fsnw_translate_setting';

    
    private $date_format = array();
    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;
        
        
        $this->settings = array(
            array('field'=>'pi_fsnw_translate_message')
        );
        
        $this->active_tab = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

       
        $this->register_settings();
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
       $saved_translations = get_option('pi_fsnw_translate_message',array());
       ?>
        <script>

            var pi_edd_saved_translations = <?php echo json_encode(array_values((is_array($saved_translations) ? $saved_translations : array())  )); ?>
        </script>
        <script id="pi_translate" type="text/x-jsrender">
            <div class="row pt-4 border-bottom align-items-center ">    
			<div class="col-12 col-md-8">
            <?php
                $languages = $this->getLanguages();
                echo '<select name="pi_fsnw_translate_message[{{:count}}][language]" class="form-control">';
                    foreach($languages as $language){
                        echo '<option value="'.$language['value'].'" lang="'.$language['lang'].'" {{if language == "'.$language['value'].'"}}selected="selected"{{/if}}>'.$language['name'].' - '.$language['value'].'</option>';
                    }
                echo '</select>';
            ?>
            </div>
            <div class="col-12 col-md-4 text-right">
            <button class="btn btn-warning btn-remove">Remove Translation</button>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not reached done any purchase till now </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_exact_0]" class="form-control" value="{{:pi_fsnw_message_exact_0}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not reached the target amount needed for free shipping (0% to 50%) completion</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_0]" class="form-control" value="{{:pi_fsnw_message_0}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not reached 50% the target amount needed for free shipping (50% to 100%) completion</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_50]" class="form-control" value="{{:pi_fsnw_message_50}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not purchased the required amount</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_100]" class="form-control" value="{{:pi_fsnw_message_100}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When Free shipping is available without minimum restriction</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_normal_free_shipping_message]" class="form-control" value="{{:pi_fsnw_normal_free_shipping_message}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                    <strong>Message used when free shipping is for minimum amount And Coupon</strong>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not reached done any purchase till now </strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_exact_0_and]" class="form-control" value="{{:pi_fsnw_message_exact_0_and}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not reached the target amount needed for free shipping (0% to 50%) completion</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_0_and]" class="form-control" value="{{:pi_fsnw_message_0_and}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not reached 50% the target amount needed for free shipping (50% to 100%) completion</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_50_and]" class="form-control" value="{{:pi_fsnw_message_50_and}}">
                    </div>
                </div>
            </div>
            <div class="col-12 border-bottom py-2">
                <div class="row align-items-center mb-2">
                    <div class="col-12 col-md-5">
                    <strong>When user has not purchased the required amount</strong>
                    </div>
                    <div class="col-12 col-md-7">
                    <input required name="pi_fsnw_translate_message[{{:count}}][pi_fsnw_message_100_and]" class="form-control" value="{{:pi_fsnw_message_100_and}}">
                    </div>
                </div>
            </div>
            </div>
        </script>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <div class="row py-4 border-bottom align-items-center bg-primary text-light">
            <div class="col-12">
            <h2 class="mt-0 mb-0 text-light font-weight-light h4">Add translation for the estimate message</h2>
            </div>
        </div>
        <div id="pi_edd_translation_container">

        </div>
        <button type="button" class="btn btn-primary my-2" id="btn-edd-add-translation">Add Translation</button><br>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    function getLanguages(){
        $languages = array();
        $args = array('echo' => 0); 
        $html = wp_dropdown_languages( $args );
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();
        $options = $dom->getElementsByTagName('option');
        foreach ($options as $option){
            $value = $option->getAttribute('value');
            $lang = $option->getAttribute('lang');
            $name = $option->nodeValue;
            $languages[] = array( 'value'=>$value, 'name'=> $name, 'lang'=>$lang);
        }
        return $languages;
    }
    
}

//new Class_Pisol_fsnw_Translate($this->plugin_name);