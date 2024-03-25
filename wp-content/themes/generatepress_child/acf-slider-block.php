<?php
function acf_slider_block_init() {
    if (function_exists('acf_register_block_type')) {
        acf_register_block_type(array(
            'name'              => 'acf/slider-block',
            'title'             => __('ACF Slider Block'),
            'description'       => __('A custom slider block using ACF.'),
            'render_callback'   => 'acf_slider_block_render_callback',
            'category'          => 'common',
            'icon'              => 'images-alt2',
            'keywords'          => array('slider', 'acf'),
        ));
    }
}
add_action('acf/init', 'acf_slider_block_init');

function acf_slider_block_render_callback($block, $content = '', $is_preview = false) {
    $images = get_field('slides'); // Assuming you named your ACF field group 'slides'
    if ($images) {
        echo '<div class="slider">';
        foreach ($images as $image) {
            echo '<img src="' . $image['url'] . '" alt="' . $image['alt'] . '"/>';
        }
        echo '</div>';
    }
}