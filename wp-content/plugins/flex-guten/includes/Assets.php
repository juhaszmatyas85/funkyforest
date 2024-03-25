<?php

namespace Dwp;

// Assets Handle Class 

class Assets  
{
    function __construct(){
        add_action('wp_enqueue_scripts',[$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts',[$this, 'enqueue_assets']);
    }

    public function get_scripts(){
        return [
                'flexguten-plugin-script' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/js/plugin.js',
                    'version' => FLEXGUTEN_VERSION ,
                    'deps' => ['jquery','flexguten-rater-script']
                ],
                'flexguten-rater-script' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/js/rater.min.js',
                    'version' => FLEXGUTEN_VERSION ,
                    'deps' => ['jquery']
                ],
                'flexguten-pinit-script' => [
                    'src' => '//assets.pinterest.com/js/pinit.js',
                    'version' => FLEXGUTEN_VERSION ,
                    'deps' => ['jquery']
                ],
            ];
            
        
    }

    public function get_styles(){
        return [
                'flexguten-plugin-style' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/css/main.css',
                    'version' => FLEXGUTEN_VERSION,
                ],
                'flexguten-merriweather-font' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/fonts/merriweather.css',
                    'version' => FLEXGUTEN_VERSION,
                ],
                'flexguten-proximanova-font' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/fonts/proxima-nova-2.css',
                    'version' => FLEXGUTEN_VERSION,
                ],
                'flexguten-sharpsans-font' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/fonts/sharp-sans.css',
                    'version' => FLEXGUTEN_VERSION,
                ],
                'flexguten-nunito-font' => [
                    'src' => FLEXGUTEN_INC_URL . '/assets/fonts/nunito.css',
                    'version' => FLEXGUTEN_VERSION,
                ],
            ];
    }

    public function enqueue_assets(){

        $scripts = $this->get_scripts();

        foreach($scripts as $handle => $script){
            $deps = isset( $script['deps']) ? $script['deps'] : false;
            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

        $styles = $this->get_styles();

        foreach($styles as $handle => $style){
            $deps = isset( $style['deps']) ? $style['deps'] : false;
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

    }

    public function flexguten_inline_style($handle, $css){

        wp_register_style($handle, false);
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);

    }
}
