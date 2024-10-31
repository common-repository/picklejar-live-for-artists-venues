<?php
/**
 * PickleJar Live for Artists & Venues Post Type Callbacks Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Api\Callbacks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Base\BaseController;

/**
 * Class PJArtistPostTypeCallbacks
 *
 * @since 1.0.0
 * @package Picklejar\Api\Callbacks
 * @extends BaseController
 */
class PJArtistPostTypeCallbacks extends BaseController {

	/**
	 * Function get page.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_page() {
		return 'pj_artists_page';
	}

	/**
	 * Function get option index.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_index() {
		return $this->get_page() . '_index';
	}

	/**
	 * Function get post type.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_type() {
		return 'pj_artists';
	}

	/**
	 * Function get nonce.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_nonce() {
		return $this->get_page() . '_nonce';
	}

	/**
	 * Function get section.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_section() {
		return $this->get_option_index();
	}

	/**
	 * Function get plugin key.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_plugin_key() {
		return '_' . $this->get_page() . '_key';
	}

	/**
	 * Function get menu slug.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_menu_slug() {
		return $this->get_page();
	}

	/**
	 * Function get option name.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_name() {
		return 'picklejar_integration_plugin_artist_post';
	}

	/**
	 * Function get option group.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_group() {
		return 'picklejar_integration_plugin_artist_post_group';
	}

	/**
	 * Function Picklejar Render Artist Preview.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_render_artist_preview() {
		require_once $this->plugin_path . 'manager/artist-layout/partials/pj-artist-preview.php';
	}

	/**
	 * Function get data.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_data() {
		return get_option( $this->get_option_name() );
	}

	/**
	 * Function get post meta data.
	 *
	 * @param integer $post_id The Post ID.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_meta_data( $post_id ) {
		return get_post_meta(
			$post_id,
			$this->get_plugin_key(),
			true
		);
	}

	/**
	 * Function short code page.
	 *
	 * @return mixed require_once
	 * @since 1.0.0
	 * @access public
	 */
	public function shortcode_page() {
		return require_once "$this->plugin_path/manager/dashboard/artists-shortcode-page.php";
	}
}

