<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

// Directory Feed
$rss_katalogus = fetch_feed( 'https://www.hucommerce.hu/katalogus/feed/' );
$rss_katalogus_woocommerce_kiegeszitok = fetch_feed( 'https://www.hucommerce.hu/katalogus-kategoria/woocommerce-kiegeszitok/feed/' );

$maxitems_katalogus = false;
$maxitems_katalogus_woocommerce_kiegeszitok = false;

if ( !is_wp_error( $rss_katalogus ) ) {
	$maxitems_katalogus = $rss_katalogus->get_item_quantity( 99999 );
	$rss_katalogus_items = $rss_katalogus->get_items( 0, $maxitems_katalogus );
}

if ( $maxitems_katalogus ) {
	?>
	<div uk-filter="target: .directory-filter">

		<ul class="uk-subnav uk-subnav-pill uk-text-small uk-hidden">
			<li class="uk-active" uk-filter-control><a href="#"><?php esc_html_e( 'All post', 'surbma-magyar-woocommerce' ); ?></a></li>
			<li uk-filter-control="[data-tags*='woocommerce-extensions']"><a href="#"><span uk-icon="icon: bag;ratio: .7"></span> <?php esc_html_e( 'WooCommerce Extensions', 'surbma-magyar-woocommerce' ); ?></a></li>
			<li uk-filter-control="[data-tags*='woocommerce-fejlesztok']"><a href="#"><span uk-icon="icon: cart;ratio: .7"></span> <?php esc_html_e( 'WooCommerce Developers', 'surbma-magyar-woocommerce' ); ?></a></li>
			<li uk-filter-control="[data-tags*='woocommerce-tarhelyszolgaltatok']"><a href="#"><span uk-icon="icon: credit-card;ratio: .7"></span> <?php esc_html_e( 'WooCommerce Hosting', 'surbma-magyar-woocommerce' ); ?></a></li>
			<li uk-filter-control="[data-tags*='oktatok']"><a href="#"><span uk-icon="icon: quote-right;ratio: .7"></span> <?php esc_html_e( 'Courses', 'surbma-magyar-woocommerce' ); ?></a></li>
			<li uk-filter-control="[data-tags*='ugyvedek']"><a href="#"><span uk-icon="icon: thumbnails;ratio: .7"></span> <?php esc_html_e( 'Lawyers', 'surbma-magyar-woocommerce' ); ?></a></li>
		</ul>

		<ul class="directory-filter uk-child-width-1-2@s uk-child-width-1-2@m uk-child-width-1-3@l uk-child-width-1-5@xl" uk-grid="masonry: true">
		<?php
		// Loop through each feed item and display each item as a hyperlink.
		foreach ( $rss_katalogus_items as $item_katalogus ) :
			?>
			<li data-tags="<?php echo basename( esc_url( $item_katalogus->get_permalink() ) ); ?>">
				<div class="cps-card uk-card uk-card-default uk-card-small uk-card-hover">
					<div class="uk-card-media-top">
						<a href="<?php echo esc_url( $item_katalogus->get_permalink() ); ?>?utm_source=hucommerce-user&utm_medium=hucommerce-menu&utm_campaign=<?php echo urlencode( $item_katalogus->get_title() ); ?>&utm_content=hucommerce-directory" target="_blank">
							<img src="<?php echo esc_url( $item_katalogus->get_description() ); ?>" alt="<?php echo esc_html( $item_katalogus->get_title() ); ?>" style="display: block;width: 100%;height: auto;">
						</a>
					</div>
					<div class="uk-card-body uk-text-center">
						<h2 class="uk-h5 uk-text-bold"><?php echo wp_kses_post( $item_katalogus->get_title() ); ?></h2>
						<?php /*echo wp_kses_post( $item_katalogus->get_content() );*/ ?>
						<p><a href="<?php echo esc_url( $item_katalogus->get_permalink() ); ?>?utm_source=hucommerce-user&utm_medium=hucommerce-menu&utm_campaign=<?php echo urlencode( $item_katalogus->get_title() ); ?>&utm_content=hucommerce-directory" class="cps-more uk-button uk-button-text uk-button-small uk-padding-remove-horizontal uk-animation-toggle" target="_blank"><?php esc_html_e( 'Read more', 'surbma-magyar-woocommerce' ); ?> <span class="uk-animation-slide-left-small" uk-icon="icon: arrow-right"></span></a></p>
					</div>
				</div>
			</li>
			<?php
		endforeach;
		?>
		</ul>
	</div>
	<p class="uk-text-center uk-margin-medium-top"><a class="cps-button uk-button uk-button-primary uk-button-large" href="https://www.hucommerce.hu/katalogus/" target="_blank"><?php esc_html_e( 'Check all posts in HuCommerce Directory', 'surbma-magyar-woocommerce' ); ?></a></p>
	<?php
} else {
	?>
	<div class="uk-alert-danger uk-text-center" uk-alert>
		<p><?php esc_html_e( 'There is no post in the HuCommerce Directory yet.', 'surbma-magyar-woocommerce' ); ?></p>
	</div>
	<?php
}
