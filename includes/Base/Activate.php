<?php
/**
 * PickleJar Live for Artists & Venues - Activate Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Activate
 *
 * @since 1.0.0
 * @package Picklejar\Base
 */
class Activate {
	/**
	 * Function activate_plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function activate_plugin() {
		flush_rewrite_rules();

		$default = array();

		$plugin_managers = array(
			'picklejar_integration_plugin',
			'picklejar_events_layout_manager',
			'picklejar_artist_layout_manager',
		);

		foreach ( $plugin_managers as $plugin ) :
			if ( ! get_option( $plugin ) ) {
				update_option( $plugin, $default );
			}
		endforeach;
	}
}
