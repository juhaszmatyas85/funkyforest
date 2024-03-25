<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define("LIST_TYPE_NEWSLETTER", 1);
define("LIST_TYPE_ECOMMERCE", 11);
define("FORM_TYPE_SUBSCRIBE", 1);
define("FORM_TYPE_UPDATE", 2);
define("FORM_TYPE_ORDER", 4);

/**
 * SalesAutopilot Integration
 *
 * Allows integration with SalesAutopilot eCommerce
 *
 * @class 		SS_WC_Integration_SalesAutopilot
 * @extends		WC_Integration
 * @version		1.4.6
 * @package		WooCommerce SalesAutopilot
 * @author 		Gyorgy Khauth
 */
class SS_WC_Integration_SalesAutopilot extends WC_Integration
{

	/**
	 * Init and hook in the integration.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		global $woocommerce;

		if (!class_exists('SalesAutopilotAPI')) {
			include_once('api/class-SalesAutopilotAPI.php');
		}

		$this->id                 = 'salesautopilot';
		$this->method_title       = __('SalesAutopilot', 'ss_wc_salesautopilot');
		$this->method_description = __('SalesAutpilot - Put Your Sales on Autopilot ...once and for all', 'ss_wc_salesautopilot');

		// Load the settings.
		$this->init_settings();
		$this->init_form_fields();

		// Use API username and password to connect SalesAutopilot and setup target list and form
		$this->api_username      = $this->get_option('api_username');
		$this->api_password      = $this->get_option('api_password');
		$this->enabled           = $this->get_option('enabled');
		$this->saplist           = $this->get_option('saplist');
		$this->form              = $this->get_option('form');
		$this->newsletterenabled = $this->get_option('newsletterenabled');
		$this->swapnameorder     = $this->get_option('swapnameorder');
		$this->sanlist           = $this->get_option('sanlist');
		$this->sanform           = $this->get_option('sanform');

		// Hooks
		add_action('admin_notices',                                       array(&$this, 'checks'));
		add_action('woocommerce_update_options_integration_' . $this->id, array(&$this, 'process_admin_options'));

		// Use 'woocommerce_thankyou' action hook which fires after the checkout process on the "thank you" page
		// add_action( 'woocommerce_thankyou', array( &$this, 'order_status_changed' ), 10, 1 );

		// hook into woocommerce order status changed hook to handle the desired subscription event trigger
		add_action('woocommerce_order_status_changed', array(&$this, 'order_status_changed'), 10, 3);

		// hook into woocommerce checkout page
		add_action('woocommerce_after_order_notes', array($this, 'newsletter_checkbox'));

		// Do this when order is created
		add_action('woocommerce_checkout_update_order_meta', array($this, 'on_checkout_update_order'));
	}

	/**
	 * Check if the user has enabled the plugin functionality, but hasn't provided an api key
	 **/
	function checks()
	{
		if ($this->settings['enabled'] == 'yes') {
			// Check required fields
			if (!$this->settings['api_username']) {
				echo '<div class="error"><p>' .
					sprintf(
						__('SalesAutopilot error: Please enter your api username and password <a href="%s">here</a>', 'ss_wc_salesautopilot'),
						admin_url('admin.php?page=woocommerce&tab=integration&section=salesautopilot')
					) .
					'</p></div>';
				return;
			}
		}
	}

	/**
	 * Display signup checkbox on the order form
	 **/
	function newsletter_checkbox()
	{
		if ($this->settings['enabled'] == 'yes' && $this->settings['newsletterenabled'] == 'yes') {
			//echo '<div id="salesautopilot_subscription_section">';

			woocommerce_form_field(
				'salesautopilot_checkout_subscribe',
				array(
					'type'  => 'checkbox',
					'class' => array('salesautopilot-checkout-class form-row-wide'),
					'label' => htmlspecialchars(stripslashes(__('Subscribe me to the newsletter', 'ss_wc_salesautopilot'))), // TODO: ???
				),
				''
			);

			//echo '</div>';
		}
	}

	/**
	 * Upon saving the order
	 */
	function on_checkout_update_order($id)
	{
		if ($this->settings['enabled'] == 'yes' && $this->settings['saplist'] > 0 && $this->settings['form'] > 0) {
			$order = new WC_Order($id);
			$this->send_order($order);
			$order->add_order_note(__('Order sent to SalesAutopilot. List: #', 'ss_wc_salesautopilot') . $this->settings['saplist'] . __(' Form: #', 'ss_wc_salesautopilot') . $this->settings['form']);
		}

		if ($this->settings['enabled'] == 'yes' && $this->settings['newsletterenabled'] == 'yes' && $this->settings['sanlist'] > 0 && $this->settings['sanform'] > 0 && isset($_POST['salesautopilot_checkout_subscribe'])) {
			$SalesAutopilot = new SalesAutopilotAPI($this->settings['api_username'], $this->settings['api_password']);
			$order          = new WC_Order($id);
			$data           = array();

			if ($this->settings['swapnameorder'] == 'yes') {
				$data['mssys_lastname']  = $order->get_billing_first_name();
				$data['mssys_firstname'] = $order->get_billing_last_name();
			} else {
				$data['mssys_firstname'] = $order->get_billing_first_name();
				$data['mssys_lastname']  = $order->get_billing_last_name();
			}

			$data['email'] = $order->get_billing_email();
			$retval        = $SalesAutopilot->call('subscribe/' . $this->settings['sanlist'] . '/form/' . $this->settings['sanform'], $data);

			$order->add_order_note(__('Client subscribed to SalesAutopilot newsletter list #', 'ss_wc_salesautopilot') . $this->settings['sanlist'] . __('. Subscribe form: #', 'ss_wc_salesautopilot') . $this->settings['sanform']);

			if (isset($_POST['salesautopilot_checkout_subscribe']))
				update_post_meta($id, 'salesautopilot_newsletter_signup', 1);

			if ($SalesAutopilot->status >= 300) {
				$order->add_order_note(__('WooCommerce to SalesAutopilot API call failed: (', 'ss_wc_salesautopilot') . $SalesAutopilot->status . ') ' . $SalesAutopilot->errorMessage);
				//error_log('WooCommerce to SalesAutopilot API call failed: (' . $SalesAutopilot->status . ') ' . $SalesAutopilot->errorMessage);
			}
		}
	}

	/**
	 * order_status_changed function.
	 *
	 * @access public
	 * @param $id
	 * @param string $status
	 * @param string $new_status
	 * @return void
	 */
	public function order_status_changed($id, $status = 'new', $new_status = 'pending')
	{
		if ($this->settings['enabled'] == 'yes' && $this->settings['saplist'] > 0 && ($this->settings['uform'] > 0 || $this->settings['other_form'] > 0)) {
			$this->update_status($id, $new_status);
		}
	}

	/**
	 * List is set.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_list()
	{
		return isset($this->settings['saplist']);
	}

	/**
	 * Form is set.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_form()
	{
		return !empty($this->settings['form']);
	}

	/**
	 * has_api_key function.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_api_key()
	{
		return !empty($this->settings['api_username']) && !empty($this->settings['api_password']);
	}

	/**
	 * is_valid function.
	 *
	 * @access public
	 * @return boolean
	 */
	public function is_valid()
	{
		return !empty($this->settings['enabled']) && $this->settings['enabled'] == 'yes' && $this->has_api_key() && $this->has_list();
	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields()
	{
		if (is_admin()) {
			$lists = $this->get_lists(LIST_TYPE_ECOMMERCE);

			if ($lists === false) {
				$lists  = array();
				$forms  = array();
				$uforms = array();
			} else {
				$forms = $this->get_forms('saplist', FORM_TYPE_ORDER);

				if ($forms === false) {
					$forms = array();
				}

				$uforms = $this->get_forms('saplist', FORM_TYPE_UPDATE);

				if ($uforms === false) {
					$uforms = array();
				}
			}

			$n_lists = $this->get_lists(LIST_TYPE_NEWSLETTER);

			if ($n_lists === false) {
				$n_lists = array();
				$n_forms = array();
			} else {
				$n_forms = $this->get_forms('sanlist', FORM_TYPE_SUBSCRIBE);

				if ($n_forms === false) {
					$n_forms = array();
				}
			}

			//error_log(serialize($this));

			$sap_lists  = $this->has_api_key()
				? array('' => __('Select a list...', 'ss_wc_salesautopilot')) + $lists
				: array('' => __('Enter your API username/password and save to see your lists', 'ss_wc_salesautopilot'));
			$sap_forms  = ($this->has_api_key() && (is_array($_POST) && sizeof($_POST) > 0
				? isset($_POST['woocommerce_salesautopilot_saplist']) && $_POST['woocommerce_salesautopilot_saplist'] > 0
				: isset($this->settings['saplist']) && $this->settings['saplist'] > 0)
				? array('' => __('Select a form...', 'ss_wc_salesautopilot')) + $forms
				: array('' => __('Select a list in order to see its order forms.', 'ss_wc_salesautopilot'))
			);
			$sap_uforms = ($this->has_api_key() && (is_array($_POST) && sizeof($_POST) > 0
				? isset($_POST['woocommerce_salesautopilot_saplist']) && $_POST['woocommerce_salesautopilot_saplist'] > 0
				: isset($this->settings['saplist']) && $this->settings['saplist'] > 0)
				? array('' => __('Select a form...', 'ss_wc_salesautopilot')) + $uforms
				: array('' => __('Select a list in order to see its update forms.', 'ss_wc_salesautopilot'))
			);
			$san_lists  = $this->has_api_key()
				? array('' => __('Select a list...', 'ss_wc_salesautopilot')) + $n_lists
				: array('' => __('Enter your API username/password and save to see your lists', 'ss_wc_salesautopilot'));
			$san_forms  = ($this->has_api_key() && (is_array($_POST) && sizeof($_POST) > 0
				? isset($_POST['woocommerce_salesautopilot_sanlist']) && $_POST['woocommerce_salesautopilot_sanlist'] > 0
				: isset($this->settings['sanlist']) && $this->settings['sanlist'] > 0)
				? array('' => __('Select a form...', 'ss_wc_salesautopilot')) + $n_forms
				: array('' => __('Select a list in order to see its forms.', 'ss_wc_salesautopilot'))
			);

			$formfields = array(
				'enabled' => array(
					'title'       => __('Enable/Disable', 'ss_wc_salesautopilot'),
					'label'       => __('Enable SalesAutopilot', 'ss_wc_salesautopilot'),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => 'no'
				),
				'api_username' => array(
					'title'       => __('API Username', 'ss_wc_salesautopilot'),
					'description' => __('SalesAutopilot API username. <a href="http://www.salesautopilot.com/knowledge-base/api/api-key-pairs" target="_blank">How to get your API username/password</a>', 'ss_wc_salesautopilot'),
					'type'        => 'text',
					'default'     => ''
				),
				'api_password' => array(
					'title'       => __('API Password', 'ss_wc_salesautopilot'),
					'description' => __('SalesAutopilot API password. <a href="http://www.salesautopilot.com/knowledge-base/api/api-key-pairs" target="_blank">How to get your API username/password</a>', 'ss_wc_salesautopilot'),
					'type'        => 'text',
					'default'     => ''
				),
				'saplist' => array(
					'title'       => __('eCommerce List', 'ss_wc_salesautopilot'),
					'description' => __('Orders will be added to this eCommerce list.', 'ss_wc_salesautopilot'),
					'type'        => 'select',
					'default'     => '',
					'options'     => $sap_lists,
				),
				'form' => array(
					'title'       => __('Order Form', 'ss_wc_salesautopilot'),
					'description' => __('Orders will be added through this eCommerce form.', 'ss_wc_salesautopilot'),
					'type'        => 'select',
					'default'     => '',
					'options'     => $sap_forms,
				),
				'uform' => array(
					'title'       => __('Billing Form', 'ss_wc_salesautopilot'),
					'description' => __('Payed status will be set in SalesAutopilot through this update form. This for should be used to generate bills after payment.', 'ss_wc_salesautopilot'),
					'type'        => 'select',
					'default'     => '',
					'options'     => $sap_uforms,
				),
				'other_form' => array(
					'title'       => __('Other Status Change Form', 'ss_wc_salesautopilot'),
					'description' => __('Order status (except the payed) will be set in SalesAutopilot through this update form.', 'ss_wc_salesautopilot'),
					'type'        => 'select',
					'default'     => '',
					'options'     => $sap_uforms,
				),
				'newsletterenabled' => array(
					'title'       => __('Subscription form', 'ss_wc_salesautopilot'),
					'label'       => __('Display subscription form', 'ss_wc_salesautopilot'),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => 'no'
				),
				'sanlist' => array(
					'title'       => __('Newsletter List', 'ss_wc_salesautopilot'),
					'description' => __('Newsletter List', 'ss_wc_salesautopilot'),
					'type'        => 'select',
					'default'     => '',
					'options'     => $san_lists,
				),
				'sanform' => array(
					'title'       => __('Newsletter form', 'ss_wc_salesautopilot'),
					'description' => __('Newsletter form', 'ss_wc_salesautopilot'),
					'type'        => 'select',
					'default'     => '',
					'options'     => $san_forms
				),
				'swapnameorder' => array(
					'title'       => __('Swap firstname/lastname order', 'ss_wc_salesautopilot'),
					'label'       => __('Swap firstname/lastname order when sending to SalesAutopilot', 'ss_wc_salesautopilot'),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				)
			);

			if ($this->has_api_key()) {
				$this->form_fields = array(
					'enabled'           => $formfields['enabled'],
					'api_username'      => $formfields['api_username'],
					'api_password'      => $formfields['api_password'],
					'saplist'           => $formfields['saplist'],
					'form'              => $formfields['form'],
					'uform'             => $formfields['uform'],
					'other_form'        => $formfields['other_form'],
					'newsletterenabled' => $formfields['newsletterenabled'],
					'sanlist'           => $formfields['sanlist'],
					'sanform'           => $formfields['sanform'],
					'swapnameorder'     => $formfields['swapnameorder']
				);
			} else {
				$this->form_fields = array(
					'enabled'      => $formfields['enabled'],
					'api_username' => $formfields['api_username'],
					'api_password' => $formfields['api_password']
				);
			}

			$this->wc_enqueue_js("
				if(jQuery('#woocommerce_salesautopilot_enabled').length) {
					jQuery(function() {
						if (jQuery('#woocommerce_salesautopilot_saplist').val() != '' && jQuery('#woocommerce_salesautopilot_form').val() == '') {
							jQuery('body,html').animate({scrollTop: jQuery('#woocommerce_salesautopilot_saplist').offset().top}, 2000);
						} else if (jQuery('#woocommerce_salesautopilot_sanlist').val() != '' && jQuery('#woocommerce_salesautopilot_sanform').val() == '') {
							jQuery('body,html').animate({scrollTop: jQuery('#woocommerce_salesautopilot_sanlist').offset().top}, 2000);
						}
					})

					jQuery('#woocommerce_salesautopilot_saplist, #woocommerce_salesautopilot_sanlist').change(function() {
						jQuery('.woocommerce-save-button').click();
					});

					jQuery('#mainform').submit(function() {
						var zdiv = jQuery('<div>').attr('id', 'z_overlay').css({'background': 'rgba(255,255,255,0.4) url(/wp-admin/images/spinner-2x.gif) no-repeat center center', 'width': '100%', 'height': '100%', 'position': 'fixed', 'top': 0, 'left': 0, 'zIndex': '99999'})
						jQuery('body').append(zdiv);
					});
				}
			");
		}
	} // End init_form_fields()

	/**
	 * WooCommerce 2.1 support for wc_enqueue_js
	 *
	 * @since 1.2.1
	 *
	 * @access private
	 * @param string $code
	 * @return void
	 */
	private function wc_enqueue_js($code)
	{

		if (function_exists('wc_enqueue_js')) {
			wc_enqueue_js($code);
		} else {
			global $woocommerce;
			$woocommerce->add_inline_js($code);
		}
	}

	/**
	 * Get eCommerce lists from SalesAutopilot.
	 *
	 * @access public
	 * @param $type
	 * @return array|bool
	 */
	public function get_lists($type)
	{
		if ($this->has_api_key()) {
			$lists          = array();
			$SalesAutopilot = new SalesAutopilotAPI($this->settings['api_username'], $this->settings['api_password']);
			$retval         = $SalesAutopilot->call('zapier/getlists/' . $type);

			if (!is_array($retval)) {
				error_log(sprintf(__('Unable to load lists from SalesAutopilot: (%s) %s', 'ss_wc_salesautopilot'), $SalesAutopilot->status, $SalesAutopilot->errorMessage));

				return false;
			} else {
				foreach ($retval as $list) {
					$lists[$list['nl_id']] = $list['list_name'];
				}
			}

			return $lists;
		} else {
			return false;
		}
	}

	/**
	 * Get SalesAutopilot forms
	 *
	 * @access public
	 * @param $list
	 * @param $method
	 * @return array|bool
	 */
	public function get_forms($list, $method)
	{
		if ($this->has_api_key() && isset($this->settings[$list]) && is_numeric($this->settings[$list]) && $this->settings[$list] > 0) {
			$forms          = array();
			$SalesAutopilot = new SalesAutopilotAPI($this->settings['api_username'], $this->settings['api_password']);
			$retval         = $SalesAutopilot->call('zapier/getforms/' . $this->settings[$list] . '/' . $method);

			if (!is_array($retval)) {
				error_log(sprintf(__('Unable to load forms from SalesAutopilot: (%s) %s', 'ss_wc_salesautopilot'), $SalesAutopilot->status, $SalesAutopilot->errorMessage));

				return false;
			} else {
				foreach ($retval as $form) {
					$forms[$form['ns_id']] = $form['form_name'];
				}
			}

			return $forms;
		} else {
			return false;
		}
	}

	/**
	 * Send order to SalesAutopilot through API.
	 *
	 * @access public
	 * @param object $order_details
	 * @param integer $listid
	 * @return void
	 */
	public function send_order($order_details, $listid = 0, $formid = 0)
	{

		if ($listid == 0)
			$listid = $this->settings['saplist'];
		if ($formid == 0)
			$formid = $this->settings['form'];

		$SalesAutopilot = new SalesAutopilotAPI($this->settings['api_username'], $this->settings['api_password']);

		/*if ($order_details->order_currency == 'HUF') {
			$decimals = 0;
		} else {*/
		$decimals = 2;
		//}

		$data = array();
		$data['order_id']			= $order_details->get_id();
		$data['email'] 				= $order_details->get_billing_email();
		if ($this->settings['swapnameorder'] == 'yes') {
			$data['mssys_lastname'] 	= $order_details->get_billing_first_name();
			$data['mssys_firstname'] 	= $order_details->get_billing_last_name();
		} else {
			$data['mssys_firstname'] 	= $order_details->get_billing_first_name();
			$data['mssys_lastname']	 	= $order_details->get_billing_last_name();
		}
		$data['mssys_phone']	 	= $order_details->get_billing_phone();
		$data['payment_method'] 	= $order_details->get_payment_method_title();
		$data['payment_code'] 		= $order_details->get_payment_method();
		$data['currency'] 			= $order_details->get_currency();
		$data['mssys_bill_company']	= $order_details->get_billing_company();
		$data['mssys_vat_number']	= $order_details->get_meta('_billing_tax_number');
		$data['mssys_bill_country']	= strtolower($order_details->get_billing_country());
		$data['mssys_bill_state']	= $order_details->get_billing_state();
		$data['mssys_bill_zip']		= $order_details->get_billing_postcode();
		$data['mssys_bill_city']	= $order_details->get_billing_city();
		$data['mssys_bill_address']	= $order_details->get_billing_address_1() . ' ' . $order_details->get_billing_address_2();
		$data['mssys_postal_company']	= $order_details->get_shipping_company();
		$data['mssys_postal_country']	= strtolower($order_details->get_shipping_country());
		$data['mssys_postal_state']		= $order_details->get_shipping_state();
		$data['mssys_postal_zip']		= $order_details->get_shipping_postcode();
		$data['mssys_postal_city']		= $order_details->get_shipping_city();
		$data['mssys_postal_address']	= $order_details->get_shipping_address_1() . ' ' . $order_details->get_shipping_address_2();
		$data['netshippingcost']		= round($order_details->get_total_shipping(), $decimals);
		$data['grossshippingcost']		= round($order_details->get_total_shipping() + $order_details->get_shipping_tax(), $decimals);
		$data['mssys_comment']			= $order_details->get_customer_note();
		$data['order_status_id']        = $order_details->get_status();

		// Add fees like COD
		$extraFees = $order_details->get_fees();
		foreach ($extraFees as $feeData) {
			$data['netshippingcost'] += round($feeData['line_total'], $decimals);
			$data['grossshippingcost'] += round($feeData['line_total'] + $feeData['line_tax'], $decimals);
		}

		foreach ($order_details->get_shipping_methods() as $shipping_item_id => $shipping_item) {
			$data['shipping_method'] .= $shipping_item['name'];
		}

		//Add custom pickup point if set
		$wc_selected_pont = get_post_meta($order_details->get_id(), 'wc_selected_pont', true);

		if ($wc_selected_pont) { //Pont plugin
			$deliveryPointData = explode("|", $wc_selected_pont);

			if ('GLS CsomagPont' == $deliveryPointData[1]) {
				$data['mssys_postal_deliverypoint_type'] = "PSD";
				$data['mssys_postal_deliverypoint_name'] = $deliveryPointData[2];
				$data['mssys_postal_zip'] = explode("-", $data['mssys_postal_deliverypoint_name'])[0];
				$addressData = explode(", ", $deliveryPointData[0]);
				$data['mssys_postal_city'] = ($data['mssys_postal_zip'][0] == '1') ? "Budapest" : $addressData[0];
				$data['mssys_postal_address'] = $addressData[1];
			} else if ('Posta Pont' == $deliveryPointData[1] || 'PostaPont Csomagautomata' == $deliveryPointData[1]) {
				if ('PostaPont Csomagautomata' == $deliveryPointData[1]) {
					$data['mssys_postal_deliverypoint_type'] = "KH_CS";
				} else if (preg_match("/MOL/", $deliveryPointData[0]) || preg_match("/Coop/", $deliveryPointData[0])) {
					$data['mssys_postal_deliverypoint_type'] = "KH_PP";
				} else {
					$data['mssys_postal_deliverypoint_type'] = "KH_PM";
				}

				$data['mssys_postal_deliverypoint_name'] = $deliveryPointData[2];
				$addressData = explode(',', substr(explode("(", $deliveryPointData[0])[0], 0, -1));
				$data['mssys_postal_zip'] = explode(" ", $addressData[0])[0];
				$data['mssys_postal_city'] = explode(" ", $addressData[0])[1];
				$data['mssys_postal_address'] = substr($addressData[1], 1);
			}
		} else if (preg_match("/GLS Csomagpont/i", $data['shipping_method'])) { //Zoneit plugin
			preg_match("/(.*)\(([0-9]{4}-.*)\)/", $data['mssys_postal_address'], $matches);
			$data['mssys_postal_deliverypoint_type'] = "PSD";
			$data['mssys_postal_address']            = $matches[1];
			$data['mssys_postal_deliverypoint_name'] = $matches[2];
		}
		// Add products to the API call
		$products = array();

		if (is_array($order_details->get_items())) {
			foreach ($order_details->get_items() as $item_id => $item) {
				$product    = $item->get_product();
				$categories = get_the_terms($product->get_id(), 'product_cat');

				$bookingData = $this->getBookingData($item_id);

				if (array_key_exists('Start at', $bookingData)) {
					$data['mssys_booking_start_time'] = $bookingData['Start at'];
				}

				if (array_key_exists('Finish at', $bookingData)) {
					$data['mssys_booking_end_time'] = $bookingData['Finish at'];
				}

				$taxPercent = $item->get_subtotal() == 0
					? 0
					: round($item->get_subtotal_tax() / $item->get_subtotal() * 100, 0);

				if ($product->get_sku() != '') {
					$productID = $product->get_sku();
				} else {
					$productID = $product->get_id();
				}

				$prodCategories = array();

				foreach ($categories as $category) {
					array_push($prodCategories, array("category_id" => $category->term_id, "category_name" => $category->name));
				}

				$products[] = array(
					'prod_id'    => $productID,
					'prod_name'  => $item['name'],
					'qty'        => (int)$item['qty'],
					'tax'        => $taxPercent,
					'prod_price' => round($item->get_subtotal() / (int)$item['qty'], $decimals),
					'categories' => $prodCategories
				);
			}
		}

		$couponCodes  = array();
		$couponNetto  = 0;
		$couponBrutto = 0;

		foreach ($order_details->get_items('coupon') as $coupon_item_id => $coupon_item) {
			$products[] = array(
				'prod_id' 		=> "wc_coupon_" . $coupon_item->get_code(),
				'prod_name'		=> $coupon_item->get_code(),
				'qty'			=> 1,
				'tax'			=> $taxPercent,
				'prod_price'	=> '-' . ($coupon_item->get_discount() + $coupon_item->get_discount_tax()) / (1 + $taxPercent / 100)
			);
		}

		$data['products']               = $products;
		$data['mssys_integration_type'] = 'woocommerce';
		$order                          = new WC_Order($order_details->get_id());
		$encData                        = json_encode($data);
		$jsonErrorCode                  = json_last_error();

		if ($jsonErrorCode != JSON_ERROR_NONE) {
			/*
			The above outputs :
			0 JSON_ERROR_NONE
			1 JSON_ERROR_DEPTH
			2 JSON_ERROR_STATE_MISMATCH
			3 JSON_ERROR_CTRL_CHAR
			4 JSON_ERROR_SYNTAX
			5 JSON_ERROR_UTF8
			6 JSON_ERROR_RECURSION
			7 JSON_ERROR_INF_OR_NAN
			8 JSON_ERROR_UNSUPPORTED_TYPE
			*/
			$order->add_order_note(__('WooCommerce to SalesAutopilot data encoding error (JSON): ', 'ss_wc_salesautopilot') . $jsonErrorCode);
		} else {
			$retval = $SalesAutopilot->call('processWebshopOrder/' . $listid . '/ns_id/' . $formid, $data);

			if ($SalesAutopilot->status >= 300) {
				$order->add_order_note(__('WooCommerce to SalesAutopilot API call failed: (', 'ss_wc_salesautopilot') . $SalesAutopilot->status . ')' . $SalesAutopilot->errorMessage);
			}
		}
	}

	/**
	 * Send status update to SalesAutopilot through API.
	 *
	 * @access public
	 * @param $id
	 * @param $new_status
	 * @return void
	 */
	public function update_status($id, $new_status)
	{
		$order                        = new WC_Order($id);
		$listid                       = $this->settings['saplist'];
		$formid                       = $this->settings['other_form'];
		$SalesAutopilot               = new SalesAutopilotAPI($this->settings['api_username'], $this->settings['api_password']);
		$data                         = array();
		$data['mssys_webshop_status'] = $new_status;

		$order->add_order_note('Payment method: ' . $order->get_payment_method() . '  Status: ' . $new_status);

		if (!empty($this->settings['uform']) && 'cod' != $order->get_payment_method() && 'processing' == $new_status) {
			$data['mssys_order_status'] = 2;
			$data['mssys_pay_date']     = date("Y-m-d");
			//Use billing for only when order status gets paid
			$formid                     = $this->settings['uform'];
		}

		$retval = $SalesAutopilot->call('update/' . $listid . '/form/' . $formid . '/field/mssys_shoprenter_id/value/' . $id, $data);

		if ($SalesAutopilot->status >= 300) {
			$order->add_order_note(__('WooCommerce to SalesAutopilot API call failed: (', 'ss_wc_salesautopilot') . $SalesAutopilot->status . ') ' . $SalesAutopilot->errorMessage);
		} else {
			$order->add_order_note(__('Status update sent to SalesAutopilot. New status: ', 'ss_wc_salesautopilot') . $new_status . __(' List: #', 'ss_wc_salesautopilot') . $this->settings['saplist'] . (('cod' != $order->get_payment_method() && 'processing' == $new_status) ? __(' Form: #', 'ss_wc_salesautopilot') . $this->settings['uform'] : ''));
		}
	}

	/**
	 * Helper log function for debugging
	 *
	 * @since 1.2.2
	 */
	static function log($message)
	{
		if (WP_DEBUG === true) {
			if (is_array($message) || is_object($message)) {
				error_log(print_r($message, true));
			} else {
				error_log($message);
			}
		}
	}

	function getBookingData($item_id)
	{
		$htmlData = str_replace("</em>", "", wc_get_order_item_meta($item_id, 'Details'));

		if ('' == $htmlData) {
			return array();
		}

		$dom = new domDocument;
		$dom->loadHTML($htmlData);
		$dom->preserveWhiteSpace = false;

		$spans       = $dom->getElementsByTagName('span');
		$strongs     = $dom->getElementsByTagName('strong');
		$bookingData = array();

		for ($i = 0; $i < $spans->length; $i++) {
			$bookingData[$strongs->item($i)->textContent] = $spans->item($i)->textContent;
		}

		if (array_key_exists('Start at', $bookingData)) {
			$startAt = $bookingData['Start at'];
			$bookingData['Start at'] = $this->getBookingDateTime($bookingData['Check in'], $bookingData['Start at']);

			if (array_key_exists('Finish at', $bookingData)) {
				$bookingData['Finish at'] = $this->getBookingDateTime($bookingData['Check in'], $bookingData['Finish at']);
			} else {
				$startAt  = explode(':', $startAt);
				$finishAt = ((int)($startAt[0]) + 1) . ':' . $startAt[1];
				$bookingData['Finish at'] = $this->getBookingDateTime($bookingData['Check in'], $finishAt);
			}
		} else {
			if (array_key_exists('Check in', $bookingData)) {
				$bookingData['Start at'] = $this->getBookingDateTime($bookingData['Check in']);
			}

			if (array_key_exists('Check out', $bookingData)) {
				$bookingData['Finish at'] = $this->getBookingDateTime($bookingData['Check out']);
			}
		}
		return ($bookingData);
	}

	function getBookingDateTime($dateString, $timeString = 'N/A')
	{
		if ('N/A' == $timeString) {
			$dateTime = DateTime::createFromFormat('M d, Y', $dateString);
			return date_format($dateTime, 'Y-m-d');
		} else {
			$dateTime = DateTime::createFromFormat('M d, Y H:i', $dateString . ' ' . $timeString);
			return date_format($dateTime, 'Y-m-d H:i');
		}
	}
}
