<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

function surbma_hc_offers_page() {
	echo surbma_hc_page_header();
	?>
	<div id="cps-settings">
		<div class="uk-grid-small" uk-grid>
			<div class="uk-width-medium uk-visible@m">
				<?php echo surbma_hc_page_sidebar(); ?>
			</div>
			<div class="uk-width-expand">
				<?php echo surbma_hc_page_notifications(); ?>
				<div class="cps-card uk-card uk-card-default uk--card-large uk-card-hover uk-margin-bottom">
					<div class="uk-card-header">
						<div class="uk-grid-small uk-flex-middle" uk-grid>
							<div class="uk-width-expand">
								<h3 class="uk-card-title uk-margin-remove-bottom"><?php esc_html_e( 'Offers', 'surbma-magyar-woocommerce' ); ?></h3>
								<p class="uk-text-meta uk-margin-remove-top">Ajánlatok csak a HuCommerce felhasználóknak válogatott partnerektől.</p>
							</div>
							<?php echo surbma_hc_page_mobile_nav(); ?>
						</div>
					</div>
					<div class="uk-card-body uk-background-muted">
						<?php include_once( SURBMA_HC_PLUGIN_DIR . '/pages/settings-nav-offers.php'); ?>
					</div>
					<?php echo surbma_hc_page_card_footer(); ?>
				</div>
				<?php cps_admin_footer( SURBMA_HC_PLUGIN_FILE ); ?>
			</div>
		</div>
	</div>
	<?php
	echo surbma_hc_page_footer();
}
