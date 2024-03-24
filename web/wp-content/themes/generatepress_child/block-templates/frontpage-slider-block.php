<h1>helló</h1>
<?php if( have_rows('slides') ): ?>
    <ul class="slides">
    <?php while( have_rows('slides') ): the_row();
    echo 'helló2';
        $image = get_sub_field('image');
        $title = get_sub_field('slide_szoveg');
        echo $title;
    ?>
        <li>
            <?php echo wp_get_attachment_image( $image, 'full' ); ?>
            <p><?php echo get_sub_field('slide_szoveg'); ?></p>
        </li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>