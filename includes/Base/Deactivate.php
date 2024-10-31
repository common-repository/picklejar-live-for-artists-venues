<?php
/**
 * PickleJar Live for Artists & Venues  - Deactivate Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Deactivate
 *
 * @since 1.0.0
 * @package Picklejar\Base
 */
class Deactivate {
	/**
	 * Function Deactivate Plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function deactivate_plugin() {
		flush_rewrite_rules();
	}
}
