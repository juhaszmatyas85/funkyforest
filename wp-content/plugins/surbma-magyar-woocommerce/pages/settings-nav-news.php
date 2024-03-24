<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

// Latest News Feed
$rss_hirek = fetch_feed( 'https://www.hucommerce.hu/kategoria/hirek/feed/' );
$maxitems_hirek = false;

if ( !is_wp_error( $rss_hirek ) ) {
	$maxitems_hirek = $rss_hirek->get_item_quantity( 9 );
	$rss_hirek_items = $rss_hirek->get_items( 0, $maxitems_hirek );
}

if ( $maxitems_hirek ) {
	?>
	<div class="uk-child-width-1-2@s uk-child-width-1-1@m uk-child-width-1-3@l uk-child-width-1-4@xl" uk-grid="masonry: true">
	<?php
	// Loop through each feed item and display each item as a hyperlink.
	foreach ( $rss_hirek_items as $item_hirek ) :
		?>
		<article>
			<div class="cps-card uk-card uk-card-default uk-card-small uk-card-hover">
				<div class="uk-card-media-top">
					<a href="<?php echo esc_url( $item_hirek->get_permalink() ); ?>?utm_source=hucommerce-user&utm_medium=hucommerce-menu&utm_campaign=<?php echo urlencode( $item_hirek->get_title() ); ?>&utm_content=hucommerce-news" target="_blank"><img src="<?php echo esc_url( $item_hirek->get_description() ); ?>" alt="<?php echo esc_html( $item_hirek->get_title() ); ?>" style="display: block;width: 100%;height: auto;"></a>
				</div>
				<div class="uk-card-body">
					<h2 class="uk-h5 uk-text-bold"><?php echo wp_kses_post( $item_hirek->get_title() ); ?></h2>
					<?php echo wp_kses_post( $item_hirek->get_content() ); ?>
					<p><a href="<?php echo esc_url( $item_hirek->get_permalink() ); ?>?utm_source=hucommerce-user&utm_medium=hucommerce-menu&utm_campaign=<?php echo urlencode( $item_hirek->get_title() ); ?>&utm_content=hucommerce-news" class="cps-more uk-button uk-button-text uk-button-small uk-padding-remove-horizontal uk-animation-toggle" target="_blank"><?php esc_html_e( 'Read article', 'surbma-magyar-woocommerce' ); ?> <span class="uk-animation-slide-left-small" uk-icon="icon: arrow-right"></span></a></p>
				</div>
			</div>
		</article>
		<?php
	endforeach;
	?>
	</div>
	<p class="uk-text-center uk-margin-medium-top"><a class="uk-button uk-button-danger" href="https://www.hucommerce.hu/kategoria/hirek/" target="_blank"><?php esc_html_e( 'Check all News', 'surbma-magyar-woocommerce' ); ?></a></p>
	<?php
} else {
	?>
	<div class="uk-alert-danger uk-text-center" uk-alert>
		<p><?php esc_html_e( 'There is no blog content yet.', 'surbma-magyar-woocommerce' ); ?></p>
	</div>
	<?php
}
