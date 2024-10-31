<?php
/**
 * PickleJar Live for Artists & Venues - Enqueue Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Enqueue
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Base
 */
class Enqueue extends BaseController {
	/**
	 * Function Register.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'init', array( $this, 'pj_theme_enqueue' ) );
	}

	/**
	 * Function Admin Enqueue.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_enqueue() {
		wp_localize_script( 'pj-js-dashboard', 'ajaxQuery', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_style( $this->style_id . 'icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', null, '5.4.4' );
		wp_enqueue_style( 'pj-plugin', $this->plugin_url . 'assets/css/style.css', null, '1.0.0' );
		wp_enqueue_style( 'pj-swiper', $this->plugin_url . 'assets/css/swiper.css', null, '9.1.0' );
		wp_enqueue_script( 'pj-swiper', $this->plugin_url . 'assets/js/swiper.min.js', array( 'jquery' ), '9.1.0', true );
	}

	/**
	 * Function Picklejar Theme Enqueue.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_theme_enqueue() {
		wp_enqueue_style( 'pj-plugin', $this->plugin_url . 'assets/css/style.css', null, '1.0.0' );
		wp_enqueue_style( 'pj-swiper', $this->plugin_url . 'assets/css/swiper.css', null, '9.1.0' );
		wp_enqueue_script( 'pj-modal-vanilla', $this->plugin_url . 'assets/js/modal-vanilla.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'pj-multiselect-dropdown', $this->plugin_url . 'assets/js/multiselect-dropdown.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'pj-swiper', $this->plugin_url . 'assets/js/swiper.min.js', array( 'jquery' ), '9.1.0', true );
		wp_enqueue_script( 'pj-datepicker', $this->plugin_url . 'assets/js/flatpickr.js', array( 'jquery' ), '4.1.4', true );
		wp_enqueue_script( $this->js_identifier, $this->plugin_url . 'assets/js/index.js', array( 'jquery' ), '1.0.0', true );

		if ( $this->is_elementor_active ) {
			wp_enqueue_style( 'pj-plugin-elementor', $this->plugin_url . 'assets/css/elementor.css', null, '1.0.0' );
		}
	}
}

