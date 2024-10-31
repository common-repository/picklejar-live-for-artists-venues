<?php
/**
 * PickleJar Live for Artists & Venues  - Elementor.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\PageBuilders;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Base\BaseController;

/**
 * Class PJElementor
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\PageBuilders
 */
class PJElementor extends BaseController {
	/**
	 * Function Construct.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'pj_init_elementor' ), 10, 1 );
	}

	/**
	 * Function init elementor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_init_elementor() {
		wp_enqueue_script( $this->js_identifier, $this->plugin_url . 'assets/js/index.js', array( 'jquery' ), '1.0.0', true );
	}
}
