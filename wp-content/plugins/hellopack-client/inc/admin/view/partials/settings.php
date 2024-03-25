<?php
/**
 * Settings panel partial
 *
 * @package HelloPack_Client
 * @since 2.0.0
 */

$token = hellopack_client()->get_option( 'token' );
$items = hellopack_client()->get_option( 'items', array() );

?>


<div id="settings" class="panel">

	<div
			class="hellopack-builder-important-notice hellopack-template-builder hellopack-db-card hellopack-db-card-first">
			<div class="intro-text">
				<h1 class="hellopack-panel-title">
					<svg class="hellopack-settings-icon">
						<use
								xlink:href="<?php echo HELLOPACK_CLIENT_URI . 'images/sprite.svg'; ?>?v=<?php echo HELLOPACK_CLIENT_VERSION; ?>#hellopack-settings-icon">
						</use>
					</svg>
					<?php esc_html_e( 'Settings', 'hellopack-client' ); ?>
				</h1>

				<p><?php esc_html_e( 'Here you can set up your API key and make various configurations related to the updater.', 'hellopack-client' ); ?>
				</p>
			</div>
	</div>



	<?php

	if ( function_exists( 'is_hellopack_plugin_active' ) && is_hellopack_plugin_active() ) {
		// Output HTML for active status
		echo '<div class="hellopack-builder-important-notice hellopack-template-builder hellopack-db-card hellopack-db-card-first">
             <h3>
                 <svg class="hellopack-license-icon success">
                     <use xlink:href="' . esc_url( HELLOPACK_CLIENT_URI . 'images/sprite.svg?v=' . HELLOPACK_CLIENT_VERSION . '#hellopack-license-icon' ) . '"></use>
                 </svg>
                 ' . esc_html__( 'Your Website is Registered', 'hellopack-client' ) . '
             </h3>
             <p>' . esc_html__( 'Congratulations, and thank you for registering your website.', 'hellopack-client' ) . ' ❤️</p>
           </div>';

	} else {
		// Output HTML for inactive status
		echo '<div class="hellopack-builder-important-notice hellopack-template-builder hellopack-db-card hellopack-db-card-first">
             <h3>
                 <svg class="hellopack-info-icon danger">
                     <use xlink:href="' . esc_url( HELLOPACK_CLIENT_URI . 'images/sprite.svg?v=' . HELLOPACK_CLIENT_VERSION . '#hellopack-info-icon' ) . '"></use>
                 </svg>
                 ' . esc_html__( 'HelloPack is not active', 'hellopack-client' ) . '
             </h3>
		   ';
		if ( ! defined( 'HELLOPACK_WHITELABEL' ) ) {
			echo '
             <p>' . esc_html__( 'After installing the HelloPack package manager, the next step is to activate it on your website. To do this, you need to create an API key on the HelloWP.io site, which you can then enter in the HelloPack package manager on your WordPress website to activate the plugin. For more details,', 'hellopack-client' ) . ' <a target="_blank" href="https://hub.hellowp.io/docs/dokumentacio/hellopack/aktivalas">' . esc_html__( 'click here', 'hellopack-client' ) . '</a>.</p>
             <p>
                 <h4>' . esc_html__( 'How to Create an API Key?', 'hellopack-client' ) . '</h4>
                 <ol class="how-to-hellopack">
                     <li>' . esc_html__( 'Log in to the HelloWP.io site and click on the', 'hellopack-client' ) . ' <a target="_blank" href="https://hellowp.io/hu/helloconsole/hellopack-kozpont/api-creator/">' . esc_html__( 'API Key Generator', 'hellopack-client' ) . '</a> ' . esc_html__( 'menu item.', 'hellopack-client' ) . '</li>
                     <li class="hellopack-domain">' . esc_html__( 'Enter the following domain name:', 'hellopack-client' ) . ' <strong>' . esc_html( str_replace( array( 'http://', 'https://' ), '', get_site_url() ) ) . '</strong></li>
                     <li>' . esc_html__( 'Copy the received API key here.', 'hellopack-client' ) . '</li>
                 </ol>
             </p>
          ';

		}
		echo ' </div>';
	}
	?>




	<div class="hellopack-client-columns">
			<?php settings_fields( hellopack_client()->get_slug() ); ?>
			<?php HelloPack_Client_Admin::do_settings_sections( hellopack_client()->get_slug(), 2 ); ?>
	</div>
	<?php if ( is_hellopack_plugin_active() ) : ?>
	<div
			class="hellopack-builder-important-notice hellopack-template-builder hellopack-db-card hellopack-db-card-first">

			<div class="hellopack-builder-option">
				<div class="hellopack-builder-option-title">
					<h3>

						<?php esc_html_e( 'Admin Notification Cleaner', 'hellopack-client' ); ?>
					</h3>
					<span class="hellopack-builder-option-label">
						<p>
								<?php esc_html_e( 'Enable the Admin Notification Cleaner throughout the admin area.', 'hellopack-client' ); ?>
						</p>
					</span>
				</div>

				<div class="hellopack-builder-option-field">
					<div class="hellopack-form-radio-button-set ui-buttonset enable-builder-ui">
						<input type="hidden" class="button-set-value"
								value="<?php echo esc_html( hellopack_client()->get_option( 'silent_mode' ) ); ?>"
								name="<?php echo esc_attr( hellopack_client()->get_option_name() ); ?>[silent_mode]"
								id="hellopack_silent_mode">
						<a data-value="on" class="ui-button buttonset-item 
						<?php
						if ( 'on' === hellopack_client()->get_option( 'silent_mode' ) ) {
							echo 'ui-state-active';}
						?>
" href="#"><?php esc_html_e( 'On', 'hellopack-client' ); ?></a>
						<a data-value="off" class="ui-button buttonset-item 
						<?php
						if ( empty( hellopack_client()->get_option( 'silent_mode' ) ) || 'off' === hellopack_client()->get_option( 'silent_mode' ) ) {
							echo 'ui-state-active';}
						?>

" href="#"><?php esc_html_e( 'Off', 'hellopack-client' ); ?></a>
					</div>
				</div>
			</div>

			<script>
			jQuery(document).ready(function() {

				jQuery('.hellopack-builder-admin-toggle-heading').on('click', function() {
					jQuery(this).parent().find('.hellopack-builder-admin-toggle-content')
						.slideToggle(
								300);
					if (jQuery(this).find('.hellopack-builder-admin-toggle-icon').hasClass(
								'hellopack-plus')) {
						jQuery(this).find('.hellopack-builder-admin-toggle-icon').removeClass(
								'hellopack-plus').addClass('hellopack-minus');
					} else {
						jQuery(this).find('.hellopack-builder-admin-toggle-icon').removeClass(
								'hellopack-minus').addClass('hellopack-plus');
					}

				});

				jQuery('.enable-builder-ui .ui-button').on('click', function(e) {
					e.preventDefault();

					jQuery(this).parent().find('#hellopack_silent_mode').val(jQuery(this).data(
						'value'));

					jQuery(this).parent().find('.ui-button').removeClass('ui-state-active');
					jQuery(this).addClass('ui-state-active');
				});

			});
			</script>

			<span class="hellopack-builder-option-description">
				<p>
					<?php esc_html_e( 'The Admin Notification Cleaner is a carefully selected compilation of JavaScript and CSS snippets aimed at streamlining the WordPress admin interface. It removes superfluous notifications that plugin developers often introduce. The goal is to foster a tidier and more concentrated admin dashboard, thereby boosting productivity and improving the user experience.', 'hellopack-client' ); ?>
				</p>
				<p>
					<?php
					_e( 'Are you encountering an annoying notification in the WordPress admin area? Contribute to the growth of the Admin Notification Cleaner collection. Submit the CSS class on GitHub at the', 'hellopack-client' );
					?>
					<a class="anc-github" target="_blank" href="https://github.com/trueqap/admin-notification-cleaner">
						<?php
							esc_html_e(
								'Admin Notification Cleaner repository',
								'hellopack-client'
							);
						?>
					</a>
				</p>
			</span>
	</div>
	<?php endif; ?>
	<p class="submit hellopack-settings-save">
			<input type="submit" name="submit" id="submit" class="button button-primary"
				value="<?php esc_html_e( 'Save Changes', 'hellopack-client' ); ?>" />
			<?php if ( ( '' !== $token || ! empty( $items ) ) && 10 !== has_action( 'admin_notices', array( $this, 'error_notice' ) ) ) { ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'authorization' => 'check' ), hellopack_client()->get_page_url() ) ); ?>"
				class="button button-secondary auth-check-button"
				style="margin:0 5px"><?php esc_html_e( 'Test API Connection', 'hellopack-client' ); ?></a>
			<?php } ?>
	</p>
</div>
