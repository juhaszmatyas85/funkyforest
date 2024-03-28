<?php
$args = array(
    'post_type' => 'alapanyagok',
    'posts_per_page' => -1, // Display all posts
);

$query = new WP_Query($args);

if ($query->have_posts()) :
    while ($query->have_posts()) :
        $query->the_post();
        $link = get_permalink(); // Get the post URL
        $title = get_the_title(); // Get the post title
        $file_id = get_post_meta(get_the_ID(), 'image', true); // Get the file ID from ACF field
        $file_url = wp_get_attachment_url($file_id); // Get the file URL using attachment ID

        // Check if the file URL is valid
        if ($file_url) {
            echo '<div c lass="alapanyag"><span class="bg" style="background-image: url(\'' . esc_url($file_url) . '\');" >&nbsp;</span><span class="background-element"><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></span></div>';
        }
    endwhile;
    wp_reset_postdata();
else :
    echo 'Nincsenek alapanyagok.';
endif;
?>
