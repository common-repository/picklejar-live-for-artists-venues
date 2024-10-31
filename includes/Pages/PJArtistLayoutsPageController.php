<?php
/**
 * PickleJar Live for Artists & Venues Artist - Layouts Page Controller.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Api\Callbacks\ManagerCallbacks;
use Picklejar\Api\Callbacks\PJArtistPageCallbacks;
use Picklejar\Api\SettingsApi;
use Picklejar\Base\BaseController;
use Picklejar\Models\Artist\PJArtistFilters;
use Picklejar\Models\Artist\PJArtistLayoutStyle;

/**
 * Class PJArtistLayoutsPageController
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Pages
 */
class PJArtistLayoutsPageController extends BaseController {
	/**
	 * Variable Callbacks
	 *
	 * @since 1.0.0
	 * @access private
	 * @var PJArtistPageCallbacks
	 */
	private $callbacks;

	/**
	 * Variable Dashboard Callbacks
	 *
	 * @since 1.0.0
	 * @access private
	 * @var DashboardCallbacks
	 */
	private $callbacks_adm;

	/**
	 * Variable Manager Callbacks
	 *
	 * @since 1.0.0
	 * @access private
	 * @var ManagerCallbacks
	 */
	private $callbacks_manager;

	/**
	 * Variable subpages
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $subpages = array();

	/**
	 * Variable model
	 *
	 * @since 1.0.0
	 * @access private
	 * @var PJArtistFilters
	 */
	public $model;

	/**
	 * Variable settings
	 *
	 * @since 1.0.0
	 * @access private
	 * @var SettingsApi
	 */
	public $settings;

	/**
	 * Variable default
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $default;

	/**
	 * Variable social_networks_list
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public array $social_networks_list;

	/**
	 * Function Register
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function register() {

		if ( ! $this->activated( 'picklejar_artist_layout_manager' ) ) {
			return;
		}

		$this->settings = new SettingsApi();

		$this->callbacks = new PJArtistPageCallbacks();

		$this->callbacks_adm = new DashboardCallbacks();

		$this->callbacks_manager = new ManagerCallbacks();

		$this->model = new PJArtistFilters();

		$this->set_subpages();
		$this->set_settings();
		$this->set_sections();
		$this->set_fields();
		$this->pj_artist_layout_template_shortcode();
		$this->pj_artist_list_shortcode();
		$this->settings->add_sub_pages( $this->subpages )->register();

		add_action( 'admin_enqueue_scripts', array( $this, 'load_media_files' ) );
		// Generate Endpoints.
		add_action( 'wp_enqueue_scripts', array( $this, 'pj_generate_artist_endpoints' ) );
		add_action( 'wp_ajax_get-artist-template', array( $this->callbacks, 'pjRenderArtistTemplate' ) );
	}

	/**
	 * Function Set Subpages
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_subpages() {
		if ( $this->callbacks_adm->pj_enable_CTP() ) {
			$this->subpages = array(
				array(
					'parent_slug' => 'picklejar_integration_plugin',
					'page_title'  => 'Picklejar Artist Layout',
					'menu_title'  => 'Artist Layout',
					'capability'  => 'manage_options',
					'menu_slug'   => $this->callbacks->get_menu_slug(),
					'callback'    => array( $this->callbacks_adm, 'pj_artist_layout_settings' ),
				),
			);
		}
	}

	/**
	 * Function Set Settings
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_settings() {
		$args     = array();
		$settings = array(
			'option_group' => $this->callbacks->get_option_group(),
			'option_name'  => $this->callbacks->get_option_name(), // 'picklejar_integration_plugin_events_configuration'
			'callback'     => array( $this->callbacks, 'pj_artist_layout_sanitize' ),
		);

		$type = array(
			'option_group' => $this->callbacks->get_option_group(),
			'name'         => 'type',
			'option_name'  => $this->callbacks->get_option_name(),
		);

		array_push( $args, $settings );
		array_push( $args, $type );

		foreach ( $this->callbacks->social_networks_list as $item ) :
			$value = array(
				'option_group' => $this->callbacks->get_option_group(),
				'name'         => strtolower( $item ),
				'option_name'  => $this->callbacks->get_option_name(),
			);
			array_push( $args, $value );
		endforeach;
		$this->settings->set_settings( $args );
	}

	/**
	 * Function Set Sections
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_sections() {
		$args = array(
			array(
				'id'       => 'picklejar_integration_artist_layouts_style_index',
				'title'    => 'Settings',
				'callback' => array( $this->callbacks, 'pj_layout_settings_section_manager' ),
				'page'     => $this->callbacks->get_page(),
			),
		);
		$this->settings->set_sections( $args );
	}

	/**
	 * Function Set Fields
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_fields() {
		$model_data = new PJArtistLayoutStyle();
		$fields     = $model_data->get_pj_artist_inputs_settings();
		$args       = array();
		foreach ( $fields as $key => $values ) {
			$args[ $key ] = array(
				'id'       => $key,
				'title'    => $values['title'] ?? '',
				'callback' => array( $this->callbacks_manager, $values['callback'] ),
				'page'     => $this->callbacks->get_page(),
				'section'  => $this->callbacks->get_section(),
				'args'     => $values,
			);
		}

		$this->settings->set_fields( $args );
	}

	/**
	 * Function Load Media Files
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function load_media_files() {
		wp_enqueue_media();
	}

	/**
	 * Function Picklejar Generate Artist Endpoints
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_generate_artist_endpoints() {
		wp_localize_script(
			$this->js_identifier,
			'pj_artist_template',
			array(
				'url'    => "$this->ajax_call_url",
				'nonce'  => wp_create_nonce( 'pj-get-artist-once' ),
				'action' => 'get-artist-template',
			)
		);
	}

	/**
	 * Function Picklejar Artist Layout Template Shortcode
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_layout_template_shortcode() {
		add_shortcode( 'pj_artist_layout_template', array( $this->callbacks, 'pj_artist_layout_template' ) );
	}

	/**
	 * Function Picklejar Artist List Shortcode
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_list_shortcode() {
		add_shortcode( 'pj_artist_list', array( $this->callbacks, 'pj_artist_list' ) );
	}
}
