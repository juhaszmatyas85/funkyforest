<?php
/*
 * Plugin Name: HFTT SAP WEBSHOP INTEGRATION
 * Description: Plugin to handle wordpress funky webshop and SAP integration.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'hf_plugin_init' ) ) {
    function hftt_prices_post($prices) {
        $plugin_active = is_plugin_active( 'addify_customer_specific_pricing-1.4.1/addify_customer_specific_pricing.php' );
        $errors = [];
        //$plugin_active = true;
        foreach ($prices as $price){
        	// B2B price post
            if(isset($price['user_id']) && $price['user_id'] != '' && $plugin_active) {
                if($user = get_user_by( 'id', $price['user_id'] )){
	                if($product = wc_get_product( $price['product_id'] )){
		                $product_name = $product->get_name();
		                $post_csp_rules = get_page_by_title($product_name,OBJECT,'csp_rules');

		                // CREATE PRODUCT RULES
		                if(!$post_csp_rules){
			                $new_csp_rules = array(
				                'post_title'   => $product_name,
				                'post_name' => $product_name,
				                'post_type'    => 'csp_rules',
				                'post_status'  => 'publish'
			                );
							$id = wp_insert_post($new_csp_rules);
							update_post_meta($id,'csp_pricing_type','price_specific_to_a_customer');
							update_post_meta($id,'csp_applied_on','products');
			                update_post_meta($id,'csp_applied_on_products',(array($price['product_id'])));
			                $rcus_base_price = array(1=>array("customer_name"=>$price['user_id'],"discount_type"=>"fixed_price","discount_value"=>$price['price'],"min_qty"=>"","max_qty"=>"","start_date"=>"","end_date"=>""));
			                update_post_meta($id,'rcus_base_price',($rcus_base_price));

		                }
		                else{
		                	$products = get_post_meta($post_csp_rules->ID,'rcus_base_price');
			                $product_users = array();
			                $product_keys = array();
		                	foreach ($products[0] as $product_key => $product_val) {
		                		$product_users [] = $product_val['customer_name'];
		                	    $product_keys [$product_val['customer_name']] = $product_key;
			                }
			               	if(in_array($price['user_id'],$product_users)){
			               		$products[0][$product_keys [$price['user_id']]]['discount_value'] = $price['price'];
			               	}
			               	else{
			               		$new_prod_rule = array("customer_name"=>$price['user_id'],"discount_type"=>"fixed_price","discount_value"=>$price['price'],"min_qty"=>"","max_qty"=>"","start_date"=>"","end_date"=>"");
			               		array_push($products[0],$new_prod_rule);
			               	}
			                update_post_meta($post_csp_rules->ID,'rcus_base_price',$products[0]);
		                }
	                }
	                else{
		                $errors[] = "Product not found: ".$price['product_id'];
	                }
                }
                else{
                    $errors[] = "User not found: ".$price['user_id'];
                }

            }
            // B2C price post or B2B regular price
            else if(!isset($price['user_id']) || $price['user_id'] == ''){
                if($product = wc_get_product( $price['product_id'] )){
                    $product->set_regular_price($price['price']);
                    $product->save();
                }
                else{
	                $errors[] = "Product not found: ".$price['product_id'];
                }
            }

        }
	    if(isset($errors) && $errors != null ){
		    $errors = array("errors"=>$errors);
	    }

        return $errors;
    }


    function hftt_partner_post($partners) {
	    $plugin_active = is_plugin_active( 'multiple-shipping-address-woocommerce/oc-woo-multiple-address.php' );
	    $errors = [];
	    $response = [];
        $address_type1_counter = 0;
	    $address_type2_counter = 0;
	    if($plugin_active) {
	    	$user_name = explode(" ",$partners['user_name']);

		    $userdata = array(
			    "user_login"   => $partners['user_name'],
			    "user_email"   => $partners['user_email'],
			    "user_pass"    => wp_generate_password( 8, false ),
			    "first_name"   => $user_name[1],
			    "last_name"    => $user_name[0],
			    "display_name" => $partners['billing_info']['name'],
			    "role"         => "customer"
		    );


		    $user_id  = wp_insert_user( $userdata );
			//$user_id = 1195;
		    if ( is_wp_error( $user_id ) ) {
			    $errors = $user_id->get_error_message();
			    $response[] = array("errors"=>$errors);
			    return $response;
		    }

		    if ( !is_email( $partners['user_email'] ) ) {
			    $response[] = array("errors"=>"Email is not valid");
			    return $response;
		    }

		    $response["user_id"] = strval($user_id);

		    foreach ( $partners['addresses'] as $address ) {
		    	// create billing default address
		    	if($address['id'] == $partners['billing_info']['address_id'] && $address['type'] == 1){
		    		if(isset($partners['billing_info']['name']) && $partners['billing_info']['name'] != ''){
					    update_user_meta( $user_id, 'billing_company', $partners['billing_info']['name'] );
				    }
		    		if(isset($address['city']) && $address['city'] != ''){
					    update_user_meta( $user_id, 'billing_city', $address['city']);
				    }
		    		if(isset($address['postalcode']) && $address['postalcode'] != ''){
					    update_user_meta( $user_id, 'billing_postcode', $address['postalcode'] );
				    }
		    		if(isset($address['country']) && $address['country'] != ''){
					    update_user_meta( $user_id, 'billing_country', $address['country'] );
				    }
				    if(isset($partners['user_email']) && $partners['user_email'] != ''){
					    update_user_meta( $user_id, 'billing_email', $partners['user_email'] );
				    }
				    if(isset($partners['billing_info']['taxnum']) && $partners['billing_info']['taxnum'] != ''){
					    update_user_meta( $user_id, 'billing_tax_number', $partners['billing_info']['taxnum'] );
				    }
				    $billing_address_1 = $address['placename']." ".$address['placetype']." ".$address['number'];
				    if(isset($billing_address_1) && $billing_address_1 != ''){
					    update_user_meta( $user_id, 'billing_address_1', $billing_address_1 );
				    }
                    $billing_address_2 = '';
                    if(isset($address['building']) && $address['building'] != ''){
                        $billing_address_2 .= $address['building']." épület,";
                    }
                    if(isset($address['staircase']) && $address['staircase'] != ''){
                        $billing_address_2 .= $address['staircase']." lépcsőház,";
                    }
                    if(isset($address['floor']) && $address['floor'] != ''){
                        $billing_address_2 .= $address['floor']." emelet,";
                    }
                    if(isset($address['door']) && $address['door'] != ''){
                        $billing_address_2 .= $address['door']." ajtó";
                    }
				    if(isset($billing_address_2) && $billing_address_2 != ''){
					    update_user_meta( $user_id, 'billing_address_2', $billing_address_2);
				    }
				    if(isset($address['name']) && $address['name'] != ''){
					    update_user_meta( $user_id, 'billing_reference_field', $address['name'] );
				    }
				    if(isset($address['note']) && $address['note'] != ''){
					    update_user_meta( $user_id, 'billing_note', $address['note'] );
				    }
				    if(isset($address['id']) && $address['id'] != ''){
					    update_user_meta( $user_id, 'billing_hftt_id', $address['id'] );
				    }

				    $addresses[]=array("address_id"=>strval($address['id']),"wp_id"=>strval($address['id']));
                    $address_type1_counter++;
			    }
		    	// add shipping addresses
		    	else if($address['type'] == 2){
				    global $wpdb;
				    $tablename=$wpdb->prefix.'ocwma_billingadress';
				    $shipping_address_1 = $address['placename']." ".$address['placetype']." ".$address['number'];
                    $shipping_address_2 = '';
                    if(isset($address['building']) && $address['building'] != ''){
                        $shipping_address_2 .= $address['building']." épület,";
                    }
                    if(isset($address['staircase']) && $address['staircase'] != ''){
                        $shipping_address_2 .= $address['staircase']." lépcsőház,";
                    }
                    if(isset($address['floor']) && $address['floor'] != ''){
                        $shipping_address_2 .= $address['floor']." emelet,";
                    }
                    if(isset($address['door']) && $address['door'] != ''){
                        $shipping_address_2 .= $address['door']." ajtó";
                    }
                    $shipping_data = array(
				    	"reference_field"     =>  $address['name'],
					    "shipping_company"    =>  $partners['billing_info']['name'],
					    "shipping_country"    =>  $address['country'],
					    "shipping_city"       =>  $address['city'],
					    "shipping_postcode"   =>  $address['postalcode'],
					    "shipping_state"      =>  "",
					    "shipping_address_1"  =>  $shipping_address_1,
					    "shipping_address_2"  =>  $shipping_address_2,
					    "shipping_first_name" =>  $user_name[1],
					    "shipping_last_name"  =>  $user_name[0]

				    );
				    $shipping_data_serlized=serialize( $shipping_data );
				    $wpdb->insert($tablename,array('userid'=>$user_id,'userdata' => $shipping_data_serlized,'type'=>'shipping'));
				    $ship_id = $wpdb->insert_id;
				    $addresses[]=array("address_id"=>$address['id'],"wp_id"=>strval($ship_id));
                    $address_type2_counter++;
			    }
		    }
		    $response["addresses"]  = $addresses;
            if($address_type1_counter == 0 ){
                $errors[] = "Missing address type 1";
            }
		    if($address_type2_counter == 0 ){
                $errors[] = "Missing address type 2";
            }
	    }
	    else{
		    $errors[] = "This service is not available on this site";
	    }

	    if(isset($errors) && $errors != null ){
		    $response["errors"] = $errors;
	    }

        return $response;
    }

	function hftt_partner_put($partners) {
		$plugin_active = is_plugin_active( 'multiple-shipping-address-woocommerce/oc-woo-multiple-address.php' );
		$errors = [];
		$response = [];
		global $wpdb;
		$tablename=$wpdb->prefix.'ocwma_billingadress';
		if(isset($partners['wp_id']) && $partners['wp_id'] == ''){
			$errors["errors"] = "The wordpress user_id is not specified";
			return $errors;
		}
		if($plugin_active) {
			if($user = get_user_by( 'id', $partners['wp_id'] )){
				$user_name = explode(" ",$partners['user_name']);
				$userdata = array(
					"ID"            => $partners['wp_id'],
					"user_login"    => $partners['user_name'],
					"user_email"    => $partners['user_email'],
					"first_name"    => $user_name[1],
					"last_name"     => $user_name[0],
					"display_name"  => $partners['billing_info']['name'],
					"role"          => "customer"
				);



                $user_login = $wpdb->update($wpdb->users, array('user_login' => $partners['user_name']), array('ID' => $partners['wp_id']));
                if ( is_wp_error( $user_login ) ) {
                    $errors = $user_login->get_error_message();
                    $response[] = array("errors"=>$errors);
                    return $response;
                }

				$user_id  = wp_update_user( $userdata );

				if ( is_wp_error( $user_id ) ) {
					$errors = $user_id->get_error_message();
					$response[] = array("errors"=>$errors);
					return $response;
				}

				if ( !is_email( $partners['user_email'] ) ) {
					$response[] = array("errors"=>"Email is not valid");
					return $response;
				}

				//$response["user_id"] =  strval($partners['wp_id']);
				$result_shipping_addresses_before_actions = $wpdb->get_results( "SELECT * FROM ".$tablename." WHERE userid = '". $partners['wp_id']. "' AND type='shipping' " );
				if ( $wpdb->last_error ) {
					$errors[] = $wpdb->last_error;
				}
				foreach ( $result_shipping_addresses_before_actions as $key ) {
					$shipping_addresses_keys[] = $key->id;
				}

				foreach ( $partners['addresses'] as $address ) {
					// update billing default address
					if($address['id'] == $partners['billing_info']['address_id'] && $address['type'] == 1){
						if(isset($partners['billing_info']['name']) && $partners['billing_info']['name'] != ''){
							update_user_meta( $user_id, 'billing_company', $partners['billing_info']['name'] );
						}
						if(isset($address['city']) && $address['city'] != ''){
							update_user_meta( $user_id, 'billing_city', $address['city']);
						}
						if(isset($address['postalcode']) && $address['postalcode'] != ''){
							update_user_meta( $user_id, 'billing_postcode', $address['postalcode'] );
						}
						if(isset($address['country']) && $address['country'] != ''){
							update_user_meta( $user_id, 'billing_country', $address['country'] );
						}
						if(isset($partners['user_email']) && $partners['user_email'] != ''){
							update_user_meta( $user_id, 'billing_email', $partners['user_email'] );
						}
						if(isset($partners['billing_info']['taxnum']) && $partners['billing_info']['taxnum'] != ''){
							update_user_meta( $user_id, 'billing_tax_number', $partners['billing_info']['taxnum'] );
						}
						$billing_address_1 = $address['placename']." ".$address['placetype']." ".$address['number'];
						if(isset($billing_address_1) && $billing_address_1 != ''){
							update_user_meta( $user_id, 'billing_address_1', $billing_address_1 );
						}
                        $billing_address_2 = '';
                        if(isset($address['building']) && $address['building'] != ''){
                            $billing_address_2 .= $address['building']." épület,";
                        }
                        if(isset($address['staircase']) && $address['staircase'] != ''){
                            $billing_address_2 .= $address['staircase']." lépcsőház,";
                        }
                        if(isset($address['floor']) && $address['floor'] != ''){
                            $billing_address_2 .= $address['floor']." emelet,";
                        }
                        if(isset($address['door']) && $address['door'] != ''){
                            $billing_address_2 .= $address['door']." ajtó";
                        }
                        if(isset($billing_address_2) && $billing_address_2 != ''){
                            update_user_meta( $user_id, 'billing_address_2', $billing_address_2);
                        }
						if(isset($address['name']) && $address['name'] != ''){
							update_user_meta( $user_id, 'billing_reference_field', $address['name'] );
						}
						if(isset($address['note']) && $address['note'] != ''){
							update_user_meta( $user_id, 'billing_note', $address['note'] );
						}
						if(isset($address['id']) && $address['id'] != ''){
							update_user_meta( $user_id, 'billing_hftt_id', $address['id'] );
						}

						//$addresses[]=array("address_id"=>$address['id'],"wp_id"=>$address['id']);
					}
					// add or update shipping addresses
					else if($address['type'] == 2){

						$shipping_address_1 = $address['placename']." ".$address['placetype']." ".$address['number'];
                        $shipping_address_2 = '';
                        if(isset($address['building']) && $address['building'] != ''){
                            $shipping_address_2 .= $address['building']." épület,";
                        }
                        if(isset($address['staircase']) && $address['staircase'] != ''){
                            $shipping_address_2 .= $address['staircase']." lépcsőház,";
                        }
                        if(isset($address['floor']) && $address['floor'] != ''){
                            $shipping_address_2 .= $address['floor']." emelet,";
                        }
                        if(isset($address['door']) && $address['door'] != ''){
                            $shipping_address_2 .= $address['door']." ajtó";
                        }
                        $shipping_data = array(
							"reference_field"       =>  $address['name'],
							"shipping_company"      =>  $partners['billing_info']['name'],
							"shipping_country"      =>  $address['country'],
							"shipping_city"         =>  $address['city'],
							"shipping_postcode"     =>  $address['postalcode'],
							"shipping_state"        =>  "",
							"shipping_address_1"    =>  $shipping_address_1,
							"shipping_address_2"    =>  $shipping_address_2,
							"shipping_first_name"   =>  $user_name[1],
							"shipping_last_name"    =>  $user_name[0]

						);
						$shipping_data_serlized=serialize( $shipping_data );
						if(isset($address['wp_id']) && $address['wp_id'] != ''){
							if(isset($result_shipping_addresses_before_actions) && count($result_shipping_addresses_before_actions) != 0) {
								if ( in_array($address['wp_id'],$shipping_addresses_keys) ) {
										$address_shipping_ids[] = $address['wp_id'];
										$update_address         = $wpdb->update( $tablename, array(
											'userdata' => $shipping_data_serlized,
										), array(
												'ID' => strval( $address['wp_id'] )
											)
										);

										if ( $wpdb->last_error ) {
											$errors[] = $wpdb->last_error;
										}

										if ( $update_address === "FALSE" ) {
											$errors[] = "Address updated FAIL: " . $address['wp_id'];
										}


								}
								else {
										$error_msg = "The address does not belong to the user: " . $address['wp_id'];
										if ( ! in_array( $error_msg, $errors ) ) {
											$errors[] = $error_msg;
										}
								}
							}
							else{
								$error_msg = "The address does not belong to the user: " . $address['wp_id'];
								if ( ! in_array( $error_msg, $errors ) ) {
									$errors[] = $error_msg;
								}
							}


						}
						else {
							$wpdb->insert( $tablename, array( 'userid'   => $user_id,
							                                  'userdata' => $shipping_data_serlized,
							                                  'type'     => 'shipping'
							) );
							$ship_id     = $wpdb->insert_id;
							$address_shipping_ids[] = $ship_id;

							if($ship_id) {
								$addresses[] = array( "address_id" => strval($address['id']), "wp_id" => strval($ship_id));
							}
						}
						if ( $wpdb->last_error ) {
							$errors[] = $wpdb->last_error;
						}

					}
				}

				// delete shipping address(es)
				$result_shipping_addresses = $wpdb->get_results( "SELECT * FROM ".$tablename." WHERE userid = '". $partners['wp_id']. "' AND type='shipping' " );
				if ( $wpdb->last_error ) {
					$errors[] = $wpdb->last_error;
				}

				foreach($result_shipping_addresses as $key){
					if(!in_array($key->id,$address_shipping_ids)){
						$wpdb->delete($tablename,array('ID'=>$key->id));
						if ( $wpdb->last_error ) {
							$errors[] = $wpdb->last_error;
						}
					}
				}
				
				if(isset($addresses) && $addresses != null && $errors == null ) {
					$response = $addresses;
				}
				elseif (isset($errors) && $errors != null ){
					$response[] = $addresses;
				}
			}
			else{
					$errors[] = "User not found: ".$partners['wp_id'];
			}
		}
		else{
			$errors[] = "This service is not available on this site";
		}

		if(isset($errors) && $errors != null ){
			$response["errors"] = $errors;
		}

		return $response;

	}

    function hftt_tracking_post($trackings) {

	    $errors = [];
	    $response = [];

	    if(isset($trackings['order_id']) && $trackings['order_id'] != '') {
		    if($order = wc_get_order($trackings['order_id'])){
		    	/*
		    	    1: UNDER_PROCESSING: a rendelés feldolgozás alatt, a rendelés összekészítése folyamatban van.
					2: PACKAGE_SENT: a rendelés átadásra került a futárcégnek
					3: DELIVERY_IN_PROGRESS: a futár elkezdte a csomag kiszállítását.
					4: DELIVERY_SUCCESS: kiszállítás sikeresen lezárult
					5: DELIVERY_FAILED: kiszállítás sikertelen
		    	*/
			    $order_status_array = array(
			        1 => "processing",
				    2 => "package_sent",
				    3 => "delivery_in_progress",
				    4 => "delivery_success",
				    5 => "delivery_failed"
			    );
			    $order_status = $order_status_array[$trackings['status']];
			    update_post_meta($trackings['order_id'], 'htff_megjegyzes',$trackings['note'] );
			    update_post_meta($trackings['order_id'], 'htff_status',$trackings['status'] );
			    update_post_meta($trackings['order_id'], 'htff_modified_date',$trackings['event_ts'] );
			    //$order->update_status($order_status);
			    //$order->set_date_modified($trackings['event_ts']);
			    //$order->save();
		    }
		    else{
			    $errors[] = "Order not found: ".$trackings['order_id'];
		    }
	    }
	    else{
		    $errors[] = "Order not specified: ".$trackings['order_id'];
	    }

	    if(isset($errors) && $errors != null ){
		    $response["errors"] = $errors;
	    }
	    return $response;
    }

    function hf_plugin_init() {

    	add_filter('v_forcelogin_bypass',function (bool $whether, string $url){
    		return false !== strpos($url,'/hftt/v1/') ? true : $whether;
	    },10,2);
        add_rewrite_rule(
            'hftt\/v1\/prices\/?$',
            'index.php?hftt=1&hftt_task=prices',
            'top'
        );

        add_rewrite_rule(
            'hftt\/v1\/partner\/?$',
            'index.php?hftt=1&hftt_task=partner',
            'top'
        );

        add_rewrite_rule(
            'hftt\/v1\/tracking\/?$',
            'index.php?hftt=1&hftt_task=tracking',
            'top'
        );

	    add_rewrite_rule(
		    'hftt\/v1\/resend_order\/([0-9]+)[\/]?$',
		    'index.php?hftt=1&hftt_task=resend_order&hftt_orderid=$matches[1]',
		    'top'
	    );

        add_filter('query_vars', function($query_vars) {
            $query_vars[] = 'hftt';
            $query_vars[] = 'hftt_task';
	        $query_vars[] = 'hftt_orderid';

            return $query_vars;
        });

        add_action('hf_send_order_hook', function($order_id,$response_times){
	        $plugin_active = is_plugin_active( 'multiple-shipping-address-woocommerce/oc-woo-multiple-address.php' );
	        $order = wc_get_order($order_id);
	        $order_note = strval($order->get_customer_note());
	        $items = $order->get_items();
	        $payment_method = get_post_meta($order_id,'_payment_method');

	        // B2B order
	        if($plugin_active){
	        	$x_api_key = 'b65f2c59-c5b9-4837-abe0-b54934c929b0';
                //$url = 'https://bckWebshopIntegration-prod.cfapps.eu10-004.hana.ondemand.com/webshopintegration.svc/order/v1/b2bs';
	        	$url = 'https://bckWebshopIntegration-qa.cfapps.eu10-004.hana.ondemand.com/webshopintegration.svc/order/v1/b2bs';
                //$url = 'https://bckWebshopIntegration-dev.cfapps.us10.hana.ondemand.com/webshopintegration.svc/order/v1/b2bs';
		        $order_shipping_id = get_post_meta($order_id,'shipping_hftt_id');
		        foreach ( $items as $item_id => $item ) {
			        $product_id = strval($item->get_product_id());
			        $woosb_ids = get_post_meta($product_id,'woosb_ids');
			        $quantity = $item->get_quantity();
                    $terms = get_the_terms($product_id, 'product_cat');
                    foreach ($terms as $term) {
                        $product_cat = $term->slug;
                    }

			        if((isset($woosb_ids) && $woosb_ids != null) || $product_cat == "3l_bib" ){
				        $item_woosb[] = array("product" => array("product_id"=>$product_id,"quantity"=>intval($quantity)));
			        }
		        }
		        $request = [
			        'wp_id'                 => strval($order->get_id()),
			        'pub_id'                => strval('#P'.$order->get_id()),
			        'user_id'               => strval($order->get_user_id()),
			        'delivery_address_id'   => $order_shipping_id[0],
			        'items'                 => $item_woosb,
			        'note'                  => $order_note
		        ];

		        //error_log(print_r(json_encode($request),true));

	        }
	        // B2C order
	        else{
		        $x_api_key = 'b65f2c59-c5b9-4837-abe0-b54934c929b0';
		        //$url = 'https://bckWebshopIntegration-prod.cfapps.eu10-004.hana.ondemand.com/webshopintegration.svc/order/v1/b2cs';
                $url = 'https://bckWebshopIntegration-qa.cfapps.eu10-004.hana.ondemand.com/webshopintegration.svc/order/v1/b2cs';
		        //$url = 'https://bckWebshopIntegration-dev.cfapps.us10.hana.ondemand.com/webshopintegration.svc/order/v1/b2cs';
		        //$url = 'https://bckWebshopIntegration-dev.com/webshopintegration.svc/order/v1/b2cs';
		        $barion_payment_id = get_post_meta($order_id,'Barion paymentId');
                $selected_pont = get_post_meta($order_id,'wc_selected_pont');

		        foreach ( $items as $item_id => $item ) {
			        $product_id = strval($item->get_product_id());
			        $woosb_ids = get_post_meta($product_id,'woosb_ids');
			        $woosb_item_ids = wc_get_order_item_meta($item_id,'_woosb_ids');
			        $woosb_item_parent_id = wc_get_order_item_meta($item_id,'_woosb_parent_id');
			        $quantity = $item->get_quantity();
                    $details = array();
			        if(isset($woosb_ids) && $woosb_ids != null){
				        $items_qtys = explode(",",$woosb_item_ids);
				        foreach($items_qtys as $item_qty ){
					        list($woosb_item_id,$woosb_item_qty) = explode("/",$item_qty);
					        $details[] = array("product_id"=>$woosb_item_id,"quantity"=>intval($woosb_item_qty));
				        }
				        $items_orderline[] = array("product" => array("product_id"=>$product_id,"quantity"=>$quantity),"details" => $details );
			        }
			        else if($woosb_item_parent_id == ''){
				        $items_orderline[] = array("product" => array("product_id"=>$product_id,"quantity"=>$quantity));
			        }
		        }
		        $user_id = strval($order->get_user_id());
		        $request['wp_id'] = strval($order->get_id());
		        $request['pub_id'] = strval('#'.$order->get_id());
		        if(isset($barion_payment_id[0]) && $barion_payment_id[0] != ''){
			        $request['payment_id'] = strval($barion_payment_id[0]);
		        }
		        else{
			        $request['payment_id'] = null;
		        }


		        if(isset($user_id) && $user_id != 0){
			        $request["user_id"] = $user_id;
		        }
		        $billing_country = $order->get_billing_country();
		        $billing_postcode = $order->get_billing_postcode();
		        $billing_city = $order->get_billing_city();
		        $billing_placename = get_post_meta($order_id, '_billing_placename');
		        $billing_placetype = get_post_meta($order_id, '_billing_placetype');
		        $billing_number = get_post_meta($order_id, '_billing_number');
		        $billing_building = get_post_meta($order_id, '_billing_building');
		        $billing_staircase = get_post_meta($order_id, '_billing_staircase');
		        $billing_floor = get_post_meta($order_id, '_billing_floor');
		        $billing_door = get_post_meta($order_id, '_billing_door');
		        $billing_company = $order->get_billing_company();
		        $billing_email = $order->get_billing_email();
		        $billing_phone = $order->get_billing_phone();
		        
		        $billing_name = $order->get_billing_last_name() ." ". $order->get_billing_first_name();
		        $billing_tax_number = get_post_meta($order_id, '_billing_tax_number');
		        
		        $billing_address_in["type"] = 1;
		        
		        if(isset($billing_country) && $billing_country != ''){
			        $billing_address_in["country"] = $billing_country;
		        }
		        if(isset($billing_postcode) && $billing_postcode != ''){
					$billing_address_in["postalcode"] = $billing_postcode;
		        }
		        if(isset($billing_city) && $billing_city != ''){
			        $billing_address_in["city"] = $billing_city;
		        }
		        if(isset($billing_placename[0]) && $billing_placename[0] != ''){
			        $billing_address_in["placename"] = $billing_placename[0];
		        }
		        if(isset($billing_placetype[0]) && $billing_placetype[0] != ''){
			        $billing_address_in["placetype"] = $billing_placetype[0];
		        }
		        if(isset($billing_number[0]) && $billing_number[0] != ''){
			        $billing_address_in["number"] = $billing_number[0];
		        }
		        if(isset($billing_building[0]) && $billing_building[0] != ''){
			        $billing_address_in["building"] = $billing_building[0];
		        }
		        if(isset($billing_staircase[0]) && $billing_staircase[0] != ''){
			        $billing_address_in["staircase"] = $billing_staircase[0];
		        }
		        if(isset($billing_floor[0]) && $billing_floor[0] != ''){
			        $billing_address_in["floor"] = $billing_floor[0];
		        }
		        if(isset($billing_door[0]) && $billing_door[0] != ''){
			        $billing_address_in["door"] = $billing_door[0];
		        }
		        if(isset($billing_company) && $billing_company != ''){
			        $billing_info["name"] = $billing_company;
		        }
		        else{
			        $billing_info["name"] = $billing_name;
		        }
		        if(isset($billing_name) && $billing_name != ''){
			        $request["user_name"] = strval($billing_name);
		        }
		        if(isset($billing_email) && $billing_email != ''){
			        $request["user_email"] = strval($billing_email);
		        }
		        if(isset($billing_phone) && $billing_phone != ''){
			        $request["user_phone"] = strval($billing_phone);
		        }
		        if(isset($billing_tax_number[0]) && $billing_tax_number[0] != ''){
			        $billing_info["taxnum"] = $billing_tax_number[0];
		        }
		        
		        $shipping_country = $order->get_shipping_country();
		        $shipping_postcode = $order->get_shipping_postcode();
		        $shipping_city = $order->get_shipping_city();
		        $shipping_placename = get_post_meta($order_id, '_shipping_placename');
		        $shipping_placetype = get_post_meta($order_id, '_shipping_placetype');
		        $shipping_number = get_post_meta($order_id, '_shipping_number');
		        $shipping_building = get_post_meta($order_id, '_shipping_building');
		        $shipping_staircase = get_post_meta($order_id, '_shipping_staircase');
		        $shipping_floor = get_post_meta($order_id, '_shipping_floor');
		        $shipping_door = get_post_meta($order_id, '_shipping_door');
		        //$shipping_company = $order->get_shipping_company();

		        $shipping_address_in["type"]  = 2;
		        
		        if(isset($shipping_country) && $shipping_country != ''){
			        $shipping_address_in["country"] =  $shipping_country;
		        }
		        if(isset($shipping_postcode) && $shipping_postcode != ''){
			        $shipping_address_in["postalcode"] =  $shipping_postcode;
		        }
		        if(isset($shipping_city) && $shipping_city != ''){
			        $shipping_address_in["city"] =  $shipping_city;
		        }
		        if(isset($shipping_placename[0]) && $shipping_placename[0] != ''){
			        $shipping_address_in["placename"] = $shipping_placename[0];
		        }
		        if(isset($shipping_placetype[0]) && $shipping_placetype[0] != ''){
			        $shipping_address_in["placetype"] = $shipping_placetype[0];
		        }
		        if(isset($shipping_number[0]) && $shipping_number[0] != ''){
			        $shipping_address_in["number"] = $shipping_number[0];
		        }
		        if(isset($shipping_building[0]) && $shipping_building[0] != ''){
			        $shipping_address_in["building"] = $shipping_building[0];
		        }
		        if(isset($shipping_staircase[0]) && $shipping_staircase[0] != ''){
			        $shipping_address_in["staircase"] = $shipping_staircase[0];
		        }
		        if(isset($shipping_floor[0]) && $shipping_floor[0] != ''){
			        $shipping_address_in["floor"] = $shipping_floor[0];
		        }
		        if(isset($shipping_door[0]) && $shipping_door[0] != ''){
			        $shipping_address_in["door"] = $shipping_door[0];
		        }
		        if(isset($shipping_company) && $shipping_company != ''){
			        $shipping_address_in["company"] = $shipping_company;
		        }

                if(isset($selected_pont[0]) && $selected_pont[0] != 0){
                    $pontMeta = $selected_pont_id = explode('|',$selected_pont[0]);
                    $request['pick_pack_pont_id'] = strval($selected_pont_id[2]);
                    $jsonfile 	= WC_Pont::$plugin_path ."pickpont.json";
                    if ( file_exists( $jsonfile ) ){
                        $p = json_decode( file_get_contents( $jsonfile ),true );
                    }
                    if ( $p ) {
                        $id = array_search($pontMeta[2], array_column($p, 'id'));
                        $p = (object)$p[$id];
                        $shipping_address_in["name"] = $p->name;
                        $shipping_address_in["note"] = $p->address;
                    }
                }
                else{
                    $request['pick_pack_pont_id'] = null;
                }

		        $billing_info["address"] = $billing_address_in;


		        $request['billing_info']         = $billing_info;
		        $request['delivery_address']     = $shipping_address_in;
		        $request['items']                = $items_orderline;
		        $request['note']                 = $order_note;
		        //error_log(print_r(json_encode($request),true));

		        /*if(isset($payment_method[0]) && $payment_method[0] == "barion"  ) {
			        $allmails = WC()->mailer()->emails;
			        $email    = $allmails['WC_Email_Customer_Completed_Order'];
                    $email->enabled ='yes';
                    $email->trigger( $order_id );
		        }*/
	        }

            // request küldése
            $response = wp_remote_post(
                $url,
                [   'method' => 'POST',
                    'timeout' => 30,
                    'headers' => [ 'X-API-Key' => $x_api_key, 'Content-type' => 'application/json' ],
                    'body' => json_encode($request)
                ]
            );


	        // hiba feldolgozása
            if ($response instanceof \WP_Error) {
                error_log('Hiba, megrendelés: '.$order_id. 'Hiba oka: '.$response->get_error_message());

	            $response_times++;

                // újra próbálkozás hiba esetén
	            if($response_times <= 5) {
		            wp_schedule_single_event( time() + 3, 'hf_send_order_hook', array( $order_id, $response_times ) );
	            }
	            else{
		            // email értessítő hiba esetén
		            if(isset($payment_method[0]) && $payment_method[0] == 'barion' && !$plugin_active) {
			            $allmails = WC()->mailer()->emails;
			            $email    = $allmails['WC_Email_Customer_Completed_Order'];
                        $email->enabled ='yes';
			            $email->trigger( $order_id );
		            }
                    if($plugin_active){
                        $system_type = "B2B";
                    }
                    else{
                        $system_type = "B2C";
                    }

		            wp_mail(
			            'benda@appsters.me',
			            'Hiba a megrendelés beküldése során - '.$system_type,
			            'Hiba, megrendelés: '.$order_id. "\n".'Hiba oka: '.$response->get_error_message()
		            );


	            }
            }

	        $root_path = get_home_path();
	        $code = wp_remote_retrieve_response_code( $response );
	        $headers_data = wp_remote_retrieve_headers($response);
	        $header_message = wp_remote_retrieve_response_message($response);
	        date_default_timezone_set('Europe/Budapest');
	        error_log(print_r("-----Order-ID:".$order_id."-----DATE:".date('Y-m-d H:i:s'). PHP_EOL,true),3,$root_path.'/wp-content/hftt_error_log.log');
	        error_log(print_r("-----REQUEST----". PHP_EOL,true),3,$root_path.'/wp-content/hftt_error_log.log');
	        error_log(print_r(json_encode($request),true). PHP_EOL,3,$root_path.'/wp-content/hftt_error_log.log');
	        error_log(print_r("-----RESPONSE----". PHP_EOL,true),3,$root_path.'/wp-content/hftt_error_log.log');
	        error_log(print_r(json_encode($response),true). PHP_EOL,3,$root_path.'/wp-content/hftt_error_log.log');
	        error_log(print_r("Response-code: ".$code." HFTT-error-code: ".$headers_data['errorcode']." HFTT-error-message: ".$header_message. PHP_EOL,true),3,$root_path.'/wp-content/hftt_error_log.log');

	        //error_log(print_r($response_times,true));

            // response feldolgozása

        }, 10,2);

	    // woocommerce_admin_order_data_after_order_details: egy gombot szeretnénk a megrendelés újra küldéséhez

	    add_action( 'woocommerce_admin_order_data_after_order_details', function ($order){

		    echo '</div><div class="order_data_column">
    		<h3>' . esc_html__( 'Rendelés Újraküldése HFTT Rendszerbe', 'woocommerce' ) . '</h3>';

		    $order_id = $order->get_id();
		    $current_url = home_url( '/hftt/v1/resend_order/'.$order_id );
		    echo '<p><button id="order_resend_id" class="button button-primary" data-order-url="'.$current_url.'" data-order-id="'.$order_id.'" type="button">Újraküldés</button></p>';

	    }, 10,1 );

        // megrendelés státuszának a vizsgálata
        add_action( 'woocommerce_order_status_changed', function($order_id, $from, $to, $order) {
            if ( 'completed' === $to
                && $order->get_date_created()
                && $order->get_date_modified()
                //&& $order->get_date_created()->getTimestamp() === $order->get_date_modified()->getTimestamp()
            ) {
	            $response_times = 0;
                // háttér task létrehozása
                wp_schedule_single_event(time() + 60, 'hf_send_order_hook', array( $order_id, $response_times ));
            }
            else{
				$root_path = get_home_path();
			date_default_timezone_set('Europe/Budapest');
	        error_log(print_r("-----Order-ID:".$order_id."-----DATE:".date('Y-m-d H:i:s'). PHP_EOL,true),3,$root_path.'/wp-content/hftt_sending_log.log');
			error_log(print_r("-----From:".$from. PHP_EOL,true),3,$root_path.'/wp-content/hftt_sending_log.log');
			error_log(print_r("-----To:".$to. PHP_EOL,true),3,$root_path.'/wp-content/hftt_sending_log.log');
			error_log(print_r("-----Order:".$order. PHP_EOL,true),3,$root_path.'/wp-content/hftt_sending_log.log');
			}

        }, 10, 4 );



        add_action('template_redirect', function() {
            if (0 === intval(get_query_var('hftt', 0))) {
                return;
            }
            $hftt_task = get_query_var('hftt_task', '');

	       if ($hftt_task == "resend_order" ) {
		        // request feldolgozása

		        $order_id = get_query_var('hftt_orderid', '');
		        $response_times = 0;
		        // háttér task létrehozása
		        wp_schedule_single_event(time() + 3, 'hf_send_order_hook', array( $order_id, $response_times ));

		        die();
	        }
                // authentikáció
                $body = file_get_contents( 'php://input' );
                $body = @json_decode($body, true);

                $headers = getallheaders();
                if (isset($headers['x-api-key']) && $headers['x-api-key'] != '123') {
                    http_response_code(403);
                    $response = 'X-API-Key nem megfelelo!';
                    echo json_encode($response);
                    $response_code = 'HTTP/1.0 '.http_response_code();
                    header($response_code);
                    die();
                };
                if (!isset($headers['x-api-key']) || $headers['x-api-key'] === '') {
                    http_response_code(404);
                    $response = 'X-API-Key nincs megadva!';
                    echo json_encode($response);
                    $response_code = 'HTTP/1.0 '.http_response_code();
                    header($response_code);
                    die();
                };


            if ($hftt_task == "prices" ) {
                // request feldolgozása
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST' :
                        $response = hftt_prices_post($body);
                        break;
                }

                // response adás
	            if(isset($response) && $response != null ){
		            http_response_code(500);
		            echo json_encode($response);
	            }
                else{
	                http_response_code(200);
                }
                $response_code = 'HTTP/1.0 '.http_response_code();
                header($response_code);
                die();
            }
            if ($hftt_task == "partner" ) {
                // request feldolgozása
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST' :
                        $response = hftt_partner_post($body);
                        break;
                    case 'PUT'  :
                        $response = hftt_partner_put($body);
                        break;
                }
	            // response adás
               // $ake = array_key_exists('errors', $response);
	            if (array_key_exists('errors', $response[0])) {
		            http_response_code(500);
	            }
				else{
                     http_response_code(200);
				}
                $response_code = 'HTTP/1.0 '.http_response_code();
                echo json_encode($response);
                header($response_code);
                die();
            }

            if ($hftt_task == "tracking" ) {
                // request feldolgozása
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST' :
                        $response = hftt_tracking_post($body);
                        break;
                }
	            if(isset($response) && $response != null ){
		            http_response_code(500);
		            echo json_encode($response);
	            }
	            else{
		            http_response_code(200);
	            }
                $response_code = 'HTTP/1.0 '.http_response_code();
                header($response_code);
                die();
            }
        });
    }

    add_action('init', 'hf_plugin_init');
}
