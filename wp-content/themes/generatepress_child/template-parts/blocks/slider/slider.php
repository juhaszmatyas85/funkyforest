<?php

/**
 * Slider Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$id = 'slider-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

$className = 'slider';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
}
if ($is_preview) {
    $className .= ' is-admin';
}

?>
<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <div class="slides">
        <?php
        $slides = get_field('slides', 8450); // 1423 az "xy" bejegyzés azonosítója

        if ($slides) {
            foreach ($slides as $slide) {
                $image = $slide['image'];
                $image2 = $slide['slide_jobb_oldali_kep'];
                $text = $slide['slide_szoveg'];
                $link = $slide['slide_link'];

                if (!empty(trim($text))) {
                ?>
                    <div>
                        <div class="text-on-slide">
                            <?php echo $text; ?>
                            <?php if(!empty(trim($link))){
    ?>
    <div class="link-in-text">
        <a href="<?= $link ?>">Tovább</a>
    </div>
<?php } ?>
                        </div>
                        <?php echo wp_get_attachment_image($image['id'], 'full'); ?>
                        <div class="image-on-slide"><?php echo wp_get_attachment_image($image2['id'], 'full'); ?></div>
                    </div>
                    <?php
                } elseif (isset($image)) {
                    ?>
                    <div>
                        <?php echo wp_get_attachment_image($image['id'], 'full'); ?>
                        <div class="image-on-slide"><?php echo wp_get_attachment_image($image2['id'], 'full'); ?></div>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
</div>