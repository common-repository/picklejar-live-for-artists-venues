<?php
/**
 * PickleJar Live for Artists & Venues Init Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Init.
 *
 * @package Picklejar
 * @since 1.0.0
 */
final class Init {

	/**
	 * Store all classes in the array.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_services() {
		return array(
			Pages\Dashboard::class,
			Pages\PJEventsLayoutsPageController::class,
			Pages\PJArtistLayoutsPageController::class,
			Base\Enqueue::class,
			Base\PJEventsCustomPostTypeController::class,
			Base\PJArtistCustomPostTypeController::class,
			PageBuilders\PJElementor::class,
		);
	}

	/**
	 * Loop through classes, initialize them and call the register() method if exists.
	 */
	public static function register_services() {
		foreach ( self::get_services() as $class ) {
			$service = self::instanciate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Register instances of the classes.
	 *
	 * @param class $class from the services array.
	 *
	 * @return class instance new Instance of the class.
	 */
	private static function instanciate( $class ) {
		return new $class();
	}
}


new LoadLanguage();
