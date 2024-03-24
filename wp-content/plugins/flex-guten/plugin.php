<?php
/**
 * Plugin Name:       Flex Guten - A Multipurpose Gutenberg Blocks Plugin
 * Description:       Flex Guten comes with all necessary gutenberg blocks like pinterest save button, pin it button, share button.
 * Requires at least: 6.0
 * Requires PHP:      5.7
 * Version:           1.1.2
 * Author:            Drag WP
 * Author URI:        https://dragwp.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       flexguten
 *
 * @package           @wordpress/create-block 
 */

 /**
  * @package Zero Configuration with @wordpress/create-block
  *  [flexguten] && [FLEXGUTEN] ===> Prefix
  */

// Stop Direct Access 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blocks Final Class
 */
if (!class_exists('FLEXGUTEN_BLOCKS_CLASS')) {

	final class FLEXGUTEN_BLOCKS_CLASS {

		public $assets;

		public function __construct() {

			// Define Constants
			$this->flexguten_define_constants();					

			// Blocks Categories
			if( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
				add_filter( 'block_categories', [ $this, 'flexguten_register_block_category' ], 10, 2 );
			} else {
				add_filter( 'block_categories_all', [ $this, 'flexguten_register_block_category' ], 10, 2 );
			}

			require 'vendor/autoload.php';

			// Enqueue Block Assets
			add_action( 'enqueue_block_assets', [ $this, 'flexguten_external_libraries' ] );
			add_filter( 'clean_url', [ $this, 'flexguten_js_config' ] );
			$this->assets = new Dwp\Assets(); 

			// Block Initialization
			new Dwp\Blocks\BlockRegister\BlockRegister();

		}

		/**
		 * Initialize the plugin
		 */

		public static function init(){
			static $instance = false; 
			if( ! $instance ) {
				$instance = new self();
			}
			return $instance;
		}

		/**
		 * Define the plugin constants
		 */
		private function flexguten_define_constants() {			
			define( 'FLEXGUTEN_VERSION', '1.0.0' );
			define( 'FLEXGUTEN_URL', plugin_dir_url( __FILE__ ) );
			define( 'FLEXGUTEN_PATH',  __DIR__ );
			define( 'FLEXGUTEN_INC_URL', FLEXGUTEN_URL . 'includes/' );			
		
		}

		/**
		 * Register Block Category
		 */

		public function flexguten_register_block_category( $categories, $post ) {
			return array_merge(
				array(
					array(
						'slug'  => 'flex-guten',
						'title' => __( 'Flex Guten', 'flexguten' ),
					),
				),
				$categories,
			);
		}

		/**
		 * Enqueue Block Assets
		 */
		public function flexguten_external_libraries() {

			// Block Assets
			$this->assets->enqueue_assets(); 

			// enqueue JS
			
			if( has_block( 'flexguten/pinterest-style-one' )){
				if(!is_admin()){
					wp_enqueue_script('flexguten-pinit-script');
				}				
				wp_enqueue_style( 'flexguten-merriweather-font');
				wp_enqueue_style( 'flexguten-proximanova-font');
				wp_enqueue_style( 'flexguten-sharpsans-font');
			}

			if(has_block( 'flexguten/amazon-review-one' )){	
				
				if(!is_admin()){
					wp_enqueue_script( 'flexguten-rater-script');
					wp_enqueue_script( 'flexguten-plugin-script');
				}			

				wp_enqueue_style('flexguten-nunito-font');
			}
		}

		function flexguten_js_config($url) {
			if (FALSE === strpos($url, 'pinit') || FALSE === strpos($url, '.js') || FALSE === strpos($url, 'pinterest.com')) {
				"pinit.js";
				return $url;
				}
	
			$return_string = "' async defer'";
			return $url . $return_string;
		}

	}
}
/**
 * Kickoff
*/

FLEXGUTEN_BLOCKS_CLASS::init();
