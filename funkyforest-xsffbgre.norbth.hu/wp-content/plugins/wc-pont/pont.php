<?php
/*
 * Plugin Name: Pont shipping for Woocommerce
 * Plugin URI: https://szathmari.hu/wordpress/15-pick-pack-pont-posta-pont-gls-csomagpont-woocommerce-szallitasi-modul
 * Description: Átvételi pont térképes kiválasztása WooCommerce rendelésnél. Posta Pont, Pick Pack Pont, GLS CsomagPont, Foxpost, Express One Pont, DPD csomagpont, Csomagküldő átvételi pontok térképes keresővel.
 * Version: 7.4
 * Author: szathmari.hu
 * Author URI: https://szathmari.hu/
 * Text Domain: wc-pont
 * Domain Path: /lang
 *
 * Requires at least: 4.0
 * Tested up to: 5.8
 * WC requires at least: 4.1
 * WC tested up to: 5.4
 * Requires PHP: 7.2
 * PHP tested up to: 8.0
 *
 * Copyright: ©2021 szathmari.hu
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( !defined( 'ABSPATH') ) exit;
if ( !function_exists( 'is_woocommerce_activated') ) {
function is_woocommerce_activated() {
if ( class_exists( 'woocommerce') )
return true;
else
return false;
}
}
class WC_Pont
{
public $version = '7.4';
public $plugin_name = 'wc-pont';
public $plugin_url;
public static $plugin_prefix;
public static $plugin_path;
public static $plugin_basename;
public static $carriers;
public static $payment_gateways;
public static $trk_urls;
public static $glsTrCodes;
public static $glsTrCodesEn;
public static $cskuldoTrCodes;
public static $sprinterTrCodes;
public static $dpdTrCodes;
public static $foxTrCodes;
public static $postaTrCodes;
public static $eoneTrCodes;
private static $copyright;
protected $aCarriers;
var $id = 'wc-pont';
public static $log = false;
public function __construct()
{
$this->plugin_url = plugins_url ('/',__FILE__);
$this->aCarriers = array();
self::$plugin_prefix = 'wc_pont_';
self::$plugin_basename = plugin_basename( __FILE__ );
self::$plugin_path = trailingslashit( __DIR__ );
self::$carriers = array(
'posta'=>__( 'Posta Pont','wc-pont'),
'postam'=>__( 'PostaPont Postán maradó','wc-pont'),
'postap'=>__( 'PostaPont (MOL, COOP, MediaMarkt stb.)','wc-pont'),
'postacs'=>__( 'PostaPont Csomagautomata','wc-pont'),
'pick'=>__( 'Pick Pack Pont','wc-pont'),
'gls'=>__( 'GLS CsomagPont','wc-pont'),
'fox'=>__( 'FoxPost','wc-pont'),
'eone'=>__( 'Express One Pont','wc-pont'),
'cskuldo-hu'=>__( 'Csomagküldő Magyarország','wc-pont'),
'cskuldo-cz'=>__( 'Csomagküldő Csehország','wc-pont'),
'cskuldo-pl'=>__( 'Csomagküldő Lengyelország','wc-pont'),
'cskuldo-ro'=>__( 'Csomagküldő Románia','wc-pont'),
'cskuldo-sk'=>__( 'Csomagküldő Szolvákia','wc-pont'),
'dpd'=>__( 'DPD csomagpont','wc-pont'),
'epontok'=>__( 'Személyes átvétel','wc-pont')
);
self::$trk_urls = array (
'gls'=>array(
'HU'=>'https://online.gls-hungary.com',
'RO'=>'https://online.gls-hungary.com',
'SK'=>'https://online.gls-slovakia.sk',
'CZ'=>'https://online.gls-czech.com',
'SI'=>'https://connect.gls-slovenia.com',
'HR'=>'https://online.gls-croatia.com',
),
'cskuldo-hu'=>'https://www.csomagkuldo.hu/kereses/%s',
'cskuldo'=>'https://www.csomagkuldo.hu/kereses/%s',
'pick'=>'https://www.pickpackpont.hu/csomagkereso/?bc=%s',
'dpd'=>'https://tracking.dpd.de/status/hu_HU/parcel/%s',
'fox'=>'https://www.foxpost.hu/csomagkovetes?code=%s',
'posta'=>'https://www.posta.hu/nyomkovetes/nyitooldal?searchvalue=%s',
'postacs'=>'https://www.posta.hu/nyomkovetes/nyitooldal?searchvalue=%s',
'eone'=>'https://tracking.expressone.hu/?plc_number=%s',
);
self::$glsTrCodes = (object) array( 1=>'Irsz & Súly rögzítése',2=>'HUB Outbound scan',3=>'Érkezés a depóba',4=>'Kézbesítés alatt',5=>'Kiszállítva',6=>'HUB tárolás',7=>'Depó tárolás',8=>'Ügyfeles felvétel',9=>'Meghat. időpontra történő kisz',11=>'Szabadság',12=>'Átvevő nem található',13=>'Depó továbbítási hiba',14=>'Áruátvétel bezárva',15=>'Időhiány',16=>'Pénzhiány',17=>'Átvétel megtagadása',18=>'Hibás cím',19=>'Megközelíthetetlen',20=>'Rossz irányítószám',21=>'HUB rakodási hiba',22=>'Vissza a HUB-nak',23=>'Vissza a feladónak',24=>'Depó ismételt kiszállítás',25=>'APL-hiba',26=>'HUB-Inbound',27=>'Small Parcel',28=>'HUB Sérült',29=>'Nincs adat',30=>'Sérülten érkezett',31=>'Totálkár beérkezéskor',32=>'Esti kézbesítés',33=>'Időn túli várakoztatás',34=>'Késői szállítás',35=>'Nem rendelték',36=>'Zárt lépcsőház',37=>'Központ utasítására vissza',38=>'Nincs szállítólevél a csomagon',43=>'Eltűnt',44=>'Not Systemlike Parcel',46=>'Átszállítva',47=>'transferred to subcontractor',51=>'Ügyfeles adat fogadva',52=>'Ügyfeles utánvét adat fogadva',53=>'DEPOT TRANSIT',55=>'CsomagPontba letéve',56=>'CsomagPontban tárolva',57=>'CsomagPont visszáru',58=>'Szomszédba kézbesítve',80=>'CHANGD DLIVERYADRES',81=>'RQINFO NORMAL',82=>'REQFWD MISROUTED',83=>'P&S/P&R rögzítve',84=>'P&S/P&R kinyomtatva',85=>'P&S/P&R rollkartén',86=>'P&S/P&R felvéve',87=>'Nincs P&S/P&R csomag',88=>'Küldemény nem áll készen',89=>'Kevesebb csomagcímke');
self::$glsTrCodesEn = array( 1,3,4,5,7,8,12,15,16,17,18,19,20,22,23,27,28,31,32,33,34,35,36,37,43,46,51,52,55,56,57,58 );
self::$cskuldoTrCodes = array( 1 =>'Várják',2 =>'Beérkezett',3 =>'Küldésre kész',4 =>'departed',5 =>'Elvitelre kész',6 =>'Kiszállítónál',7 =>'Átvéve',8 =>'Visszaszállítás',9 =>'Visszaküldve',10 =>'Visszaérkezett',11 =>'Törölve',12=>'Begyűjtve',999=>'Ismeretlen');
self::$sprinterTrCodes = array( 4=>'A csomag szállítása folyamatban.',5=>'A csomag megérkezett a PickPack Pontra.',14=>'A küldeményt a Sprinter Futárszolgálat átvette.',15=>'A csomag a Sprinter futárnál van, kézbesítés alatt.', 16 =>'Sikeres csomagátvétel', 17=>'A csomag nem került átvételre.', 19 => 'A csomag visszaküldésre került a Feladó részére.');
self::$dpdTrCodes = array( 1 =>'A csomagot a DPD felvette',4 =>'Szállítás folyamatban',5 =>'A kiszállítást végző kirendeltségen',6 =>'Kézbesítés folyamatban, a küldemény futárhoz került',9 =>'A csomagot a címzett átvette a Csomagpontban',10 =>'Sikeres csomagátvétel');
self::$foxTrCodes = array( 'Webáruháznál'=>'Webáruháznál', 'Raktárban' => 'Raktárban', 'Úton'=>'Úton','Automatában'=>'Automatában','Átvéve'=>'Átvéve');
self::$postaTrCodes = array( 1 =>'A küldeményt a feladó előrejelezte, az átadást követően megkezdjük a feldolgozást',2 =>'A küldeményt a feladótól átvettük',3 =>'A küldemény feldolgozás alatt',4 =>'Telefonos egyeztetés címzettel',5 =>'A küldemény a kézbesítőnél van',6 =>'Sikeres kézbesítés rögzítése belső rendszerben',7 =>'A küldemény Csomagautomatából átvehető (az sms/email-ben kapott kóddal)',8 =>'A küldemény postán átvehető', 10 =>'Sikeresen kézbesítve',11 => 'Sikeresen kézbesítve Csomagautomatából',12 =>'UTALT - Elszamolasi esemeny',13 =>'Feladónak visszakézbesítve', 14 => 'Sikeresen kézbesítve PostaPonton', 20 => 'A küldemény PostaPonton 12:00 után átvehető');
self::$eoneTrCodes = (object) array( 'D00' =>'Sikeres kézbesítés','OFD' =>'Kiadva futárnak','OUB' =>'Központi raktárat elhagyta (HU - Budapest)','INB' =>'Központi raktárba érkezett (HU - Budapest)','E53' =>'Címzett kérésére, későbbi kiszállítás','DLS' =>'Adat bekerült a rendszerbe');
self::$copyright = 'szathmari.hu';
self::$payment_gateways = array();
if ( get_option( 'wc_pont_notice_free_shipping') === 'yes')  {
add_action( 'wp_ajax_fs_notice',array( $this,'notice_free_shipping_ajax') );
add_action( 'wp_ajax_nopriv_fs_notice',array( $this,'notice_free_shipping_ajax') );
if ( isset( $_POST["action"] ) &&$_POST["action"] === 'fs_notice')
return;
}
add_filter( 'woocommerce_shipping_settings',array( $this,'settings') );
add_action( 'wp_enqueue_scripts',array( $this,'frontend_css_js') );
add_action( 'woocommerce_review_order_before_payment',array( $this,'wc_pont_html'),1 );
add_action( 'woocommerce_after_checkout_validation',array( $this,'wc_pont_validation'),10,1 );
add_action( 'woocommerce_checkout_update_order_meta',array( $this,'wc_pont_checkout_field_update_post') );
add_action( 'woocommerce_thankyou',array( $this,'wc_pont_thankyou_page') );
add_action( 'woocommerce_admin_order_data_after_shipping_address',array( $this,'wc_pont_show_selected_location_admin'),10,1 );
add_action( 'woocommerce_email_after_order_table',array( $this,'wc_pont_show_selected_location_admin'),10,1 );
add_filter( 'woocommerce_shipping_methods','wc_pont_add_shipping_method');
add_filter( 'woocommerce_default_address_fields','shipping_override_default_address_fields');
add_action( 'woocommerce_view_order','extend_woocommerce_view_order',100 );
add_filter( 'woocommerce_update_order_review_fragments',array( $this,'wc_pont_filter_woocommerce_update_order_review_fragments') );
if ( is_admin() ) {
add_filter( 'woocommerce_get_settings_pages',array( $this,'add_pont_settings_tab') );
add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ),array( $this,'wc_pont_plugin_links') );
}
register_uninstall_hook( __FILE__,'wc_pont_uninstall_callback') ;
add_action( 'admin_init',array( $this,'wc_pont_autoupdate') );
add_action( 'woocommerce_shipping_init',array( $this,'shipping_init') );
add_action( 'woocommerce_calculated_total',array( $this,'wc_pont_round_total') );
add_filter( 'woocommerce_cart_shipping_method_full_label',array( $this,'wc_pont_shipping_logo'),10,2 );
add_filter( 'woocommerce_available_payment_gateways',array( $this,'wc_pont_shipping_available_payment_gateways') );
add_filter( 'manage_edit-shop_order_columns',array( $this,'parcel_number_order_list_column') );
add_action( 'manage_shop_order_posts_custom_column',array( $this,'parcel_number_order_list_column_content'),11,1 );
add_action( 'admin_enqueue_scripts',array( $this,'wc_pont_gls_tracking_order_list_scripts') );
add_action( 'init',array( $this,'wc_pont_schedule_events') );
add_action( 'wc_pont_status_updater',array( $this,'wc_pont_schedule_status_updater') );
add_action( 'plugins_loaded',array( $this,'load_textdomain') );
add_action( 'admin_menu',array( $this,'wc_pont_menu_status') );
add_action( 'admin_post_contact_form',array( $this,'wc_pont_status_query') );
add_action( 'woocommerce_cart_calculate_fees',array( $this,'wc_pont_cod_fees') );
add_action( 'woocommerce_api_'.$this->plugin_name .'-fox',array( $this,'callback_fox') );
add_filter( 'bulk_actions-edit-shop_order',array( $this,'wc_pont_register_bulk_action') );
add_filter( 'handle_bulk_actions-edit-shop_order',array( $this,'pont_export_handle_bulk_action_edit_shop_order'),10,3 );
add_filter( 'removable_query_args',array( $this,'wc_pont_removable_query_args' ) );
add_action( 'woocommerce_order_actions',array( $this,'wc_pont_order_action'),10,1 );
add_action( 'woocommerce_order_action_export_mygls',array( $this,'wc_pont_order_action_export_mygls') );
add_action( 'add_meta_boxes',array( $this,'wc_pont_add_meta_box'),10,3 );
add_action( 'woocommerce_order_hide_shipping_address',array( $this,'wc_pont_order_hide_shipping_address'),10,1 );
add_action( 'woocommerce_my_account_my_orders_columns',array( $this,'wc_pont_my_account_my_orders_columns'),10,1 );
add_action( 'woocommerce_my_account_my_orders_column_order-tracking',array( $this,'wc_pont_my_orders_tracking_column_data'),10,1 );
}
public function shipping_init() {
include_once( 'includes/class-wc-pont.php');
}
function wc_pont_autoupdate()
{
if(!class_exists('WC_Pont_AutoUpdate'))
require_once ( __DIR__.'/includes/wp_autoupdate.php');
$plugin_data = get_plugin_data( __FILE__ );
$plugin_current_version = $plugin_data['Version'];
$plugin_remote_path = 'https://wc-pont.szathmari.hu/update/';
$plugin_slug = plugin_basename( __FILE__ );
$lkey = get_option( 'wc_pont_licencekey','');
new WC_Pont_AutoUpdate ( $plugin_current_version,$plugin_remote_path,$plugin_slug,$lkey );
if ( class_exists( 'WooCommerce') ) {
$paymentGateways = WC()->payment_gateways->payment_gateways();
if( !empty ($paymentGateways) ) {
foreach(  $paymentGateways as $key =>$value ) {
self::$payment_gateways[$value->id] = $value->title;
}
}
}
}
public function add_pont_settings_tab( $settings ) {
$settings[] = include( 'includes/class-wc-settings-pont.php');
return $settings;
}
public function frontend_css_js()
{
if ( is_checkout() ) {
wp_enqueue_style( $this->id .'-style',plugins_url( 'assets/jquery.pont.css',__FILE__ ),false,$this->version );
if ( 'openmap'== get_option( 'wc_pont_mode') ){
wp_enqueue_style( $this->id .'-leaflet','//cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css',false,'1.7.1');
wp_enqueue_style( $this->id .'-leaflet-cluster','//cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.0/MarkerCluster.min.css',false,'1.5.0');
wp_enqueue_style( $this->id .'-leaflet-cluster-default','//cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.0/MarkerCluster.Default.min.css',false,'1.5.0');
}
$suffix	= defined( 'SCRIPT_DEBUG') &&SCRIPT_DEBUG ?'': '.min';
if( !wp_script_is( 'select2','enqueued') )
wp_enqueue_script( 'select2',WC()->plugin_url() .'/assets/js/select2/select2.full'.$suffix .'.js',array('jquery') );
if( !wp_style_is( 'select2','enqueued') )
wp_enqueue_style( 'select2',WC()->plugin_url() .'/assets/css/select2.css',array() );
wp_enqueue_script( $this->id .'_script-select2-hu','//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/hu.js',
array( 'jquery'),'4.0.3',true );
}
if ( get_option( 'wc_pont_notice_free_shipping') === 'yes')
wp_enqueue_script( 'fs-notice',plugin_dir_url( __FILE__ ) .'assets/fs-notice.js',array( 'jquery'),$this->version,false );
}
public function settings( $settings )
{
$updated_settings = array();
foreach ( $settings as $section ) {
if ( isset( $section['id'] ) &&'shipping_options'== $section['id'] &&isset( $section['type'] ) &&'sectionend'== $section['type'] ) {
$shipping_methods = array();
global $woocommerce;
}
$updated_settings[] = $section;
}
return $updated_settings;
}
public function wc_pont_html( $checkout )
{
if ( get_option( 'wc_pont_gls_round_total') == 'yes'): ;echo '		<script>
			 (function($) {
			 	$( \'body\' ).on(\'change\', \'#payment [name="payment_method"]\', function() {
					$( \'body\' ).trigger(\'update_checkout\');
				});
		 	})(jQuery);
		</script>
		';
endif;
if ( get_option( 'wc_pont_mode') == 'minimal'): ;echo '			<div class="ponta woocommerce-shipping-fields">
				<div class="pont-ajax"></div>
			</div>
			';
return;
endif;
list( $p,$jsVar ) = $this->getList();
$jsVar[] = 'gmapsKey =\''.get_option( 'wc_pont_gmapskey').'\'';
if ( empty( $this->aCarriers ) ){
return;
}
$jsLang = array(
__( 'Település kiválasztása','wc-pont'),
__( 'Átvevőpont kiválasztása','wc-pont'),
__( 'Nem található...','wc-pont'),
__( 'pont','wc-pont'),
__( 'Kiválasztott átvevőhely','wc-pont')
);
$uzip = (get_option( 'wc_pont_use_zip') == 'yes') ?1 : 0;
;echo '
	<div class="pont woocommerce-shipping-fields">
		<div class="pont-ajax">
			<h3 class="pont">';_e( 'Válassz átvevőhelyet','wc-pont');;echo '</h3>
		</div>

	';
if ( 'simple'== get_option( 'wc_pont_mode') ): ;echo '		<select  style="width:100%" name="dpoint" id="dpoint" class="select"></select>
		<label for="dpoint" ></label>
		';
wp_enqueue_script( $this->id .'ajax_script',$this->plugin_url .'assets/jquery.pont.ajax.js',array( 'jquery'),$this->version,true );
wp_add_inline_script(
$this->id .'ajax_script',
"/* <![CDATA[ */\n var pontajaxurl ='".$this->plugin_url.'ajax.php\', pontc, wc_selected_pont'
.", uZip = {$uzip}"
.', lang='.json_encode($jsLang,JSON_UNESCAPED_UNICODE)
.', '.implode( ",",$jsVar ) ."\n/* ]]> */",
'before');
;echo '		</div>
	';
return;
endif;
if ( 'openmap'== get_option( 'wc_pont_mode') ): ;echo '	<select  style="width:100%" name="ShIrsz" id="ShIrsz" data-placeholder="Irányítószám/település kiválasztása" class="select2" ></select>
	<select  style="width:100%" name="ShipmentPoint" id="ShipmentPoint"></select>
	<div id="map-container">
		<div id="openmap"></div>
	</div></div>
		';
wp_enqueue_script( 'wc-pont-marker',
'//cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js',
'1.7.1',true );
wp_enqueue_script( 'wc-pont-marker-cluster',
'//cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.0/leaflet.markercluster.js',
'1.5.0',true );
wp_enqueue_script( $this->id .'_script',
$this->plugin_url .'assets/jquery.pont.openmap.js',
array( 'jquery'),$this->version,true );
wp_add_inline_script(
$this->id .'_script',
'SZip = \''.WC()->customer->get_billing_postcode().'\';
      lang = '.json_encode($jsLang,JSON_UNESCAPED_UNICODE) .';
      mediaURL = "'.$this->plugin_url .'assets/"'
."; uZip = {$uzip};"
.implode( ";",$jsVar ) .';
      window.pont = \''.json_encode($p,JSON_UNESCAPED_UNICODE) .'\';

      (function ($) {
           if(typeof window.initOpenMap === "function") {
              window.initOpenMap();
          } else {
            setTimeout(function() {
             window.initOpenMap();
            }, 100);
          }
      })(jQuery);'
,'before');
return;
endif;
wp_enqueue_script( 'wc-pont-marker',
'//cdnjs.cloudflare.com/ajax/libs/js-marker-clusterer/1.0.0/markerclusterer_compiled.js',
array( 'jquery'),'1.0.0',true );
wp_enqueue_script( $this->id .'_script',
$this->plugin_url .'assets/jquery.pont.map.js',
array( 'jquery'),$this->version,true );
wp_add_inline_script(
$this->id .'_script',
'SZip = \''.WC()->customer->get_billing_postcode().'\';
		lang = '.json_encode($jsLang,JSON_UNESCAPED_UNICODE) .';
		mediaURL = "'.$this->plugin_url .'assets/"'
."; uZip = {$uzip};"
.implode( ";",$jsVar ) .';
		window.pont = \''.json_encode($p,JSON_UNESCAPED_UNICODE) .'\';
		(function ($) {
			if (typeof google == "undefined"){
		        var gk = (typeof gmapsKey != "undefined" ? "&key="+gmapsKey : "");
		        $.getScript("//maps.google.com/maps/api/js?language=hu&region=HU&callback=initMap"+gk);
		    } else {
		      if(typeof window.initMap === "function") {
						window.initMap();
				} else {
					setTimeout(function() {
					 window.initMap();
					}, 100);
				}
		    }
		})(jQuery);'
,'before');
;echo '	<select  style="width:100%" name="ShIrsz" id="ShIrsz" data-placeholder="Irányítószám/település kiválasztása" class="select2" ></select>
	<select  style="width:100%" name="ShipmentPoint" id="ShipmentPoint"></select>
	<div id="map-container">
		<div id="map"></div>
	</div></div>

	';
}
public function wc_pont_validation() {
if ( empty( WC()->shipping->get_packages() ) )
return;
$chosen_methods = WC()->session->get( 'chosen_shipping_methods');
if ( !$_POST['wc_selected_pont'] AND strstr( $chosen_methods[0],'wc_pont_shipping_method') ) {
wc_add_notice( __( 'Nem választottál átvevőhelyet','wc-pont'),'error');
}
}
public function wc_pont_checkout_field_update_post( $order_id )
{
$chosen_methods = WC()->session->get( 'chosen_shipping_methods');
if ( $_POST['wc_selected_pont'] AND strstr( $chosen_methods[0],'wc_pont_shipping_method') ){
$carrier 	= explode( '|',$_POST['wc_selected_pont'] )[1];
if ( 'fox'=== $carrier )
update_post_meta( $order_id,'package_size',get_option( 'wc_pont_fox_size','M') );
elseif ( 'postacs'=== $carrier )
update_post_meta( $order_id,'package_size',get_option( 'wc_pont_posta_size','M') );
$d = strtr( $_POST['wc_selected_pont'],WC_Pont::$carriers );
update_post_meta( $order_id,'wc_selected_pont',esc_attr( $d ) );
$this->log(
'Chosen '.json_encode( $_POST['wc_selected_pont'],JSON_UNESCAPED_UNICODE)
.' order id: '.$order_id
.' carrier: '.$carrier,
'info'
);
}
}
public function wc_pont_thankyou_page( $order_id )
{
$pontMeta = get_post_meta( $order_id,'wc_selected_pont',true );
if ( $pontMeta ) {
$pontMeta = $this->wc_pont_format( $pontMeta );
;echo '			<h2>';_e( 'A kiválasztott átvevőhely:','wc-pont');;echo '</h2>
			<address>
				';echo $pontMeta;;echo '			</address>
		';
}
}
public function wc_pont_show_selected_location_admin( $order )
{
$pontMeta = get_post_meta( $order->get_id(),'wc_selected_pont',true );
if ( !empty( $pontMeta ) ) {
$pontMeta = $this->wc_pont_format( $pontMeta );
;echo '		<h4>';_e( 'A kiválasztott átvevőhely:','wc-pont');;echo '</h4>
		<p>
			';echo $pontMeta;;echo '		</p>
	';
}
}
public function is_available( $package )
{
$is_available = "yes"=== $this->enabled;
if ( $is_available &&$this->get_valid_postcodes() ) {
$is_available = $this->is_valid_postcode( $package['destination']['postcode'],$package['destination']['country'] );
}
if ( $is_available ) {
$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
if ( is_array( $ship_to_countries ) &&!in_array( $package['destination']['country'],$ship_to_countries ) ) {
$is_available = false;
}
}
return apply_filters( 'woocommerce_shipping_'.$this->id .'_is_available',$is_available,$package,$this );
}
static function wc_pont_format( $pontMeta ) {
$pontMeta 	= explode( '|',$pontMeta);
$carrier 	= array_search( $pontMeta[1],self::$carriers );
$jsonfile 	= self::$plugin_path .$carrier ."pont.json";
if ( file_exists( $jsonfile ) ){
$p = json_decode( file_get_contents( $jsonfile ),true );
}elseif( $carrier=='epontok')
$p = get_option('wc_pont_epontok');
if ( $p ){
$id = array_search( $pontMeta[2],array_column( $p,'id') );
if ( $id===false )
return  '<span style="color:red">'.__( 'Jelenleg nem található az adatbázisban!','wc-pont').'</span><br>'.implode( '<br>',$pontMeta );
$p = (object)$p[$id];
isset( $p->group ) ?: $p->group = '';
isset( $p->desc ) ?: $p->desc = '';
isset( $p->phone ) ?: $p->phone = '';
$templateRep = array(
'[név]'=>$p->name,
'[térkép]'=>'<a href="//maps.google.com/?q='.$p->lat.'+'.$p->lon
.'" target="_blank" title="Google Térkép - '.$pontMeta[1].' '.$p->id.'">'
.$p->address.'</a>',
'[térképcímmel]'=>'<a href="//maps.google.com/?q='.urlencode( $p->name.' '.$pontMeta[1] )
.'" target="_blank" title="Google Térkép - '.$pontMeta[1].' '.$p->id.'">'
.$p->address.'</a>',
'[irányítószám]'=>$p->zip,
'[város]'=>$p->city,
'[cím]'=>$p->address,
'[csoport]'=>$p->group,
'[leírás]'=>$p->desc,
'[logó]'=>'<img src="'.plugins_url ('/assets/',__FILE__) .$carrier.'marker.png">',
'[telefon]'=>$p->phone,
'[azonosító]'=>$p->id,
'[szolgáltató]'=>$pontMeta[1],
'[újsor]'=>'<br>',
);
$template = get_option( 'wc_pont_template');
$template = empty( $template ) ?'[név] [térkép]': $template;
$pontMeta = strtr( $template,$templateRep );
}else
$pontMeta = implode( '<br>',$pontMeta );
return $pontMeta;
}
static function wc_pont_upload_dir() {
$upload_dir = wp_upload_dir();
wp_mkdir_p( $upload_dir['basedir'].'/wc_pont');
$path = $upload_dir['basedir'] .'/wc_pont';
return apply_filters( 'wc_pont_get_upload_dir',$path );
}
function notice_free_shipping_ajax() {
$min = get_option( 'wc_pont_notice_free_shipping_value',20000 );
$trigger = get_option( 'wc_pont_notice_free_shipping_trigger_value',10000 );
$total = WC()->cart->get_displayed_subtotal();
if ( WC()->cart->display_prices_including_tax() ) {
$total = round( $total -( WC()->cart->get_discount_total() +WC()->cart->get_discount_tax() ),wc_get_price_decimals() );
}else {
$total = round( $total -WC()->cart->get_discount_total(),wc_get_price_decimals() );
}
$resp = array( 'price'=>0 );
if ( $total >$trigger and $total <$min ) {
$text = get_option( 'wc_pont_notice_free_shipping_text', __( 'Ingyen szállítjuk ha még [összeg] értékben rendelsz!','wc-pont') );
$html = strtr($text,array ('[' . __( 'összeg','wc-pont') . ']'=>wc_price( $min-$total )));
$resp = array( 'price'=>wc_price( $min-$total ),'html'=>$html,'to'=>9000 );
}
echo json_encode( $resp );
wp_die();
}
function wc_pont_filter_woocommerce_update_order_review_fragments( $array ) {
$virutal = true;
foreach( WC()->cart->get_cart() as $k =>$item ) {
if ( !$item['data']->is_virtual() ) {
$virutal = false;
break;
}
}
if ( $virutal )
return $array;
if ( wc_get_chosen_shipping_method_ids()[0] !== 'wc_pont_shipping_method'){
$array[".pont-ajax"] = '<div class="pont-ajax"></div>';
return $array;
}
$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods')[0];
$o = explode( ':',$chosen_shipping_method );
$o = (object)get_option('woocommerce_wc_pont_shipping_method_'.$o[1].'_settings');
$html = '';
if ( get_option( 'wc_pont_mode') == 'minimal'):
$html = '<select style="width:100%" name="wc_selected_pont">
						<option value="">'.__( 'Átvevőhely','wc-pont') .'</option>';
list( $p,$jsVar ) = $this->getList( $o->carrier );
foreach ($p as $k =>$v)
$html .= '<option value="'.$v['address'].'|'.$v['c'].'|'.$v['id'].'">'.$v['address'].'</option>';
$html .= '</select>
				</div>';
endif;
$title = get_option( 'wc_pont_select_title',__( 'Válassz [szolgáltató] átvevőhelyet','wc-pont') ) ;
$title = strtr( $title,array ('[szolgáltató]'=>'%s') );
$array[".pont-ajax"] = '
		<div class="pont-ajax">
    		<h3 class="pont"> '.sprintf( $title,$o->title ) .' </h3>'
.$html
.'</div>';
return $array;
}
private function getList( $filter = array() ) {
$jsVar = array();
$zones = WC_Shipping_Zones::get_zones();
$active_carriers = array();
$instances_carriers = '';
foreach ((array) $zones as $key =>$the_zone ) {
$shipping_methods = $the_zone['shipping_methods'];
foreach ( $shipping_methods as $shipping_method) {
if ( isset( $shipping_method->enabled ) &&'yes'=== $shipping_method->enabled ) {
if (!empty( $shipping_method->carrier )) {
$active_carriers = array_merge( $active_carriers,$shipping_method->carrier );
$instances_carriers .= 'wc_pont_shipping_method_'.$shipping_method->instance_id.'= [\''.implode( "', '",$shipping_method->carrier ).'\'],';
}
}
}
}
$this->aCarriers = array_unique($active_carriers);
$jsVar[] = rtrim( $instances_carriers,',');
$p = array();
$this->log( 'List: generate list of points','info');
$u = $this->id.'.'.$this::$copyright;
foreach( $this::$carriers as $carrier =>$carriers_name):
if ( !in_array( $carrier,$this->aCarriers) ||$carrier == 'epontok')
continue;
if ( !empty( $filter ) &&!in_array( $carrier,$filter ) )
continue;
$this->log(
'List:'.
' carrier: '.$carriers_name,
'info'
);
$jsonfile = self::$plugin_path .$carrier .'pont.json';
$jsonAge = 21;
if ( file_exists( $jsonfile ) )
$jsonAge = ( time() -filemtime( $jsonfile ) ) / 3600 ;
if ( !file_exists( $jsonfile ) ||$jsonAge  >12 ) {
$r = array();
$in = wp_remote_get( 'https://'.$u.'/'.$carrier.'pont.json');
$this->log(
'List:'.
' response code: '.wp_remote_retrieve_response_code( $in ) .
' carrier: '.$carriers_name,
'info'
);
if ( !is_array( $in ) ||200 !== wp_remote_retrieve_response_code( $in ) &&$jsonAge >24*15 ){
wc_print_notice( __( $carriers_name .' pontok listája régi.','wc-pont'),'error');
error_log( $carriers_name .' '.__( 'pontok listája régi, nem lehet frissíteni.','wc-pont') );
$this->log(
'List:'.
' age: '.$jsonAge .
' carrier: '.$carriers_name,
'error'
);
}
if ( ( is_writable( $jsonfile ) &&200 === wp_remote_retrieve_response_code( $in ) ) ||!file_exists( $jsonfile ) ){
file_put_contents( $jsonfile,$in['body'],LOCK_EX );
$this->log(
'List:'.
' updated: '.$carrier.'pont.json'.
' carrier: '.$carriers_name,
'info'
);
}
else{
error_log( sprintf( __( 'A plugins/wc-pont/%spont.json fájl nem írható','wc-pont'),$carrier ) );
$this->log(
'List:'.
' not writeable'.
' carrier: '.$carrier.'pont.json',
'error'
);
}
}
if ( file_exists( $jsonfile ) ){
$p = array_merge( $p,json_decode( file_get_contents( $jsonfile ),true ));
}
endforeach;
$epontok = get_option('wc_pont_epontok');
if( $epontok )
$p = array_merge( $p,$epontok);
$p = apply_filters( 'wc_pont_list', $p );
return array($p,$jsVar);
}
function wc_pont_plugin_links( $links )
{
$plugin_links = array(
'<a href="https://szathmari.hu/wordpress/15-pick-pack-pont-posta-pont-gls-csomagpont-woocommerce-szallitasi-modul">'.__( 'Dok','wc-pont') .'</a>',
'<a href="'.admin_url( 'admin.php?page=wc-settings&tab=pont') .'">'.__( 'Settings','woocommerce') .'</a>',
);
return array_merge( $plugin_links,$links );
}
public function wc_pont_round_total( $total ){
global $woocommerce;
if ( get_option( 'wc_pont_gls_round_total') == 'yes'&&'HUF'== get_woocommerce_currency() ) {
$current_gateway = WC()->session->chosen_payment_method;
if( 'cod'== $current_gateway ){
$total = round( $total/5,0 ) * 5;
}
}
return $total;
}
function wc_pont_shipping_logo( $label,$method ) {
$shipping_method	= explode( ":",$label );
$id				= explode( ":",$method->id );
$method_settings	= maybe_unserialize( get_option( 'woocommerce_wc_pont_shipping_method_'.$id[1] .'_settings') );
if ( !empty( $method_settings['logo'] ) &&@getimagesize( $method_settings['logo'] ) &&$method_settings['logo_enable'] != 'no') {
$logo_size = @getimagesize( $method_settings['logo'] );
$imagen	= '<img class="wc_pont_shipping_logo" src="'.$method_settings['logo'] .'" witdh="'.$logo_size[0] .'" height="'.$logo_size[1] .'" />';
if ( $method_settings['logo_enable'] == 'before') {
$label = $imagen .' '.$label;
}else if ( $method_settings['logo_enable'] == 'after') {
$label = $shipping_method[0] .' '.$imagen .':'.$shipping_method[1];
}else {
$label = $imagen .':'.$shipping_method[1];
}
}
if ( 'wc_pont_shipping_method'=== $method->method_id &&$method->cost == 0 &&get_option( 'wc_pont_free_shipping_label','') != '')
$label = get_option( 'wc_pont_free_shipping_label','Ingyenes szállítás').' '.$label;
return $label;
}
function wc_pont_shipping_available_payment_gateways( $gateways ) {
if ( isset( WC()->session->chosen_shipping_methods ) ) {
$id = explode( ":",WC()->session->chosen_shipping_methods[0] );
}else if ( isset( $_POST['shipping_method'][0] ) ) {
$id = explode( ":",$_POST['shipping_method'][0] );
}
if ( !isset( $id[1] ) ) {
return $gateways;
}
$method_settings = maybe_unserialize( get_option( 'woocommerce_wc_pont_shipping_method_'.$id[1] .'_settings') );
if ( isset( $_POST['payment_method'] ) &&!$gateways ) {
$gateways = $_POST['payment_method'];
}
if ( !empty( $method_settings['payments'] ) &&$method_settings['payments'][0] != 'all') {
foreach ( $gateways as $k =>$v ) {
if ( isset ( $method_settings['payments'] ) &&is_array( $method_settings['payments'] ) ) {
if ( !in_array( $k,$method_settings['payments'] ) )
unset( $gateways[$k] );
}else {
if ( $k != $method_settings['payments'] )
unset( $gateways[$k] );
}
}
}
return $gateways;
}
function parcel_number_order_list_column( $columns ) {
$columns['shipping_parcel_number'] = __( 'Követés','wc-pont');
return $columns;
}
function parcel_number_order_list_column_content( $column ) {
global $post;
if ( $column === 'shipping_parcel_number')
echo $this->get_trackink_info( $post->ID );
if ( 'shipping_address'=== $column ) {
$pontMeta = get_post_meta( $post->ID,'wc_selected_pont',true );
if ( $pontMeta ) {
$pontMeta 	= explode( '|',$pontMeta);
echo sprintf( '<span class="description pont id">%s: %s</span>',__( 'Azonosító','wc-pont'),$pontMeta[2] );
}
$pn = get_post_meta( $post->ID,'shipping_parcel_number',true );
$glsCountry = get_option( 'wc_pont_gls_sender_country','HU');
$trkURL = WC_Pont::$trk_urls['gls'][$glsCountry ].'/tt_page.php?tt_value=%s';
$shipping_carrier = get_post_meta( $post->ID,'shipping_carrier',true );
if ( 'cskuldo'=== $shipping_carrier )
$trkURL = WC_Pont::$trk_urls['cskuldo-hu'];
elseif ( $shipping_carrier === 'pick')
$trkURL = WC_Pont::$trk_urls['pick'];
elseif ( $shipping_carrier === 'dpd')
$trkURL = WC_Pont::$trk_urls['dpd'];
elseif ( $shipping_carrier === 'fox')
$trkURL = WC_Pont::$trk_urls['fox'];
elseif ( $shipping_carrier === 'posta')
$trkURL = WC_Pont::$trk_urls['posta'];
elseif ( $shipping_carrier === 'eone')
$trkURL = WC_Pont::$trk_urls['eone'];
if (  !empty ( $pn ) )
echo sprintf( '<span class="description pont gls">%s: <a href="'.$trkURL.'" target="_blank">%s</a></span>',__( 'Csomagszám','wc-pont'),$pn,$pn );
}
}
private function update_status_gls_tracking ( $glsTrCode,$id ){
self::$glsTrCodes = apply_filters( 'wc_pont_gls_tracking_code',self::$glsTrCodes );
$status = get_option( 'wc_pont_gls_code_'.$glsTrCode );
if ( !$status )
return array( __( 'Nem történt','wc-pont').' ',self::$glsTrCodes->$glsTrCode.' '.__( 'állapot nincs társítva','wc-pont'),);
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_gls_code_'.$glsTrCode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
private function update_status_cskuldo_tracking ( $trcode,$id ){
self::$cskuldoTrCodes = apply_filters( 'wc_pont_fox_tracking_code',self::$cskuldoTrCodes );
$status = get_option( 'wc_pont_cskuldo_code_'.$trcode );
if ( !$status )
return array( __( 'Nem történt','wc-pont').' ',self::$cskuldoTrCodes[$trcode].' '.__( 'állapot nincs társítva','wc-pont'),);
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_cskuldo_code_'.$trcode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
private function update_status_sprinter_tracking ( $trcode,$id ){
self::$sprinterTrCodes = apply_filters( 'wc_pont_sprinter_tracking_code',self::$sprinterTrCodes );
$status = get_option( 'wc_pont_sprinter_code_'.$trcode );
if ( !$status ){
$change = $trcode ?self::$sprinterTrCodes[$trcode ].' '.__( 'állapot nincs társítva','wc-pont') : '('.__( 'Nincs követési adat','wc-pont').')';
return array( __( 'Nem történt','wc-pont').' ',$change  );
}
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_sprinter_code_'.$trcode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
private function update_status_dpd_tracking ( $trcode,$id ){
self::$dpdTrCodes = apply_filters( 'wc_pont_dpd_tracking_code',self::$dpdTrCodes );
$status = get_option( 'wc_pont_dpd_code_'.$trcode );
if ( !$status ){
$change = $trcode ?self::$dpdTrCodes[$trcode ].' '.__( 'állapot nincs társítva','wc-pont') : '('.__( 'Nincs követési adat','wc-pont').')';
return array( __( 'Nem történt','wc-pont').' ',$change  );
}
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_dpd_code_'.$trcode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
private function update_status_fox_tracking ( $trcode,$id ){
self::$foxTrCodes = apply_filters( 'wc_pont_fox_tracking_code',self::$foxTrCodes );
$status = get_option( 'wc_pont_fox_code_'.$trcode );
if ( !$status ){
$change = $trcode ?self::$foxTrCodes[$trcode ] .' '.__( 'állapot nincs társítva','wc-pont') : '('.__( 'Nincs követési adat','wc-pont').')';
return array( __( 'Nem történt','wc-pont').' ',$change  );
}
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_fox_code_'.$trcode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
private function update_status_posta_tracking ( $trcode,$id ){
self::$postaTrCodes = apply_filters( 'wc_pont_posta_tracking_code',self::$postaTrCodes );
$status = get_option( 'wc_pont_posta_code_'.$trcode );
if ( !$status ){
$change = $trcode ?self::$postaTrCodes [$trcode ] .' '.__( 'állapot nincs társítva','wc-pont') : '('.__( 'Nincs követési adat','wc-pont').')';
return array( __( 'Nem történt','wc-pont').' ',$change  );
}
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_posta_code_'.$trcode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
private function update_status_eone_tracking ( $trcode,$id ){
self::$eoneTrCodes = apply_filters( 'wc_pont_eone_tracking_code',self::$eoneTrCodes );
$status = get_option( 'wc_pont_eone_code_'.$trcode );
if ( !$status ){
$change = $trcode ?self::$eoneTrCodes [$trcode ] .' '.__( 'állapot nincs társítva','wc-pont') : '('.__( 'Nincs követési adat','wc-pont').')';
return array( __( 'Nem történt','wc-pont').' ',$change  );
}
$status = ( 0 === strpos( $status,'wc-') ) ?substr( $status,3 ) : $status;
$order = new WC_Order( $id );
$order_statuses = wc_get_order_statuses();
$old = $order->get_status();
if ( !$order->has_status( $status ) )
$order->update_status( get_option( 'wc_pont_eone_code_'.$trcode ) );
return array ( $order_statuses['wc-'.$old],$order_statuses['wc-'.$status] );
}
public function wc_pont_gls_tracking_order_list_scripts( $hook ) {
global $post_type;
if ( 'edit.php'== $hook &&'shop_order'== $post_type ) {
wp_enqueue_style( 'wc_pont_gls_tracking',plugins_url('assets/admin.css',__FILE__) );
}
}
public static function log( $message,$level = 'info') {
if (  WP_DEBUG ) {
if ( empty( self::$log ) ) {
self::$log = wc_get_logger();
}
self::$log->log( $level,$message,array( 'source'=>'wc-pont') );
}
}
public function wc_pont_is_csomagkuldo( $pontMeta ) {
if ( preg_match( '#^([^ ]+) ?([^ ]*)#',$pontMeta,$m ) &&$m[1] == 'Csomagküldő')
return $m;
else return false;
}
function wc_pont_schedule_events() {
$recurrence = get_option( 'wc_pont_schedule_recurrence_status',0 );
if ( ( !$recurrence ) &&wp_next_scheduled ( 'wc_pont_status_updater') ){
$this->log( 'Cancelled wc_pont_status_updater. '.json_encode( wp_get_scheduled_event( 'wc_pont_status_updater') ),'info');
wp_clear_scheduled_hook( 'wc_pont_status_updater');
}
elseif ( $recurrence &&!wp_next_scheduled ( 'wc_pont_status_updater') ) {
wp_schedule_event( time(),$recurrence,'wc_pont_status_updater');
$this->log( 'Added wc_pont_status_updater. Recurrence: '.$recurrence .' Scheduleds: '.json_encode( wp_get_scheduled_event( 'wc_pont_status_updater') ),'info');
}
}
function wc_pont_schedule_status_updater() {
global $wpdb;
$this->log( 'Running scheduled status updater event','info');
$statuses = apply_filters( 'wc_pont_status_exclude',get_option('wc_pont_tracking_exclusion', ['wc-completed','wc-refunded','wc-cancelled','wc-failed']) );
$this->log( 'Statuses	excluded: '.json_encode( $statuses,JSON_UNESCAPED_UNICODE ),'info');
$statuses = implode( "','",$statuses );
$results = $wpdb->get_results("
			SELECT p.id, m.meta_value as t,
				substring_index(s.meta_value,'|', 1) as carrier,
				substring_index(substring_index(s.meta_value,'|', 2), '|', -1) as carrier2
			FROM {$wpdb->prefix}posts p
			LEFT JOIN {$wpdb->prefix}postmeta m
			ON p.id = m.post_id
			LEFT JOIN {$wpdb->prefix}postmeta s
			ON p.id = s.post_id
	  		WHERE p.post_type LIKE 'shop_order'
			AND p.post_status NOT IN ('$statuses') AND p.post_date >= NOW() - INTERVAL 30 DAY
			AND m.meta_key = 'shipping_parcel_number'
			AND s.meta_key = 'shipping_carrier'
			");
if ( is_null($results) ||!empty( $wpdb->last_error ) ){
$this->log( 'Scheduled event DB fail','error');
return null;
}
$this->log( 'Scheduled event items: '.count( $results ),'info');
$resp=array();
foreach( $results as $r=>$k ){
$code = 0;
$carrier = explode( '|',$k->carrier );
if ( $carrier[0] == 'gls'){
include_once( 'includes/mygls.php');
$code = get_gls_tracking_info( $k->t, $k->id, $carrier[1] ?? 0 );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_gls_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
elseif ( strpos( $k->carrier ,'cskuldo') !== false ){
include_once( 'includes/csomagkuldo.php');
$code = getCsomagkuldoTrackingInfo( $k->t,$k->id );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_cskuldo_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
elseif ( $carrier[0] == 'pick') {
include_once( 'includes/sprinter.php');
$code = get_sprinter_tracking_info( $k->t, $k->id );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_sprinter_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
elseif ( $carrier[0] == 'dpd') {
include_once( 'includes/dpd.php');
$code = get_dpd_tracking_info( $k->t,$k->id );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_dpd_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
elseif ( $carrier[0] == 'fox') {
include_once( 'includes/fox.php');
$code = get_fox_tracking_info_web( $k->t, $k->id );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_fox_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
elseif ( $carrier[0] == 'posta') {
include_once( 'includes/posta.php');
$code = get_posta_tracking_info( $k->t,$k->id );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_posta_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
elseif ( $carrier[0] == 'eone') {
include_once( 'includes/eone.php');
$code = get_eone_tracking_info( $k->t,$k->id );
$resp[$k->id]['status'] = $code[0];
$s = $this->update_status_eone_tracking( $code[1],$k->id );
$s = ( $s[0] == $s[1] ) ?__( 'Nincs','wc-pont').': '.$s[0] : $s[0].' > '.$s[1];
$resp[$k->id]['mod'] = $s;
}
$this->log(
'Orders: id:'.$k->id .
' carrier:'.$k->carrier .
' code: '.$k->t .
' TR result: '.$code[1],
'info'
);
}
$this->log( 'Scheduled event complete','info');
return $resp;
}
function load_textdomain() {
load_plugin_textdomain( 'wc-pont',false,dirname( plugin_basename( __FILE__ ) ) .'/languages');
}
public function wc_pont_menu_status() {
add_submenu_page( 'woocommerce','Rendelési állapotok','Rendelési állapotok','manage_options','pont_status',array( $this,'wc_pont_menu_page_status'));
add_menu_page('Posta', 'Posta', 'manage_woocommerce', 'posta',  array( $this, 'wc_pont_posta_page' ), 'dashicons-schedule', null);
remove_menu_page( 'posta' );
add_menu_page('Címke', 'Címke', 'manage_woocommerce', 'cimke',  array( $this, 'wc_pont_cimke_page' ), 'dashicons-format-aside', null);
remove_menu_page( 'cimke' );
}
public function wc_pont_menu_page_status() {
echo '			<h2>';_e( 'Rendelési állapotok frissítése követőkód alapján','wc-pont');echo '</h2>
			<div class="wrap">
			';
$action = empty( $_REQUEST['action'] ) ?'': $_REQUEST['action'];
if ( !empty( $action ) &&$action ==  'update') {
$data = $this->wc_pont_schedule_status_updater();
if ( !empty( $data ) ) {
;echo '				<table class="widefat fixed">
					<thead>
					<tr>
						<th id="order" class="manage-column" scope="col">';_e( 'Rendelés','wc-pont');echo '</th>
						<th id="status" class="manage-column" scope="col">';_e( 'Állapot','wc-pont');echo '</th>
						<th id="status" class="manage-column" scope="col">';_e( 'Módosítás','wc-pont');echo '</th>
					</tr>
					</thead><tbody>
					';
foreach ( $data as $key =>$value ){
$order = wc_get_order( $key );
print '<tr><td><a href="'.admin_url("post.php?post={$key}&action=edit",'admin') .'">'.$order->get_order_number().'</a></td><td>'.$value['status'] .'</td><td>'.$value['mod'] .'</td></tr>';
}

echo '				</tbody></table>\'
				';
}else {
_e( 'Nincs ilyen rendelés az elmúlt 30 napból','wc-pont');
}
}
echo '<form name="status" action="'.admin_url('admin.php?page=pont').'" method="GET">';
echo '			<input type="hidden" name="action" value="update"/>
<button type="submit" name="page" value="pont_status" class="button submit">';_e( 'Frissítés','wc-pont');echo '</button>
</div>
';
}
public function wc_pont_cod_fees( WC_Cart $cart,$apply_fee = true ) {
if( $apply_fee &&!defined( 'DOING_AJAX') ||!DOING_AJAX )
return;
$cod_fee = get_option( 'wc_pont_cod_fee');
if ( empty( $cod_fee ) )
return;
if ( !is_numeric( $cod_fee ) )
return;
$payment_gateway = isset( $_POST['payment_method'] ) &&$_POST['payment_method'] === 'cod'?'cod': '';
if( !$payment_gateway ) {
$payment_gateway = WC()->session->get( 'chosen_payment_method');
if( !$payment_gateway ) {
$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
if( !empty( $available_gateways ) &&current( array_keys( $available_gateways ) ) === 'cod') {
$payment_gateway = 'cod';
}
}
}
if( ( $payment_gateway !== 'cod') &&$apply_fee )
return;
$free_shiping_value = get_option( 'wc_pont_notice_free_shipping_value');
$free_shiping_value = apply_filters( 'wc_pont_notice_free_shipping_value',is_numeric( $free_shiping_value ) ?$free_shiping_value : 0,$free_shiping_value );
$this->log(
'COD Fee	'.
' Tax: '.WC()->cart->display_prices_including_tax() .' Total: '.WC()->cart->cart_contents_total .' Taxes: '.array_sum(  WC()->cart->get_cart_contents_taxes() ) .' Free shiping: '.$free_shiping_value,
'info'
);
if ( WC()->cart->display_prices_including_tax() ) {
$total = WC()->cart->cart_contents_total +array_sum( WC()->cart->get_cart_contents_taxes() );
}else {
$total = WC()->cart->cart_contents_total;
}
if ( !empty( $free_shiping_value ) &&is_numeric( $free_shiping_value ) &&$total >= $free_shiping_value )
return;
global $woocommerce;
$cod_fee = apply_filters( 'wc_pont_cod_fee',is_numeric( $cod_fee ) ?$cod_fee : 0,$cod_fee );
$tax = true;
if ( $apply_fee )
$woocommerce->cart->add_fee( __( 'Cash on delivery','woocommerce'),$cod_fee,apply_filters( 'wc_pont_cod_fee_tax',$tax ) );
else
return $cod_fee;
}
public function callback_fox() {
http_response_code(200);
$body = file_get_contents('php://input');
$this->log(
'Callback Fox	'.
' entity body: '.$body,
'info'
);
$answer = json_decode( $body );
if ( isset( $answer->webHookAction ) ) {
include_once( 'includes/fox.php');
process_callback( $answer );
}
die();
}
static function st_array( $transient,$array,$data ) {
$d = get_transient( $transient );
$d[$array] = $data;
set_transient( $transient,$d,5 * MINUTE_IN_SECONDS  );
}
static function get_trans( $transient ) {
$data = get_transient( $transient );
delete_transient( $transient );
return $data;
}
public function wc_pont_register_bulk_action( $actions ) {
$show = get_option( 'wc_pont_bulk_actions_menu' ) ?: [ 'mygls', 'csomagkuldo', 'dpd', 'eone', 'fox', 'posta', 'sprinter', 'csv' ];
if ( in_array ( 'mygls', $show ) ) $actions['export_mygls'] = __('MyGLS címke','wc-pont');
if ( in_array ( 'csomagkuldo', $show ) ) $actions['export_csomagkuldo'] = __('Csomagküldő címke','wc-pont');
if ( in_array ( 'dpd', $show ) ) $actions['export_dpd'] = __('DPD címke','wc-pont');
if ( in_array ( 'fox', $show ) ) $actions['export_fox'] = __('FoxPost csomagregisztrálás','wc-pont');
if ( in_array ( 'fox', $show ) ) $actions['export_fox_print'] = __('FoxPost címkenyomtatás','wc-pont');
if ( in_array ( 'posta', $show ) ) $actions['export_posta'] = __('MPL címke','wc-pont');
if ( in_array ( 'eone', $show ) ) $actions['export_eone'] = __('Express One címke','wc-pont');
if ( in_array ( 'sprinter', $show ) ) $actions['export_sprinter'] = __('Sprinter címke','wc-pont');
if ( in_array ( 'csv', $show ) ) $actions['export_csv'] = __('Exportálás CSV-be','wc-pont'); // <option value="mark_awaiting_shipment">Mark awaiting shipment</option>
return $actions;
}
function pont_export_handle_bulk_action_edit_shop_order( $redirect_to,$action,$order_ids ) {
$allowed_actions = array( 'export_csv','export_mygls','export_csomagkuldo','export_dpd','export_fox','export_fox_print','export_sprinter','export_posta','export_eone');
if( !in_array( $action,$allowed_actions ) ) return $redirect_to;
$order_ids = apply_filters( 'woocommerce_wc_pont_export_orders',$order_ids );
if( empty( $order_ids ) ) return $redirect_to;
WC_Pont::log(sprintf( 'Export:	Order ids: %s Pont ver: %s PHP: %s WP ver: %s WooCommerce ver: %s',json_encode( $order_ids ), $GLOBALS['wc_pont']->version ?? '', phpversion(), $GLOBALS['wp_version'] ?? '', $GLOBALS['woocommerce']->version ?? ''),'info');
switch($action) {
case 'export_csv':
$filename = 'pont_'.date('Y-m-d_H-i').'.csv';
$ret = exportCSV( $order_ids,$filename );
$redirect_to = add_query_arg( array(
'exported'=>$ret,
'pont_file'=>$filename
),$redirect_to );
break;
case 'export_mygls':
require_once plugin_dir_path( __FILE__ ).'includes/'.'mygls.php';
$ret = exportGLS( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_csomagkuldo':
require_once plugin_dir_path( __FILE__ ).'includes/'.'csomagkuldo.php';
$ret = exportCsomagkuldo( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_fox':
require_once plugin_dir_path( __FILE__ ).'includes/'.'fox.php';
$ret = export_fox( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_fox_print':
require_once plugin_dir_path( __FILE__ ).'includes/'.'fox.php';
$ret = export_fox_print( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_sprinter':
require_once plugin_dir_path( __FILE__ ).'includes/'.'sprinter.php';
$ret = export_sprinter( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_dpd':
require_once plugin_dir_path( __FILE__ ).'includes/'.'dpd.php';
$ret = export_dpd( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_posta':
require_once plugin_dir_path( __FILE__ ).'includes/'.'posta.php';
$ret = export_posta( $order_ids );
$redirect_to = add_query_arg( array('exported'=>$ret,'ids'=>join(',',$order_ids) ),$redirect_to );
break;
case 'export_eone':
require_once plugin_dir_path( __FILE__ ).'includes/'.'eone.php';
$ret = export_eone( $order_ids );
$redirect_to = add_query_arg( array('exported' => $ret, 'ids' => join(',', $order_ids) ), $redirect_to );
break;
default:
return $redirect_to;
}
return $redirect_to;
}
public function wc_pont_add_meta_box( $post_type,$post ) {
$order = wc_get_order( $post->ID );
if( $order ) {
$meta = $order->get_meta( 'shipping_carrier');
if ( $meta )
add_meta_box( 'wc_pont_metabox',__('Csomagkövetés','wc-pont'),array( $this,'render_meta_box_content'),'shop_order','side','default');
}
}
public function render_meta_box_content( $post ) {
$html = $this->get_trackink_info( $post->ID );
echo $html;
}
public function get_trackink_info( $post_id ) {
$pn = get_post_meta( $post_id, 'shipping_parcel_number', true );
if ( ! empty ( $pn ) ){
$carrier = get_post_meta( $post_id, 'shipping_carrier', true );
$carrier = explode ( '|', $carrier );
$status = get_post_status( $post_id );
$disabled = apply_filters( 'wc_pont_status_exclude_tracking', get_option('wc_pont_tracking_exclusion', ['wc-completed','wc-refunded','wc-cancelled','wc-failed']) );
if ( in_array( $status, $disabled ) ){
$meta_tracking = get_post_meta( $post_id, '_tracking_data', true );
if ( 'gls' === $carrier[0] )
$url = WC_Pont::$trk_urls[ 'gls' ][ $carrier[1] ] . '/tt_page_xml.php?pclid=' . $pn;
else
$url = sprintf( WC_Pont::$trk_urls[ $carrier[0] ], $pn );
if ( !empty( $meta_tracking ) ) {
return sprintf(
'<a class="%s s%s" target="_blank" title="%s" href="%s" data-info="Meta">%s</a>', $carrier[0], $meta_tracking->code,
$meta_tracking->date,
$url, $meta_tracking->event
);
} else
return sprintf(
'<a class="%s" target="_blank" href="%s" data-info="No Meta">%s</a>',
$carrier[0],
$url,
__( 'Nem volt adat', 'wc-pont' )
);
}
switch ( $carrier[0] ) {
case 'pick':{
require_once plugin_dir_path( __FILE__ ).'includes/'.'sprinter.php';
return get_sprinter_tracking_info( $pn, $post_id )[0];
break;
}
case 'dpd':{
require_once plugin_dir_path( __FILE__ ).'includes/'.'dpd.php';
return get_dpd_tracking_info( $pn, $post_id )[0];
break;
}
case 'fox':{
require_once plugin_dir_path( __FILE__ ).'includes/'.'fox.php';
return get_fox_tracking_info_web( $pn, $post_id )[0];
break;
}
case 'cskuldo-hu';
case 'cskuldo':{
require_once plugin_dir_path( __FILE__ ).'includes/'.'csomagkuldo.php';
return getCsomagkuldoTrackingInfo( $pn, $post_id )[0];
break;
}
case 'postacs';
case 'posta':{
require_once plugin_dir_path( __FILE__ ).'includes/'.'posta.php';
return get_posta_tracking_info( $pn, $post_id )[0];
break;
}
case 'eone':{
require_once plugin_dir_path( __FILE__ ).'includes/eone.php';
return get_eone_tracking_info( $pn, $post_id )[0];
break;
}
case 'gls':{
include_once( 'includes/mygls.php' );
return get_gls_tracking_info( $pn, $post_id )[0];
break;
}
default:{
return '';
}
}
$pontMeta = get_post_meta( $post_id, 'wc_selected_pont', true );
$pontMeta 	= explode( '|', $pontMeta);
if ( isset( $pontMeta[1] ) && $this->wc_pont_is_csomagkuldo( $pontMeta[1] ) != false ) {
require_once plugin_dir_path( __FILE__ ).'includes/'.'csomagkuldo.php';
return getCsomagkuldoTrackingInfo( $pn, $post_id )[0];
}
}
}
public function wc_pont_order_action( $actions ) {
$actions['export_mygls'] = __('MyGLS címke','wc-pont');
return $actions;
}
public function wc_pont_order_action_export_mygls( $order ) {
$order_id = $order->get_id();
require_once plugin_dir_path( __FILE__ ).'includes/'.'mygls.php';
$ret = exportGLS( array ( $order_id ) );
WC_Pont::log(
'MyGLS	order action ret: '.json_encode( $ret,JSON_UNESCAPED_UNICODE ),
'info'
);
$keys = WC_Pont::get_trans( 'pont_pdf');
if( !empty( $keys ) &&is_array( $keys ) ) {
$baseUrl = wp_upload_dir()['baseurl'].'/wc_pont/';
$shipping_carrier = WC_Pont::get_trans( 'pont_carrier');
$pont_pcls = WC_Pont::get_trans( 'pont_pcls');
$pont_pdf2 = WC_Pont::get_trans( 'pont_pdf2');
if ( 'gls'== $shipping_carrier  ){
$glsCountry = get_option( 'wc_pont_gls_sender_country','HU');
$shipping_carrier .= '|'.$glsCountry;
$trkURL = WC_Pont::$trk_urls['gls'][$glsCountry ].'/tt_page.php?tt_value=%s';
}
else
$trkURL = WC_Pont::$trk_urls[$shipping_carrier ];
foreach( $keys as $k =>$v ) {
$pcls_link = sprintf( '<a href="'.$trkURL.'" target="_blank">%s</a>',$pont_pcls[$k],$pont_pcls[$k] );
$pdf_deliveri_link = '';
if ( !empty( $pont_pdf2 ) ){
foreach ( $pont_pdf2 as $pdf2 ) {
$pdf_deliveri_link .= '<a href="'.$baseUrl .$pdf2 .'" target="_blank">'.__( 'Szállítólevél','wc-pont') .'</a> ';
}
}
$order = wc_get_order( $k );
$pdf .= '<p>#'.$order->get_order_number()  .': <a href="'.$baseUrl .$v .'" target="_blank">'.$v .'</a> '.$pdf_deliveri_link .$pcls_link .'</p>';
update_post_meta( $k,'shipping_parcel_number',esc_attr( $pont_pcls[$k] ) );
update_post_meta( $k,'shipping_carrier',esc_attr(  $shipping_carrier ) );
}
$pont_pdfCombied = WC_Pont::get_trans( 'pont_pdfCombied');
if( !empty( $pont_pdfCombied ) ){
$pdf .= '<p>Egyben: <a href="'.$baseUrl .$pont_pdfCombied .'" target="_blank">'.$pont_pdfCombied .'</a> </p>';
}
}
WC_Pont::log(
'MyGLS	order action pdf: '.json_encode( $pdf,JSON_UNESCAPED_UNICODE ),
'info'
);
$adminnotice = new WC_Admin_Notices();
WC_Admin_Notices::add_custom_notice( 'MyGLS',sprintf( __( 'Címke: %s','wc-pont'),$pdf ) );
WC_Admin_Notices::output_custom_notices();
}
public function wc_pont_order_hide_shipping_address( $hide ) {
$hide[] = 'wc_pont_shipping_method';
return $hide;
}
public function wc_pont_my_account_my_orders_columns( $cols ) {
$new_cols = array();
foreach ( $cols as $key =>$name ) {
$new_cols[$key ] = $name;
if ( 'order-status'=== $key ) {
$new_cols['order-tracking'] = __( 'Csomagkövetés','wc-pont');
}
}
return $new_cols;
}
public function wc_pont_my_orders_tracking_column_data( $order ) {
echo $this->get_trackink_info( $order->get_id() );
}
public static function update_order_status( $id ) {
if ( function_exists( 'wc_seq_order_number_pro' ) )
$id = wc_seq_order_number_pro()->find_order_by_order_number( $id );
$order = wc_get_order( $id );
$order->update_status( get_option( 'wc_pont_export_order_status') );
WC_Pont::log(
'Update	order status'.
' new status: '.get_option( 'wc_pont_export_order_status') .' id: '.$id ,
'info'
);
}
public static function get_package_weight( $order ) {
$weight = 0;
foreach( $order->get_items() as $item ) {
$product = wc_get_product( $item->get_product_id() );
$variation = wc_get_product( $item->get_variation_id() );
$pw = ( $product->is_type( 'variable' ) ) ? $variation->get_weight() : $product->get_weight();
$weight += $item['qty']*$pw;
}
if ( 'kg'== get_option('woocommerce_weight_unit') )
$weight *= 1000;
return $weight;
}
public function wc_pont_posta_page( ) {
?>
<div class="wrap">
<h1 class="wp-heading-inline"><?php _e( 'MPL csomagok', 'wc-pont' ) ?></h1>
<a class="page-title-action" href="<?php echo admin_url( 'admin.php?page=posta' ) ;?>"><?php _e( 'Frissít', 'wc-pont' ) ?></a>
<hr class="wp-header-end">
<?php
if ( !empty( $_REQUEST['token'] ) ) : delete_transient( 'pont_posta_token' ); ?>
<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
<p><strong><?php _e('Token törölve', 'wc-pont' ) ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
</div>
<?php endif;
include_once( 'includes/posta.php' );
if ( !empty( $_REQUEST['pn'] ) ) {
WC_Pont::log(
'Posta 	page delete: '. implode ( ' ', $_REQUEST['pn'] ),
'info'
);
$result = delete_posta_package( $_REQUEST['pn'] );
foreach ( $result as $k => $v ) {
echo $k . ' ' . $v . '<br>' ;
}
}
$packages = get_posta_shipments();
if ( !empty( $_REQUEST['close'] ) ) :
$nonce = isset( $_REQUEST[ '_wpnonce' ] ) ? wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'PostaClose' ) : false;
if ( ! $nonce ){
wp_die();
}
WC_Pont::log(
'Posta	page close',
'info'
);
posta_shipments_close();
$redirect = add_query_arg( array( 'page' => 'posta', 'success' => 'close', '_wpnonce' => wp_create_nonce( 'PostaClose' ) ), admin_url('admin.php') );
wp_redirect( $redirect );
wp_die();
endif;
if ( !empty( $_REQUEST['success'] ) ) :
WC_Pont::log(
'Posta	page close cuccess packages: '. $packages,
'info'
);
$pont_pdf2 = WC_Pont::get_trans( 'pont_pdf2' );
if ( is_array( $pont_pdf2 ) ) :
$baseUrl = wp_upload_dir()['baseurl'].'/wc_pont/';
?>
<a href="<?php echo $baseUrl . $pont_pdf2[0];?>" target="_blank">
<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 450 540" ><path d="M267.342 414.698c-6.613 0-10.884.585-13.413 1.165v85.72c2.534.586 6.616.586 10.304.586 26.818.2 44.315-14.576 44.315-45.874.2-27.216-15.745-41.597-41.206-41.597zm-114.505-.385c-6.022 0-10.104.58-12.248 1.16v38.686c2.53.58 5.643.78 9.903.78 15.757 0 25.47-7.973 25.47-21.384.001-12.05-8.362-19.242-23.126-19.242zm322.258-282.32c-.032-2.526-.833-5.02-2.568-6.993L366.324 3.694c-.02-.034-.062-.045-.084-.076-.633-.707-1.36-1.3-2.14-1.804l-.718-.422a11.07 11.07 0 0 0-2.13-.892l-.58-.192c-.8-.194-1.634-.308-2.468-.308H97.2C85.292 0 75.6 9.693 75.6 21.6v507.6c0 11.913 9.692 21.6 21.6 21.6h356.4c11.908 0 21.6-9.688 21.6-21.6v-396c-.001-.406-.064-.804-.106-1.2zM193.26 463.873c-10.104 9.523-25.072 13.806-42.57 13.806-3.882 0-7.4-.2-10.102-.58v46.84h-29.35V394.675c9.13-1.55 21.967-2.72 40.047-2.72 18.267 0 31.292 3.5 40.036 10.494 8.363 6.612 13.985 17.497 13.985 30.322 0 12.835-4.266 23.72-12.047 31.103zm125 44.52c-13.785 11.464-34.778 16.906-60.428 16.906-15.36 0-26.238-.97-33.637-1.94V394.675c10.887-1.74 25.083-2.72 40.046-2.72 24.867 0 41.004 4.472 53.645 13.995 13.6 10.1 22.164 26.24 22.164 49.37-.01 25.08-9.145 42.378-21.8 53.073zm121.32-91.167h-50.35v29.932h47.04v24.1h-47.04v52.67H359.5V392.935h80.082v24.3zM97.2 366.752V21.6h250.203v110.515a10.8 10.8 0 0 0 10.8 10.8H453.6V366.75H97.2zm289.005-134.617c-.633-.06-15.852-1.448-39.213-1.448-7.32 0-14.7.143-21.97.417-46.133-34.62-83.92-69.267-104.148-88.684.37-2.138.623-3.828.74-5.126 2.668-28.165-.298-47.18-8.786-56.515-5.558-6.1-13.72-8.13-22.233-5.806-5.286 1.385-15.07 6.513-18.204 16.952-3.46 11.536 2.1 25.537 16.708 41.773.232.246 5.2 5.44 14.196 14.24-5.854 27.913-21.178 88.148-28.613 117.073-17.463 9.33-32.013 20.57-43.277 33.465l-.738.844-.477 1.013c-1.16 2.437-6.705 15.087-2.542 25.25 1.9 4.62 5.463 7.995 10.302 9.767l1.297.35s1.17.253 3.227.253c9 0 31.25-4.735 43.18-48.695l2.9-11.138c41.64-20.24 93.688-26.768 131.415-28.587 19.406 14.4 38.717 27.6 57.428 39.318l.6.354c.907.464 9.112 4.515 18.72 4.524h0c13.732 0 23.762-8.427 27.496-23.113l.2-1.004c1.044-8.393-1.065-15.958-6.096-21.872-10.598-12.458-30.33-13.544-32.104-13.604zm-243.393 87.6c-.084-.1-.124-.194-.166-.3-.896-2.157.18-7.4 1.76-11.222 6.792-7.594 14.945-14.565 24.353-20.84-9.162 29.658-22.486 32.222-25.948 32.363zm58.172-197.05h0c-14.07-15.662-13.86-23.427-13.102-26.04 1.242-4.37 6.848-6.02 6.896-6.035 2.824-.768 4.538-.617 6.064 1.058 3.45 3.8 6.415 15.232 5.244 36.218l-5.102-5.2zm-7.27 133.373l.243-.928-.032.01 23.047-93.95.2.2.02-.124c18.9 17.798 47.88 43.83 82.58 70.907l-.4.016.574.433c-32.688 2.76-71.73 9.205-106.253 23.433zm214.672 9.052c-2.5 9.146-7.277 10.396-11.665 10.396h0c-5.094 0-9.998-2.12-11.116-2.632-12.74-7.986-25.776-16.688-38.93-25.998h.316c22.55 0 37.568 1.37 38.158 1.4 3.766.14 15.684 1.9 20.82 7.938 2.014 2.367 2.785 5.196 2.416 8.885z"/></svg>
<?php esc_html_e( 'Szállítólevél', 'wc-pont' ) ?></a>
<?php endif; ?>
<?php endif; ?>
<?php if ( $packages > 0 ) : ?>
<form action="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'posta', 'close' => 'shipments' ), admin_url( 'admin.php' ) ), 'PostaClose' );?>" method="POST">
<?php submit_button( __( 'Jegyzék zárása', 'wc-pont' ) ); ?>
</form>
</div>
<?php
endif;
}
public function wc_pont_cimke_page( ) {
?>
<div class="wrap">
<h1 class="wp-heading-inline"><?php _e( 'Címkék', 'wc-pont' ) ?></h1>
<hr class="wp-header-end">
<?php
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
$wp_filesystem = new WP_Filesystem_Direct(null);
if ( $dirlist = $wp_filesystem->dirlist( ABSPATH . '/wp-content/uploads/wc_pont/', false, false ) ){
array_multisort (array_column( $dirlist, 'lastmodunix'), SORT_DESC, SORT_LOCALE_STRING, $dirlist);
foreach ((array) $dirlist as $filename => $fileinfo) {
if ( 'f' == $fileinfo['type'] ) {
$dl_url = wp_upload_dir()['baseurl'].'/wc_pont/'. $filename;
echo sprintf( '%s <a href="%s">%s</a><br>', date( 'Y-m-d H:i', $fileinfo['lastmodunix'] ), esc_url($dl_url), $filename );
}
}
}
?>
</div>
<?php
}
public function wc_pont_removable_query_args( $args ) {
	$args[] = 'exported';
	return $args;
}
}
$GLOBALS['wc_pont'] = new WC_Pont();
function wc_pont_uninstall_callback( $settings ) {
delete_option( 'wc_pont_licencekey');
}
function calcCost( $orderPrice,$costp )
{
if(strstr($costp,PHP_EOL)){
$costp = explode("\n",$costp);
foreach ( $costp as $option ) {
$this_option = array_map( 'trim',explode( WC_DELIMITER,$option ) );
if ( sizeof( $this_option ) !== 2 ) continue;
if ( $orderPrice <= $this_option[0 ] ) {
$costs[] = $this_option[1 ];
}
}
}else{
$this_option = array_map( 'trim',explode( WC_DELIMITER,$costp ) );
if ( sizeof( $this_option ) == 2 )
if ( $orderPrice <= $this_option[0 ] )
$costs[] = $this_option[1 ];
}
if ( empty( $costs ))
$costs[] = 0;
return $costs;
}
function wc_pont_add_shipping_method( $methods )
{
$methods['wc_pont_shipping_method'] = new WC_Pont_Shipping_Method();
return $methods;
}
function shipping_override_default_address_fields( $address_fields )
{
$address_fields['state']['required'] = false;
$address_fields['address_2']['required'] = false;
return $address_fields;
}
function extend_woocommerce_view_order($order_id){
$pontMeta = get_post_meta( $order_id,'wc_selected_pont',true );
if ( $pontMeta ) {
$pontMeta = WC_Pont::wc_pont_format( $pontMeta );
;echo '		<h2>';_e( 'A kiválasztott átvevőhely:','wc-pont');;echo '</h2>
		<address>
			';echo $pontMeta;;echo '		</address>
	';
}
return true;
}
function wc_pont_admin_enqueue_script($hook) {
if ( 'edit.php'!= $hook )
return;
wp_enqueue_script( 'jqueryfileDownload','//cdnjs.cloudflare.com/ajax/libs/jquery.fileDownload/1.4.2/jquery.fileDownload.min.js');
}
add_action( 'admin_enqueue_scripts','wc_pont_admin_enqueue_script');
function wc_pont_admin_notice_messages() {
global $pagenow,$typenow;
if( isset( $_REQUEST['exported'])
&&$_REQUEST['exported'] >0
&&$typenow == 'shop_order'
&&$pagenow == 'edit.php'
&&$_REQUEST['exported']
) {
WC_Pont::log(
'Export	Notice:'.
json_encode( $_REQUEST ),
'info'
);
$count = intval( $_REQUEST['exported'] );
$link = $script = '';
$baseUrl = wp_upload_dir()['baseurl'].'/wc_pont/';
if ( isset( $_REQUEST['pont_file'] ) ){
$url 	= $baseUrl.$_REQUEST['pont_file'];
$link 	= "Letölthető: <a href='{$url}'>{$_REQUEST['pont_file']}</a>";
$script = '<script type="text/javascript">
							jQuery(document).ready(function($) {
								jQuery.fileDownload("'.$url.'")
						});</script>';
}
$pdf='';
$keys = WC_Pont::get_trans( 'pont_pdf');
if( !empty( $keys ) &&is_array( $keys ) ) {
$shipping_carrier = WC_Pont::get_trans( 'pont_carrier');
$pont_pcls = WC_Pont::get_trans( 'pont_pcls');
$pont_pdf2 = WC_Pont::get_trans( 'pont_pdf2');
if ( 'gls'== $shipping_carrier  ){
$glsCountry = get_option( 'wc_pont_gls_sender_country','HU');
$shipping_carrier .= '|'.$glsCountry;
$trkURL = WC_Pont::$trk_urls['gls'][$glsCountry ].'/tt_page.php?tt_value=%s';
}
else
$trkURL = WC_Pont::$trk_urls[$shipping_carrier ];
foreach( $keys as $k =>$v ) {
$pcls_link = sprintf( '<a href="'.$trkURL.'" target="_blank">%s</a>',$pont_pcls[$k],$pont_pcls[$k] );
$pdf_deliveri_link = '';
if ( !empty( $pont_pdf2 ) ){
foreach ( $pont_pdf2 as $pdf2 ) {
$pdf_deliveri_link .= '<a href="'.$baseUrl .$pdf2 .'" target="_blank">'.__( 'Szállítólevél','wc-pont') .'</a> ';
}
}
$order = wc_get_order( $k );
$pdf .= '<p>#'. $order->get_order_number() .': <a href="'.$baseUrl .$v .'" target="_blank">'.$v .'</a> '.$pdf_deliveri_link .$pcls_link .'</p>';
update_post_meta( $k,'shipping_parcel_number',esc_attr( $pont_pcls[$k] ) );
update_post_meta( $k,'shipping_carrier',esc_attr(  $shipping_carrier ) );
}
$pont_pdfCombied = WC_Pont::get_trans( 'pont_pdfCombied');
if( !empty( $pont_pdfCombied ) )
$pdf .= '<p>Egyben: <a href="'.$baseUrl .$pont_pdfCombied .'" target="_blank">'.$pont_pdfCombied .'</a> </p>';
if ( 'posta' == $shipping_carrier && 'yes' === get_option( 'wc_pont_posta_close' ) )
$pdf .= '<p><a href="'. admin_url( 'admin.php?page=posta' ) .'">'. __( 'MPL csomagok', 'wc-pont' ) .'</a> </p>';
}
$nonce = wp_create_nonce( 'pont-fox');
$ids = WC_Pont::get_trans( 'pont_fox_reg');
$print = WC_Pont::get_trans( 'pont_fox_print');
if( !empty( $ids ) &&is_array( $ids ) ) {
$jsLang = array(
__( 'nem feladott','wc-pont'),
__( 'másodperc','wc-pont'),
__( 'hiba oka','wc-pont'),
'https://www.foxpost.hu/csomagkovetes?code=',
);
$jsLang = json_encode( $jsLang,JSON_UNESCAPED_UNICODE );
$link = '#'.implode( ', ',$ids  ) .' '.__( 'Várjunk Foxpost válaszárá...','wc-pont') .'<br>';
foreach( $ids as $k ) {
$pdf .= "<span id='fox$k'>
							#$k</span> ";
}
$ids = json_encode ( $ids );
$script .=<<<JS
<script type="text/javascript">
var fox_reg={$ids}, fox_counter=0, langa ={$jsLang};
jQuery(document).ready(function($){
function recursively_ajax(){
var data = {action: "fox_action", security: "{$nonce}", fox: fox_reg};
$.post(ajaxurl, data, function(response){
fox_counter++;
j = $.parseJSON(response);
if (j.error != 0 && fox_counter < 24){
setTimeout(recursively_ajax, 5000)
}
for (var i = 0;i < j.items.length;i++){
id = j.items[i].id;
if (j.items[i].response == 0){
$("#fox" + id).html("#" + id + " " + fox_counter * 5 + " " + langa[1]);
$("#cb-select-" + id).css("border-bottom", "solid 2px orange")
}
else if (j.items[i].error == 0){
$("#fox" + id).html("#" + id + " " + langa[0] + " " + j.items[i].pn);
$("#cb-select-" + id).attr("checked", true).css("border-bottom", "solid 2px green")
}
else {
$("#fox" + id).html("#" + id + " " + langa[2] +": " + j.items[i].error);
$("#cb-select-" + id).css("border-bottom", "solid 2px red")
}
}
})
}
recursively_ajax();
setTimeout(function(){
$("#bulk-action-selector-top").val("export_fox_print")
}, 100)
});
</script>
JS;
}
elseif( !empty( $print ) &&is_array( $print ) ) {
$jsLang = array(
__( 'feladva','wc-pont'),
__( 'másodperc','wc-pont'),
__( 'hiba oka','wc-pont'),
__( 'címke elkészült','wc-pont'),
$baseUrl
);
$jsLang = json_encode($jsLang,JSON_UNESCAPED_UNICODE);
$link = '#'.implode( ', ',$print ) .' '.__( 'Várjunk FoxPost válaszárá...','wc-pont') .'<br>';
foreach( $print as $k ) {
$pdf .= "<span id='fox$k'>
							#$k</span> ";
}
$ids = json_encode ( $print );
$script .=<<<JS
<script type="text/javascript">
var fox_print={$ids}, fox_counter=0, langa ={$jsLang};
jQuery(document).ready(function($){
function recursively_ajax(){
var data = {action: "fox_action", security: "{$nonce}", fox: fox_print};
$.post(ajaxurl, data, function(response){
fox_counter++;
j = $.parseJSON(response);
if (j.error != 0 && fox_counter < 24){
setTimeout(recursively_ajax, 5000)
}
else {
console.log("Pont finish: " + fox_counter * 5 + " " + langa[1] + " " + response)
}
for (var i = 0;i < j.items.length;i++){
id = j.items[i].id;
if (j.items[i].response == 0){
$("#fox" + id).html("#" + id + " " + fox_counter * 5 + " " + langa[1]);
$("#cb-select-" + id).css("border-bottom", "solid 2px orange")
}
else if (j.items[i].error == 0){
$("#fox" + id).html("#" + id + " " + langa[0] + " <a>" + j.items[i].pdf + "</a>").children().attr({"target": "_blank",
"href": langa[4] + j.items[i].pdf});
$("#cb-select-" + id).css("border-bottom", "solid 2px green")
}
else {
$("#fox" + id).html("#" + id + " " + langa[2] +": " + j.items[i].error);
$("#cb-select-" + id).css("border-bottom", "solid 2px red")
}
}
})
}
recursively_ajax()
});
</script>
JS;
}
printf( '<div class="updated fade"><p>'
._n( 'Feldolgozva %s rendelés. %s','Feldolgozva %s rendelés. %s',$count,'wc-pont')
.'</p>%s</div>%s',$count,$link,$pdf,$script );
}
$keys = WC_Pont::get_trans( 'pont_errors');
if( !empty( $keys ) &&is_array( $keys ) ) {
foreach( $keys as $k =>$v) {
echo sprintf( "<div class='error'><p>#%s: %s</p></div>",$k,$v );
}
}
}
add_action( 'admin_notices','wc_pont_admin_notice_messages');
function exportCSV($order_ids,$filename) {
$csv = fopen('php://temp/maxmemory:'.(1*1024*1024),'r+');
$csvHeader = 'sorszam,nev,iranyitoszam,telepules,tomeg,erteknyilvanitas,arufizetes,szolgaltatasok,ugyfeladat1,ugyfeladat2,email,telefon,cimzett_kozterulet_nev,cimzett_kozterulet_jelleg,cimzett_kozterulet_hsz,megjegyzes,kezbesito_hely,meretX,meretY,meretZ,masolatok_szama,inverz_masolat';
fputcsv($csv,explode(',',$csvHeader),';');
$sorszam = 1;
foreach( $order_ids as $order_id ) {
$order = wc_get_order( $order_id );
$pontMeta = false;
if ( !empty( get_post_meta( $order_id,'wc_selected_pont',true ) ) ){
$pontMeta = explode( '|',get_post_meta( $order_id,'wc_selected_pont',true ) );
}
$weight = WC_Pont::get_package_weight($order);
$c = array();
$c[] = $sorszam;
$nev = '';
if( $order->shipping_company ) {
$nev .= $order->shipping_company.' ';
}
$c[] = $nev .$order->shipping_first_name.' '.$order->shipping_last_name;
$c[] = $order->shipping_postcode;
$c[] = $order->shipping_city;
$c[] = $weight;
$c[] = $order->order_total;
$c[] = $order->payment_method == 'cod'?$order->order_total : 0;
$szolgaltatasok = $order->payment_method == 'cod'?'UVT': '';
$c[] = $szolgaltatasok;
$c[] = $order->get_order_number();
$c[] = '';
$c[] = $order->billing_email;
$c[] = formatPhone($order->billing_phone);
$c[] = $order->shipping_address_1;
$c[] = $order->shipping_address_1;
$c[] = '';
$c[] = '';
$c[] = $pontMeta ?$pontMeta[0] : '';
$c[] = '';
$c[] = '';
$c[] = '';
$c[] = '';
$c[] = '';
$c[] = $order->shipping_country;
do_action( 'woocommerce_'.'wc-pont'.'_export_csv_data',$c,$order,$pontMeta );
fputcsv($csv,$c,';');
if ( get_option( 'wc_pont_export_mod_status') == 'yes'){
$order->update_status( get_option( 'wc_pont_export_order_status') );
}
$sorszam++;
}
$filename = trailingslashit( WC_Pont::wc_pont_upload_dir() ).$filename;
rewind($csv);
$outCharset = get_option( 'wc_pont_csv_code','UTF-8').'//IGNORE';
$cont = iconv( 'UTF-8',$outCharset,stream_get_contents($csv) ) ;
file_put_contents( $filename,$cont );
fpassthru($csv);
fclose($csv);
return $sorszam-1;
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.$filename);
header('Content-Type: text/csv charset=UTF-8');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Transfer-Encoding: binary');
rewind($csv);
fpassthru($csv);
fclose($csv);
return $sorszam-1;
}
function formatPhone($num) {
$num = preg_replace(['/[^0-9]/','/^06/'],'',$num);
$len = strlen($num);
if($len == 8) $num = preg_replace('/(?:1)?([0-9]{2})([0-9]{2})([0-9]{3})/','+36$1$2$3$4',$num);
elseif($len == 9) $num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{2})([0-9]{2})/','+36$1$2$3$4',$num);
elseif($len == 10) $num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{2})([0-9]{2})/','+$1$2$3$4',$num);
elseif($len == 11) $num = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{3})/','+$1$2$3$4',$num);
return $num;
}
function _sLog( $message ) {
$time = date("[Y-m-d H:i:s] ");
$tmp = array_keys(debug_backtrace());
$d = array_shift($tmp);
if (is_array($message))
$message = json_encode($message);
file_put_contents( __DIR__.'/pont.log',"$time \t".$d['function'].'#'.$d['line'] ."\t $message \n",FILE_APPEND );
add_action ('wp_print_footer_scripts',function() use ($message){
echo "<script>console.log('{$message}')</script>";
});
}
add_filter( 'woocommerce_admin_order_actions','add_pdf_order_actions_button',10,2 );
function add_pdf_order_actions_button( $actions,$order ) {
$upload_dir = wp_upload_dir()['basedir'].'/wc_pont/';
$pdfFile  = $upload_dir.$order->get_id().'.pdf';
if ( file_exists( $pdfFile ) ){
$dl_url = wp_upload_dir()['baseurl'].'/wc_pont/'.$order->get_id().'.pdf';
$actions['download'] = array(
'url'=>esc_url( $dl_url ),
'name'=>__( 'Címke letöltése','wc-pont'),
'action'=>"view download",
);
}
return $actions;
}
add_action( 'admin_head','wc_pont_add_custom_order_status_actions_button_css');
function wc_pont_add_custom_order_status_actions_button_css() {
echo '<style>.view.download::after { font-family: woocommerce; content: "\f316" !important; }</style>';
}
add_action( 'init','wc_pont_schedule_event');
function wc_pont_schedule_event() {
if (!wp_next_scheduled ( 'wc_pont_daily_delete_event')) {
wp_schedule_event( time(),'daily','wc_pont_daily_delete_event');
}
}
add_action( 'wc_pont_daily_delete_event','delete_daily');
function delete_daily() {
$days = get_option( 'wc_pont_delete_uploads',0 );
$dirUpload = wp_upload_dir()['basedir'].'/wc_pont';
if ( is_dir ( $dirUpload ) &&0 <$days ) {
$tempFiles = new FilesystemIterator( wp_upload_dir()['basedir'].'/wc_pont');
foreach( $tempFiles as $file ) {
if ( time() -$file->getCTime() >= 60 * 60 * 24 * $days)
unlink( $file->getPathname() );
}
}
}
register_deactivation_hook(__FILE__,'wc_pont_schedule_deactivation');
function wc_pont_schedule_deactivation() {
wp_clear_scheduled_hook('wc_pont_daily_delete_event');
wp_clear_scheduled_hook('wc_pont_status_updater');
}
if ( is_admin() ) {
add_action( 'wp_ajax_fox_action','fox_action_function');
}
function fox_action_function() {
check_ajax_referer( 'pont-fox','security');
global $wpdb;
$fox =  $_POST['fox'] ;
$resp = (object) array( 'error'=>0,'items'=>array () );
foreach ( $fox as $i ) {
$status = $wpdb->get_var( "SELECT $wpdb->postmeta.meta_value FROM $wpdb->postmeta  WHERE $wpdb->postmeta.post_id = $i AND $wpdb->postmeta.meta_key = 'fox_status'");
if ( 'reqreg'=== $status ) {
$resp->items[] = array (
'id'=>$i,
'response'=>0,
);
$resp->error ++;
}
elseif ( 'notposted'=== $status ) {
$pn = $wpdb->get_var( "SELECT $wpdb->postmeta.meta_value FROM $wpdb->postmeta  WHERE $wpdb->postmeta.post_id = $i AND $wpdb->postmeta.meta_key = 'shipping_parcel_number'");
$resp->items[] = array (
'id'=>$i,
'pn'=>$pn,
'response'=>1,
'error'=>0,
);
}elseif ( 'reqprint'=== $status ) {
$resp->items[] = array (
'id'=>$i,
'response'=>0,
);
$resp->error ++;
}elseif ( 'printed'=== $status ) {
$pdf = $wpdb->get_var( "SELECT $wpdb->postmeta.meta_value FROM $wpdb->postmeta  WHERE $wpdb->postmeta.post_id = $i AND $wpdb->postmeta.meta_key = 'fox_pdf'");
$resp->items[] = array (
'id'=>$i,
'pdf'=>$pdf,
'response'=>1,
'error'=>0,
);
}else {
$resp->items[] = array (
'id'=>$i,
'response'=>1,
'error'=>$status,
);
$resp->error ++;
}
}
echo json_encode( $resp );
wp_die();
}
function admin_order_list_top_bar_button( $which ) {
global $typenow;
if ( 'shop_order'=== $typenow &&'top'=== $which ) {
;echo '        <div class="alignleft actions custom">
            <button type="submit" name="custom_" style="height:32px;" class="button" value="">
						';echo __( 'Napi zárás','woocommerce');;echo '</button>
        </div>
        ';
}
}
?>
