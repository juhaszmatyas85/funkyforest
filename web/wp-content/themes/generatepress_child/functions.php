<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */
function load_stylesheets(){
    wp_register_style('flexslider', get_stylesheet_directory_uri() . '/css/flexslider.css',
        array(), false, 'all');
    wp_enqueue_style('flexslider');
}

function loadjs(){
    wp_register_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', '', 1, true);
    wp_enqueue_script('custom');
}

function crunchify_load_jquery_from_google_cdn() {
    if (!is_admin()) {

        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', false, '3.6.0');
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'crunchify_load_jquery_from_google_cdn');

add_action('acf/init', 'my_register_blocks');
function my_register_blocks() {

    if( function_exists('acf_register_block_type') ) {

        acf_register_block_type(array(
            'name'              => 'slider',
            'title'             => __('Slider'),
            'description'       => __('A custom slider block.'),
            'render_template'   => 'template-parts/blocks/slider/slider.php',
            'category'          => 'formatting',
            'icon'              => 'images-alt2',
            'align'             => 'full',
            'enqueue_assets'    => function(){
                wp_enqueue_style( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1' );
                wp_enqueue_style( 'slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array(), '1.8.1' );
                wp_enqueue_script( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true );

                wp_enqueue_style( 'block-slider', get_stylesheet_directory_uri() . '/template-parts/blocks/slider/slider.css', array(), '1.0.0' );
                wp_enqueue_script( 'block-slider', get_stylesheet_directory_uri() . '/template-parts/blocks/slider/slider.js', array(), '1.0.0', true );
            },
        ));
    }
}

function custom_slider_shortcode() {
    ob_start();
    include 'template-parts/blocks/slider/slider.php';
    return ob_get_clean();
}
add_shortcode('custom_slider', 'custom_slider_shortcode');

add_action('wp_enqueue_scripts', 'load_stylesheets');

add_action('wp_enqueue_scripts', 'loadjs');

function primary_menu_shortcode() {
    $menu = wp_nav_menu(array('menu' => 'primary-menu', 'echo' => false));
    return $menu;
}
add_shortcode('primary-menu', 'primary_menu_shortcode');

function footer_menu_shortcode() {
    $menu = wp_nav_menu(array('menu' => 'footer-menu', 'echo' => false));
    return $menu;
}
add_shortcode('footer-menu', 'footer_menu_shortcode');


function gutenberg_block_shortcode($atts) {
    $atts = shortcode_atts(array(
        'ref' => '', // Az azonosító attribútuma
    ), $atts, 'gutenberg_block');

    $block_content = '';
    if (!empty($atts['ref'])) {
        $block_id = intval($atts['ref']);
        $block = get_post($block_id);
        if ($block && has_blocks($block->post_content)) {
            $block_content = apply_filters('the_content', $block->post_content);
        }
    }

    return $block_content;
}
add_shortcode('gutenberg_block', 'gutenberg_block_shortcode');