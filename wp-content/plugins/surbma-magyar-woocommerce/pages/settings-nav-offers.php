<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

// Offers Feed
$rss_ajanlatok = fetch_feed( 'https://www.hucommerce.hu/kategoria/ajanlatok/feed/' );
$maxitems_ajanlatok = false;

if ( !is_wp_error( $rss_ajanlatok ) ) {
	$maxitems_ajanlatok = $rss_ajanlatok->get_item_quantity( 3 );
	$rss_ajanlatok_items = $rss_ajanlatok->get_items( 0, $maxitems_ajanlatok );
}

if ( $maxitems_ajanlatok ) {
	?>
	<div class="uk-child-width-1-2@s uk-child-width-1-1@m uk-child-width-1-3@l uk-child-width-1-4@xl" uk-grid="masonry: true">
	<?php
	// Loop through each feed item and display each item as a hyperlink.
	foreach ( $rss_ajanlatok_items as $item_ajanlatok ) :
		?>
		<article>
			<div class="uk-card uk-card-default uk-card-small uk-card-hover">
				<div class="uk-card-media-top">
					<a href="<?php echo esc_url( $item_ajanlatok->get_permalink() ); ?>?utm_source=hucommerce-user&utm_medium=hucommerce-menu&utm_campaign=<?php echo urlencode( $item_ajanlatok->get_title() ); ?>&utm_content=hucommerce-offers" target="_blank">
						<img src="<?php echo esc_url( $item_ajanlatok->get_description() ); ?>" alt="<?php echo esc_html( $item_ajanlatok->get_title() ); ?>" style="display: block;width: 100%;height: auto;">
					</a>
				</div>
				<div class="uk-card-body">
					<h2 class="uk-h5 uk-text-bold"><?php echo wp_kses_post( $item_ajanlatok->get_title() ); ?></h2>
					<?php echo wp_kses_post( $item_ajanlatok->get_content() ); ?>
				</div>
				<div class="uk-card-footer">
					<a class="cps-more uk-button uk-button-text uk-button-small uk-padding-remove-horizontal uk-animation-toggle" href="<?php echo esc_url( $item_ajanlatok->get_permalink() ); ?>?utm_source=hucommerce-user&utm_medium=hucommerce-menu&utm_campaign=<?php echo urlencode( $item_ajanlatok->get_title() ); ?>&utm_content=hucommerce-offers" target="_blank"><?php esc_html_e( 'View offer', 'surbma-magyar-woocommerce' ); ?> <span class="uk-animation-slide-left-small" uk-icon="icon: arrow-right"></span></a>
				</div>
			</div>
		</article>
		<?php
	endforeach;
	?>
	</div>
	<p class="uk-text-center uk-margin-medium-top"><a class="uk-button uk-button-danger" href="https://www.hucommerce.hu/kategoria/ajanlatok/" target="_blank"><?php esc_html_e( 'Check all offers', 'surbma-magyar-woocommerce' ); ?></a></p>
	<?php
} else {
	?>
	<div class="uk-alert-danger uk-text-center" uk-alert>
		<p><?php esc_html_e( 'There is no current special offer yet.', 'surbma-magyar-woocommerce' ); ?></p>
	</div>
	<?php
}
