<?php
defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

// HPOS compatibility
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', defined( 'WOOSB_LITE' ) ? WOOSB_LITE : WOOSB_FILE );
	}
} );