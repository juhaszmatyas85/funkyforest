=== SalesAutopilot for WooCommerce ===
Contributors: gykhauth
Tags: woocommerce
Requires at least: 4.0.1
Tested up to: 6.1.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

You can save WooCommerce orders  directly to SalesAutopilot eCommerce with this plugin.

== Description ==

Extend WooCommerce functionality with marketing and sales automation.
With this plugin installed when an orders takes place in WooCommerce it will automatically
save into the selected SalesAutopilot list.

If you need support contact us at https://www.salesautopilot.com/contact-us
Feedback is also welcome!

== Installation ==

1. Upload or extract the `woocommerce-salesautopilot` folder to your site's `/wp-content/plugins/` directory. You can also use the *Add new* option found in the *Plugins* menu in WordPress.
2. Enable the plugin from the *Plugins* menu in WordPress.

== Usage ==

See illustrated help here: http://www.salesautopilot.com/knowledge-base/ecommerce/woocommerce-integration

- In the left menu click to WooCommerce / Settings option. Then click to Integration tab.
- Enter your SalesAutopilot API username and password (more about how to get your API username/password: http://www.salesautopilot.com/knowledge-base/api/api-key-pairs).
- Click to Save changes button at the bottom. The you can see your eCommerce lists in the first dropdown list.
- Select the one you want WooCommerce integrate with.
- Click again to the Save changes button, then select the Order form you want the WooCommerce connect with.
- Then click to the Save changes button again.
- If you want status change to be reported to SalesAutopilot, type an update form id into the "Update Form" field.
- If you want to sign up customers to newsletter check "Display subscription form" checkbox and select a newsletter list and form.

You've done the settings. From now when an order takes place in your WooCommerce webshop it will be saved to the selected SalesAutopilot list.

== Screenshots ==

1. WooCommerce SalesAutopilot options screen.

== Changelog ==

= 1.4.6 =
*Release Date - March 24 2023*

* Fix Zoneit plugin shipping name

= 1.4.5 =
*Release Date - Nov 4 2022*

* Save newsletter signup state to order

= 1.4.4 =
*Release Date - Nov 3 2022*

* Fix newsletter singup checkbox placing

= 1.4.3 =
*Release Date - Sept 25 2022*

* Fix deprecated Woo function calls

= 1.4.2 =
*Release Date - May 17 2022*

* Tested Wordpress 6

= 1.4.1 =
*Release Date - 19 July 2021*

* Fix error reporting issue

= 1.4.0 =
*Release Date - 30 June 2020*

* Add Hucommerce TAX number to fields send to SalesAutopilot

= 1.3.8 =
*Release Date - 28 February 2019*

* Bug fixed - Now can save integration settings in newer version of woocommerce

= 1.3.7 =
*Release Date - 23 January 2019*

* Bug fixed - Small fix for javascript event in woocommerce integration page

= 1.3.6 =
*Release Date - 23 January 2019*

* Bug fixed - Cannot save options in woocommerce integration page

= 1.3.5 =
*Release Date - 14 January 2019*

* Bug fixed - Salesautopilot plugin and szamlazz.hu plugin woocommerce integration pages can now work together

= 1.3.4 =
*Release Date - 2 August 2018*

* Clear PHP Notices in debug mode

= 1.3.3 =
*Release Date - 3 April 2018*

* Bug fixed - division by zero appeared when someone ordered a zero priced product

= 1.3.1 =
*Release Date - 14 February 2018*

* New status callback to handle status changes except the order status change.

= 1.3.0 =
*Release Date - 14 December 2017*

* Order change callback
* Pinpoint integration

= 1.2.1 =
*Release Date - 24 November 2017*

* Handle MPL and GLS package point

= 1.2.0 =
*Release Date - 27 October 2017*

* Handle coupons as products
* New option: update form for create invoice when order status changed to paid.

= 1.1.9 =
*Release Date - 10 October 2017*

* Firstname, lastname switch option (to fix Hungarian translation inconsistency)

= 1.1.8 =
*Release Date - 14 September 2017*

* Use Pont Shipping For Woocommerce plugin if available.

= 1.1.7 =
*Release Date - 5 September 2017*

* Fix newsletter opt-in bug.

= 1.1.6 =
*Release Date - 5 September 2017*

* Fix more Hungarian translation issues (firstname, lastname switch).

= 1.1.5 =
*Release Date - 4 September 2017*

* Fix Hungarian translation issues (firstname, lastname switch).

= 1.1.4 =
*Release Date - 25 June 2017*

* JSON encoding check.

= 1.1.3 =
*Release Date - 7 June 2017*

* Revoke last patch.

= 1.1.2 =
*Release Date - 6 June 2017*

* Fix unnecessary API calls.

= 1.1.1 =
*Release Date - 2 June 2017*

* Complete translation

= 1.1.0 =
*Release Date - 31 May 2017*

* Fix status change issues

= 1.0.10 =
*Release Date - 23 May 2017*

* Fix add order with custom status

= 1.0.9 =
*Release Date - 16 May 2017*

* Add sing-up to newsletter option
* "Add order when status changed to" option

= 1.0.8 =
* Remove HUF round

= 1.0.7 =
* Fix HUF and VAT round

= 1.0.6 =
* Change product item round

= 1.0.5 =
* Remove of extra coupon handling because of default function of Woocommerce

= 1.0.4 =
* Add integration type to differentiate from other integrations.

= 1.0.3 =
* Fix Hungarian firstname lastname mixing Woocommerce bug

= 1.0.2 =
* Tested with Wordpress 4

= 1.0.1 =
* Add extra fees handling

= 1.0 =
* This is the first public release.
