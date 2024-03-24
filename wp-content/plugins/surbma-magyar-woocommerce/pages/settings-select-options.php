<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

global $couponfieldposition_options;
global $returntoshopcartposition_options;
global $returntoshopcheckoutposition_options;
global $shippingmethodstohide_options;
global $legalconfirmationsposition_options;
global $smtpport_options;
global $smtpsecure_options;
global $emptycartbutton_cartpage_options;
global $emptycartbutton_checkoutpage_options;
global $productpricehistory_statisticslinkdisplay_options;

$couponfieldposition_options = array(
	'beforecheckoutform' => array(
		'value' => 'beforecheckoutform',
		'label' => __( 'Before Checkout form', 'surbma-magyar-woocommerce' )
	),
	'aftercheckoutform' => array(
		'value' => 'aftercheckoutform',
		'label' => __( 'After Checkout form', 'surbma-magyar-woocommerce' )
	)
);

$returntoshopcartposition_options = array(
	'nocart' => array(
		'value' => 'nocart',
		'label' => __( 'Don\'t show on Cart page', 'surbma-magyar-woocommerce' )
	),
	'beforecarttable' => array(
		'value' => 'beforecarttable',
		'label' => __( 'Before Product table (with text)', 'surbma-magyar-woocommerce' )
	),
	'aftercarttable' => array(
		'value' => 'aftercarttable',
		'label' => __( 'After Product table (with text)', 'surbma-magyar-woocommerce' )
	),
	'cartactions' => array(
		'value' => 'cartactions',
		'label' => __( 'Next to Update cart button (without text)', 'surbma-magyar-woocommerce' )
	),
	'proceedtocheckout' => array(
		'value' => 'proceedtocheckout',
		'label' => __( 'Under Proceed to checkout button (without text)', 'surbma-magyar-woocommerce' )
	)
);

$returntoshopcheckoutposition_options = array(
	'nocheckout' => array(
		'value' => 'nocheckout',
		'label' => __( 'Don\'t show on Checkout page', 'surbma-magyar-woocommerce' )
	),
	'beforecheckoutform' => array(
		'value' => 'beforecheckoutform',
		'label' => __( 'Before Checkout form (with text)', 'surbma-magyar-woocommerce' )
	),
	'aftercheckoutform' => array(
		'value' => 'aftercheckoutform',
		'label' => __( 'After Checkout form (with text)', 'surbma-magyar-woocommerce' )
	)
);

$shippingmethodstohide_options = array(
	'showall' => array(
		'value' => 'showall',
		'label' => __( 'Show all shipping methods', 'surbma-magyar-woocommerce' )
	),
	'hideall' => array(
		'value' => 'hideall',
		'label' => __( 'Hide all shipping methods, except Free shipping', 'surbma-magyar-woocommerce' )
	),
	'hideall_except_local' => array(
		'value' => 'hideall_except_local',
		'label' => __( 'Hide all shipping methods, except Free shipping and Local pickup', 'surbma-magyar-woocommerce' )
	),
	'hideall_except_pickups' => array(
		'value' => 'hideall_except_pickups',
		'label' => __( 'Hide all shipping methods, except Free shipping, Local pickup and other Pickup methods', 'surbma-magyar-woocommerce' )
	)
);

$legalconfirmationsposition_options = array(
	'woocommerce_review_order_before_submit' => array(
		'value' => 'woocommerce_review_order_before_submit',
		'label' => __( 'Before Place order button', 'surbma-magyar-woocommerce' )
	),
	'woocommerce_after_order_notes' => array(
		'value' => 'woocommerce_after_order_notes',
		'label' => __( 'After Order notes field', 'surbma-magyar-woocommerce' )
	)
);

$smtpport_options = array(
	'25' => array(
		'value' => '25',
		'label' => '25'
	),
	'465' => array(
		'value' => '465',
		'label' => '465'
	),
	'587' => array(
		'value' => '587',
		'label' => '587'
	),
	'2525' => array(
		'value' => '2525',
		'label' => '2525'
	),
);

$smtpsecure_options = array(
	'default' => array(
		'value' => 'default',
		'label' => __( 'Default encryption', 'surbma-magyar-woocommerce' )
	),
	'tls' => array(
		'value' => 'tls',
		'label' => 'TLS'
	),
	'ssl' => array(
		'value' => 'ssl',
		'label' => 'SSL'
	),
);

$emptycartbutton_cartpage_options = array(
	'none' => array(
		'value' => 'none',
		'label' => __( 'Don\'t show on Cart page', 'surbma-magyar-woocommerce' )
	),
	'woocommerce_cart_coupon' => array(
		'value' => 'woocommerce_cart_coupon',
		'label' => __( 'Next to the Apply coupon button', 'surbma-magyar-woocommerce' )
	),
	'woocommerce_cart_actions' => array(
		'value' => 'woocommerce_cart_actions',
		'label' => __( 'Next to the Update cart button', 'surbma-magyar-woocommerce' )
	),
	'woocommerce_before_cart_collaterals' => array(
		'value' => 'woocommerce_before_cart_collaterals',
		'label' => __( 'Under the Products table', 'surbma-magyar-woocommerce' )
	)
);

$emptycartbutton_checkoutpage_options = array(
	'none' => array(
		'value' => 'none',
		'label' => __( 'Don\'t show on Checkout page', 'surbma-magyar-woocommerce' )
	),
	'woocommerce_before_checkout_form' => array(
		'value' => 'woocommerce_before_checkout_form',
		'label' => __( 'Before Checkout form (with text)', 'surbma-magyar-woocommerce' )
	),
	'woocommerce_after_checkout_form' => array(
		'value' => 'woocommerce_after_checkout_form',
		'label' => __( 'After Checkout form (with text)', 'surbma-magyar-woocommerce' )
	)
);

$productpricehistory_statisticslinkdisplay_options = array(
	'show' => array(
		'value' => 'show',
		'label' => __( 'Show advanced statistics, when Product is on Sale', 'surbma-magyar-woocommerce' )
	),
	'always' => array(
		'value' => 'always',
		'label' => __( 'Always show advanced statistics', 'surbma-magyar-woocommerce' )
	),
	'hide' => array(
		'value' => 'hide',
		'label' => __( 'Hide advanced statistics', 'surbma-magyar-woocommerce' )
	)
);
