<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

function surbma_hc_page_modules_nav() {
	$screen = get_current_screen();
	global $surbma_hc_modules_page;

	$active_modules_menu = $surbma_hc_modules_page == $screen->base ? 'uk-active' : '';

	ob_start();
		?>
		<li class="<?php echo $active_modules_menu; ?>"><a href="<?php echo admin_url( 'admin.php?page=surbma-hucommerce-menu' ); ?>"><span class="uk-margin-small-right" uk-icon="icon: thumbnails"></span> HuCommerce <?php esc_html_e( 'Modules', 'surbma-magyar-woocommerce' ); ?></a></li>
		<?php if ( $surbma_hc_modules_page == $screen->base ) { ?>
		<li class="cps-settings-subnav">
			<ul class="uk-nav-sub uk-padding-remove-left uk-padding-remove-bottom" uk-switcher="connect: #surbma-hc-modules; animation: uk-animation-fade">
				<li><a class="uk-offcanvas-close uk-modal-close-default"><span class="uk-margin-small-right" style="width: 100%;max-width: 20px;" uk-icon="icon: chevron-double-right; ratio: 1"></span> <?php esc_html_e( 'All modules', 'surbma-magyar-woocommerce' ); ?></a></li>
				<li class="uk-nav-header"><span class="uk-margin-small-right" style="width: 100%;max-width: 20px;" uk-icon="icon: settings; ratio: 1"></span> <?php esc_html_e( 'Module settings', 'surbma-magyar-woocommerce' ); ?>:</li>
				<?php cps_hc_wcgems_module_nav_item( 'Check field formats (Masking)', 'maskcheckoutfields' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Check field values', 'validatecheckoutfields' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Free shipping notification', 'freeshippingnotice' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Empty Cart button', 'module-emptycartbutton' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Product price history', 'module-productpricehistory' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Product price additions', 'module-productpriceadditions' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Legal compliance (GDPR, CCPA, ePrivacy)', 'legalcheckout' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Limit Payment Methods', 'module-limitpaymentmethods' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Global informations', 'module-globalinfo' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Fixes for Hungarian language', 'huformatfix' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Tax number field', 'taxnumber' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Hungarian translation fixes', 'translations' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Hide County field if Country is Hungary', 'nocounty' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Autofill City after Postcode is given', 'autofillcity' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Product customizations', 'module-productsettings' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Checkout page customizations', 'module-checkout' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Plus/minus quantity buttons', 'plusminus' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Automatic Cart update', 'updatecart' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Continue shopping buttons', 'returntoshop' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Login and registration redirection', 'loginregistrationredirect' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Coupon field customizations', 'module-coupon' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Redirect Cart page to Checkout page', 'module-redirectcart' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'One product per purchase', 'module-oneproductincart' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Custom Add To Cart Button', 'module-custom-addtocart-button' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Hide shipping methods', 'module-hideshippingmethods' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'SMTP service', 'module-smtp' ); ?>
				<?php cps_hc_wcgems_module_nav_item( 'Catalog mode', 'module-catalogmode' ); ?>
			</ul>
		</li>
		<?php } ?>
		<?php
	$nav_items = ob_get_contents();
	ob_end_clean();

	return $nav_items;
}

function surbma_hc_pages_nav() {
	$screen = get_current_screen();
	global $surbma_hc_offers_page;
	global $surbma_hc_directory_page;
	global $surbma_hc_news_page;
	global $surbma_hc_informations_page;

	$active_offers_menu = $surbma_hc_offers_page == $screen->base ? 'uk-active' : '';
	$active_directory_menu = $surbma_hc_directory_page == $screen->base ? 'uk-active' : '';
	$active_news_menu = $surbma_hc_news_page == $screen->base ? 'uk-active' : '';
	$active_informations_menu = $surbma_hc_informations_page == $screen->base ? 'uk-active' : '';

	ob_start();
		?>
		<li class="<?php echo $active_offers_menu; ?> uk-hidden"><a href="<?php echo admin_url( 'admin.php?page=surbma-hucommerce-offers-menu' ); ?>"><span class="uk-margin-small-right" uk-icon="icon: star"></span> <?php esc_html_e( 'Offers', 'surbma-magyar-woocommerce' ); ?></a></li>
		<li class="<?php echo $active_directory_menu; ?>"><a href="<?php echo admin_url( 'admin.php?page=surbma-hucommerce-directory-menu' ); ?>"><span class="uk-margin-small-right" uk-icon="icon: list"></span> HuCommerce <?php esc_html_e( 'Directory', 'surbma-magyar-woocommerce' ); ?></a></li>
		<li class="<?php echo $active_news_menu; ?> uk-hidden"><a href="<?php echo admin_url( 'admin.php?page=surbma-hucommerce-news-menu' ); ?>"><span class="uk-margin-small-right" uk-icon="icon: rss"></span> <?php esc_html_e( 'Latest News', 'surbma-magyar-woocommerce' ); ?></a></li>
		<?php
	$nav_items = ob_get_contents();
	ob_end_clean();

	return $nav_items;
}

function surbma_hc_page_license_nav() {
	$screen = get_current_screen();
	global $surbma_hc_license_page;
	global $surbma_hc_informations_page;

	$hc_pro_menu_icon = 'active' == SURBMA_HC_PLUGIN_LICENSE ? 'unlock' : 'lock';

	$active_license_menu = $surbma_hc_license_page == $screen->base ? 'uk-active' : '';
	$active_informations_menu = $surbma_hc_informations_page == $screen->base ? 'uk-active' : '';

	ob_start();
		?>
		<li class="<?php echo $active_license_menu; ?>"><a href="<?php echo admin_url( 'admin.php?page=surbma-hucommerce-license-menu' ); ?>"><span class="uk-margin-small-right" uk-icon="icon: <?php echo $hc_pro_menu_icon; ?>"></span> <?php esc_html_e( 'License management', 'surbma-magyar-woocommerce' ); ?></a></li>
		<li class="<?php echo $active_informations_menu; ?>"><a href="<?php echo admin_url( 'admin.php?page=surbma-hucommerce-informations-menu' ); ?>"><span class="uk-margin-small-right" uk-icon="icon: info"></span> <?php esc_html_e( 'Informations', 'surbma-magyar-woocommerce' ); ?></a></li>
		<?php
	$nav_items = ob_get_contents();
	ob_end_clean();

	return $nav_items;
}

function surbma_hc_page_social_nav() {
	$home_url = get_option( 'home' );
	$current_user = wp_get_current_user();

	ob_start();
		?>
		<li><a class="uk-inline" href="https://hucommerce.us20.list-manage.com/subscribe?u=8e6a039140be449ecebeb5264&id=2f5c70bc50&EMAIL=<?php echo urlencode( $current_user->user_email ); ?>&FNAME=<?php echo urlencode( $current_user->user_firstname ); ?>&LNAME=<?php echo urlencode( $current_user->user_lastname ); ?>&URL=<?php echo urlencode( $home_url ); ?>" target="_blank"><span class="uk-margin-small-right" uk-icon="icon: mail"></span> <?php esc_html_e( 'Newsletter', 'surbma-magyar-woocommerce' ); ?> <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<?php if ( SURBMA_HC_PRO_USER ) { ?>
		<li><a class="uk-inline" href="#" onclick="Beacon('open'); Beacon('navigate', '/ask/message')"><span class="uk-margin-small-right" uk-icon="icon: lifesaver"></span> <?php esc_html_e( 'Support', 'surbma-magyar-woocommerce' ); ?> <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<?php } else { ?>
		<li><a class="uk-inline" href="https://www.hucommerce.hu/ugyfelszolgalat/" target="_blank"><span class="uk-margin-small-right" uk-icon="icon: lifesaver"></span> <?php esc_html_e( 'Support', 'surbma-magyar-woocommerce' ); ?> <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<?php } ?>
		<li><a class="uk-inline" href="https://www.facebook.com/groups/HuCommerce.hu/" target="_blank"><span class="uk-margin-small-right" uk-icon="icon: facebook"></span> <?php esc_html_e( 'Facebook group', 'surbma-magyar-woocommerce' ); ?> <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<li><a class="uk-inline" href="https://hu.wordpress.org/plugins/surbma-magyar-woocommerce/" target="_blank"><span class="uk-margin-small-right" uk-icon="icon: wordpress"></span> <?php esc_html_e( 'WordPress.org', 'surbma-magyar-woocommerce' ); ?> <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<li><a class="uk-inline" href="https://www.hucommerce.hu" target="_blank"><span class="uk-margin-small-right" uk-icon="icon: world"></span> HuCommerce.hu <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<li><a class="uk-inline" href="https://www.hucommerce.hu/blog/" target="_blank"><span class="uk-margin-small-right" uk-icon="icon: rss"></span> HuCommerce Blog <span class="uk-position-center-right" uk-icon="icon: sign-out; ratio: .8"></span></a></li>
		<?php
	$nav_items = ob_get_contents();
	ob_end_clean();

	return $nav_items;
}

function surbma_hc_page_header() {
	ob_start();
		?>
		<div class="cps-admin cps-admin-2">
			<div class="wrap">
		<?php
	$page_header = ob_get_contents();
	ob_end_clean();

	return $page_header;
}

function surbma_hc_page_notifications() {
	$screen = get_current_screen();
	global $surbma_hc_license_page;

	ob_start();
		?>
		<?php if ( isset( $_GET['settings-updated'] ) && true == $_GET['settings-updated'] ) { ?>
			<div class="updated notice is-dismissible">
				<p><strong><?php esc_html_e( 'Settings saved.' ); ?></strong></p>
			</div>
		<?php } ?>

		<?php if ( isset( $_GET['hc-response'] ) && 'status' == $_GET['hc-response'] ) { ?>
			<div class="updated notice is-dismissible">
				<p><strong><?php esc_html_e( 'API sync finished.', 'surbma-magyar-woocommerce' ); ?></strong></p>
			</div>
		<?php } ?>

		<?php if ( isset( $_GET['hc-response'] ) && 'email-sent' == $_GET['hc-response'] ) { ?>
			<div class="updated notice is-dismissible">
				<p><strong><?php esc_html_e( 'Test email sent.', 'surbma-magyar-woocommerce' ); ?></strong></p>
			</div>
		<?php } ?>

		<?php // Free notification ?>
		<?php if ( 'free' == SURBMA_HC_PLUGIN_LICENSE && $surbma_hc_license_page != $screen->base ) { ?>
			<div class="notice notice-info is-dismissible">
				<p><strong class="uk-text-uppercase">Figyelem!</strong> Nézd meg, mivel nyújt többet a <a href="https://www.hucommerce.hu/bovitmenyek/hucommerce/" target="_blank">HuCommerce Pro</a> verzió!</p>
			</div>
		<?php } ?>

		<?php // Inactive notification ?>
		<?php if ( 'inactive' == SURBMA_HC_PLUGIN_LICENSE ) { ?>
			<div class="notice notice-error is-dismissible">
				<p><strong class="uk-text-uppercase">Még nem aktivált HuCommerce Pro licensz kulcs!</strong> <br>A megadott licensz kulcsod nincs aktiválva. A <strong>"HuCommerce → Licensz kezelés"</strong> menüpont alatt tudod a megadott licensz kulcsot frissíteni vagy újra aktiválni.</p>
			</div>
		<?php } ?>

		<?php // Invalid notification ?>
		<?php if ( 'invalid' == SURBMA_HC_PLUGIN_LICENSE ) { ?>
			<div class="notice notice-error is-dismissible">
				<p><strong class="uk-text-uppercase">Érvénytelen vagy lejárt HuCommerce Pro licensz kulcs!</strong> <br>Kérlek ellenőrizd az emailben küldött licensz kulcsot és add meg újra vagy frissítsd és aktiváld újra a <strong>"HuCommerce → Licensz kezelés"</strong> menüpont alatt!</p>
			</div>
		<?php } ?>

		<?php // Expired notification ?>
		<?php if ( 'expired' == SURBMA_HC_PLUGIN_LICENSE ) { ?>
			<div class="notice notice-error is-dismissible">
				<p><strong class="uk-text-uppercase">Lejárt HuCommerce Pro licensz kulcs!</strong> <br>Amennyiben szeretnéd tovább használni a HuCommerce Pro funkciókat vedd fel az <a href="https://www.hucommerce.hu/ugyfelszolgalat/" target="_blank"><strong>ügyfélszolgálattal</strong></a> a kapcsolatot.</p>
			</div>
		<?php } ?>

		<h2 class="uk-hidden"></h2>

		<?php
	$page_sidebar = ob_get_contents();
	ob_end_clean();

	return $page_sidebar;
}

function surbma_hc_page_sidebar() {
	ob_start();
		?>
		<div class="uk-text-center uk-margin-top uk-margin-medium-bottom"><a href="/wp-admin/admin.php?page=surbma-hucommerce-menu"><img src="<?php echo esc_url( SURBMA_HC_PLUGIN_URL ); ?>/assets/images/hucommerce-logo-2023-dark.png" alt="HuCommerce" width="150" height="27"></a></div>
		<ul class="cps-settings-nav uk-nav uk-nav-default">
			<?php echo surbma_hc_page_modules_nav(); ?>
			<li class="uk-nav-divider"><a></a></li>
			<?php echo surbma_hc_pages_nav(); ?>
			<li class="uk-nav-divider"><a></a></li>
			<?php echo surbma_hc_page_license_nav(); ?>
			<li class="uk-nav-divider"><a></a></li>
			<?php echo surbma_hc_page_social_nav(); ?>
			<li class="uk-nav-divider"><a></a></li>
		</ul>
		<?php
	$page_sidebar = ob_get_contents();
	ob_end_clean();

	return $page_sidebar;
}

function surbma_hc_page_mobile_nav() {
	ob_start();
		?>
		<div class="uk-width-auto uk-hidden@m">
			<a class="uk-text-secondary" href="#cps-settings-mobile-nav" uk-toggle><span uk-navbar-toggle-icon></span></a>
			<div id="cps-settings-mobile-nav" uk-modal="container: .cps-admin">
				<div class="uk-modal-dialog uk-modal-body">
					<button class="uk-modal-close-default" type="button" uk-close></button>
					<?php echo surbma_hc_page_sidebar(); ?>
				</div>
			</div>
		</div>
		<?php
	$page_sidebar = ob_get_contents();
	ob_end_clean();

	return $page_sidebar;
}

function surbma_hc_page_card_footer() {
	$home_url = get_option( 'home' );
	$current_user = wp_get_current_user();

	ob_start();
		?>
		<div class="uk-card-footer">
			<nav class="uk-navbar-container uk-navbar-transparent uk-margin" uk-navbar>
				<div class="uk-navbar-left uk-visible@s">
					<div class="uk-navbar-item">
						<strong>Tetszik a bővítmény? <a href="https://wordpress.org/support/plugin/surbma-magyar-woocommerce/reviews/#new-post" target="_blank">Kérlek értékeld 5 csillaggal!</a></strong>
					</div>
				</div>
				<div class="uk-navbar-right">
					<ul class="uk-navbar-nav">
						<li><a href="https://hucommerce.us20.list-manage.com/subscribe?u=8e6a039140be449ecebeb5264&id=2f5c70bc50&EMAIL=<?php echo urlencode( $current_user->user_email ); ?>&FNAME=<?php echo urlencode( $current_user->user_firstname ); ?>&LNAME=<?php echo urlencode( $current_user->user_lastname ); ?>&URL=<?php echo urlencode( $home_url ); ?>" target="_blank"><span uk-icon="icon: mail"></span></a></li>
						<?php if ( SURBMA_HC_PRO_USER ) { ?>
						<li><a href="#" onclick="Beacon('open'); Beacon('navigate', '/ask/message')"><span uk-icon="icon: lifesaver"></span></a></li>
						<?php } else { ?>
						<li><a href="https://www.hucommerce.hu/ugyfelszolgalat/" target="_blank"><span uk-icon="icon: lifesaver"></span></a></li>
						<?php } ?>
						<li><a href="https://www.facebook.com/groups/HuCommerce.hu/" target="_blank"><span uk-icon="icon: facebook"></span></a></li>
						<li><a href="https://hu.wordpress.org/plugins/surbma-magyar-woocommerce/" target="_blank"><span uk-icon="icon: wordpress"></span></a></li>
						<li><a href="https://www.hucommerce.hu" target="_blank"><span uk-icon="icon: world"></span></a></li>
					</ul>
				</div>
			</nav>
		</div>
		<?php
	$card_footer = ob_get_contents();
	ob_end_clean();

	return $card_footer;
}

function surbma_hc_page_footer() {
	ob_start();
		?>
			</div>
		</div>
		<?php
	$page_footer = ob_get_contents();
	ob_end_clean();

	return $page_footer;
}
