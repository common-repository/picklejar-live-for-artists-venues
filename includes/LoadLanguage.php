<?php
/**
 * PickleJar Live for Artists & Venues Load Language Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class LoadLanguage
 *
 * @package Picklejar
 * @since 1.0.0
 */
class LoadLanguage {
	/**
	 * LoadLanguage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action(
			'init',
			array( $this, 'load_translation' )
		);
	}

	/**
	 * Load translation
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_translation() {
		load_plugin_textdomain(
			'picklejar-integration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
