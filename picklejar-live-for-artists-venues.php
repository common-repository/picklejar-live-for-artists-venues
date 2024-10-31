<?php
/**
 * PickleJar Live for Artists & Venues.
 *
 * @package PickleJar Live for Artists & Venues
 */

/*
 * Plugin Name:       PickleJar Live for Artists & Venues
 * Description:       Connect your WordPress site with PickleJar.
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Picklejar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       picklejar-live-for-artists-venues
 */

require 'vendor/autoload.php';
defined( 'ABSPATH' ) || die( 'Somethig went wrong' );

$GLOBALS['wpmdb_meta']['picklejar-live-for-artists-venues']['version'] = '1.0.0';
$GLOBALS['picklejar_plugin_dir']                                       = plugin_dir_url( __FILE__ );
$GLOBALS['picklejar_img_dir'] = $GLOBALS['picklejar_plugin_dir'] . '/assets/images/';

if ( file_exists( dirname( __FILE__ ) . './vendor/autoload.php' ) ) {
	include_once dirname( __FILE__ ) . './vendor/autoload.php';
}

/**
 * Activate during plugin activation
 */
function activate_picklejar_integration_plugin() {
	Picklejar\Base\Activate::activate_plugin();
}

register_activation_hook( __FILE__, 'activate_picklejar_integration_plugin' );


/**
 * Deactivate during plugin activation
 */
function deactivate_picklejar_integration_plugin() {
	Picklejar\Base\Deactivate::deactivate_plugin();
}

register_deactivation_hook( __FILE__, 'deactivate_picklejar_integration_plugin' );


Picklejar\Init::register_services();

add_action( 'wp_ajax_nopriv_event-list', 'my_event_list_cb' );
add_action( 'wp_ajax_event-list', 'my_event_list_cb' );

/**
 * Function sanitize attributes
 *
 * @return array available attributes
 */
if ( ! function_exists( 'picklejar_sanitize_custom_attributes' ) ) {
	/**
	 * Sanitize attributes
	 *
	 * @return array available attributes
	 */
	function picklejar_sanitize_custom_attributes() {
		return array(
			'data-filter'        => array(),
			'data-default-value' => array(),
		);
	}
}
