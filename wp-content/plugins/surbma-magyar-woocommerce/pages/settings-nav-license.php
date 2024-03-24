<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

function surbma_hc_get_starred( $str ) {
	$str_length = strlen( $str );
	if ( $str_length < 10 ) {
		return $str;
	} else {
		return substr( $str, 0, 5 ) . str_repeat( '*', $str_length - 10 ) . substr( $str, $str_length - 5, 5 );
	}
}

$license_status = get_option( 'surbma_hc_license_status', array() );
$custom_product_id = isset( $_GET['hc-product_id'] ) ? $_GET['hc-product_id'] : false;

?>
<p>Itt kell megadnod és aktiválnod a HuCommerce Pro API kulcsodat. Sikeres aktiválás után a HuCommerce Pro minden funkciója elérhető lesz számodra az aktív előfizetésed alatt. Lejárt előfizetés esetén a Pro funkciók kikapcsolnak!</p>

<form class="uk-form-stacked" method="post" action="options.php">
	<?php settings_fields( 'surbma_hc_license_options' ); ?>
	<?php $license_options = get_option( 'surbma_hc_license', array() ); ?>
	<?php $home_url = parse_url( get_option( 'home' ) ); ?>
	<?php $instance = isset( $home_url['host'] ) ? $home_url['host'] : ''; ?>

	<?php $disabled = 'active' == SURBMA_HC_PLUGIN_LICENSE ? ' disabled' : ''; ?>

	<?php $inputType = defined( 'WP_DEBUG' ) && 1 == WP_DEBUG ? 'text' : 'hidden'; ?>

	<?php
		if ( $custom_product_id ) {
			$product_idValue = $custom_product_id;
		} elseif ( isset( $license_options['product_id'] ) && $license_options['product_id'] ) {
			$product_idValue = $license_options['product_id'];
		} else {
			$product_idValue = '1135';
		}
	?>
	<input id="surbma_hc_license[product_id]" class="uk-input uk-form-large uk-margin-bottom" type="<?php echo esc_attr( $inputType ); ?>" name="surbma_hc_license[product_id]" value="<?php echo esc_attr( wp_unslash( $product_idValue ) ); ?>" placeholder="Product ID" style="font-family: monospace;"<?php echo esc_html( $disabled ); ?> />

	<?php $instanceValue = isset( $license_options['instance'] ) && $license_options['instance'] ? $license_options['instance'] : wp_generate_password( 40, false ); ?>
	<?php $instanceValue = isset( $license_options['instance'] ) && $license_options['instance'] ? $license_options['instance'] : $instance; ?>
	<input id="surbma_hc_license[instance]" class="uk-input uk-form-large uk-margin-bottom" type="<?php echo esc_attr( $inputType ); ?>" name="surbma_hc_license[instance]" value="<?php echo esc_attr( $instanceValue ); ?>" placeholder="Instance" style="font-family: monospace;"<?php echo esc_html( $disabled ); ?> />

	<div class="uk-grid-small" uk-grid>

		<label class="uk-form-label uk-width-1-1" for="surbma_hc_license[licensekey]" style="line-height: 1;">API kulcs:</label>
		<div class="uk-form-controls uk-width-expand@xl">
			<div class="uk-inline uk-width-expand">
				<span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
				<?php $licensekeyValue = isset( $license_options['licensekey'] ) ? $license_options['licensekey'] : ''; ?>
				<input id="surbma_hc_license[licensekey]" class="uk-input uk-form-large" type="text" name="surbma_hc_license[licensekey]" value="<?php echo esc_attr( wp_unslash( $licensekeyValue ) ); ?>" placeholder="API kulcs" style="font-family: monospace;"<?php echo esc_html( $disabled ); ?> />
			</div>
		</div>

		<div class="uk-width-auto@xl">
			<?php if ( 'active' == SURBMA_HC_PLUGIN_LICENSE ) { ?>
				<input type="submit" class="uk-button uk-button-danger uk-button-large uk-width-auto@l" value="Deaktiválás" />
			<?php } else { ?>
				<input type="submit" class="uk-button uk-button-primary uk-button-large uk-width-auto@l" value="Aktiválás" />
			<?php } ?>
		</div>

	</div>
</form>

<?php
	if ( 'active' == SURBMA_HC_PLUGIN_LICENSE ) {
		$licensestatus = '<span class="uk-label uk-label-success">Aktív</span>';
	} elseif ( 'invalid' == SURBMA_HC_PLUGIN_LICENSE ) {
		$licensestatus = '<span class="uk-label uk-label-danger">Érvénytelen API kulcs</span>';
	} else {
		$licensestatus = '<span class="uk-label">Nincs aktiválva</span>';
	}
?>
<p><strong>API állapot:</strong> <?php echo wp_kses_post( $licensestatus ); ?></p>
<hr>
<p><a class="uk-button uk-button-primary" href="<?php echo esc_url( add_query_arg( 'hc-request', 'status' ) ); ?>" uk-tooltip="title: API kulcs manuális szinkronizálása, ha valamiért nem frissülne automatikusan az API állapot.; pos: top"><span style="position: relative;top: -2px;" uk-icon="icon: refresh; ratio: .8"></span> API szinkronizálás</a> <a class="uk-button uk-button-primary" href="https://www.hucommerce.hu/fiokom/api-keys/" target="_blank" uk-tooltip="title: API kulcs kezelése a Fiókodban, a HuCommerce.hu weboldalon.; pos: top"><span style="position: relative;top: -2px;" uk-icon="icon: cog; ratio: .8"></span> API kezelés</a> <a class="uk-button uk-button-primary" href="https://www.hucommerce.hu/fiokom/elofizetesek/" target="_blank" uk-tooltip="title: Előfizetéseid kezelése a Fiókodban, a HuCommerce.hu weboldalon.; pos: top"><span style="position: relative;top: -2px;" uk-icon="icon: credit-card; ratio: .8"></span> Előfizetés kezelés</a></p>
<?php if ( $licensekeyValue ) { ?>
<h4>API kulcshoz kapcsolódó adatok</h4>
<ul class="uk-list">
	<li><strong>Összesen aktiválható weboldalak száma:</strong> <?php echo wp_kses_post( intval( $license_status['total_activations_purchased'] ) ); ?></li>
	<li><strong>Eddig aktivált weboldalak száma:</strong> <?php echo wp_kses_post( intval( $license_status['total_activations'] ) ); ?></li>
	<li><strong>További aktiválható weboldalak száma:</strong> <?php echo wp_kses_post( intval( $license_status['activations_remaining'] ) ); ?></li>
</ul>
<?php }

if ( defined( 'WP_DEBUG' ) && 1 == WP_DEBUG ) {
	echo '<h4>License options</h4>';
	echo '<pre>';
	print_r( $license_options );
	echo '</pre>';
	echo '<h4>License status</h4>';
	echo '<pre>';
	print_r( $license_status );
	echo '</pre>';
}
