<?php
/**
 * PickleJar Live for Artists & Venues  - Dashboard Class.
 *
 * @since 1.0.0
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Api\Callbacks\ManagerCallbacks;
use Picklejar\Api\Callbacks\PJArtistPostTypeCallbacks;
use Picklejar\Api\SettingsApi;
use Picklejar\Base\BaseController;
use WP_Query;

/**
 * Class PJElementor
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Pages
 */
class Dashboard extends BaseController {
	/**
	 * Variable Settings.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var SettingsApi
	 */
	private $settings;

	/**
	 * Variable Callbacks.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var DashboardCallbacks
	 */
	private $callbacks;

	/**
	 * Variable Callbacks Manager.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var ManagerCallbacks
	 */
	private $callbacks_mngr;

	/**
	 * Variable Pages.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $pages = array();

	/**
	 * Variable Subpages.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $subpages = array();

	/**
	 * Variable Cookie Name.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $cookie_name = 'pj_validation_token';

	/**
	 * Function Register
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function register() {
		$this->settings = new SettingsApi();

		$this->callbacks = new DashboardCallbacks();

		$this->callbacks_mngr = new ManagerCallbacks();

		$this->set_pages();
		$this->set_settings();
		$this->set_sections();
		$this->set_fields();

		$this->settings->add_pages( $this->pages )->with_sub_page( 'Dashboard' )->register();
		$this->settings->add_sub_pages( $this->subpages );

		// Generate Admin ajax endpoints.
		add_action( 'wp_enqueue_scripts', array( $this, 'pj_generate_admin_endpoints' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'pj_generate_admin_endpoints' ) );
		add_action( 'wp_ajax_pj-login-step-2', array( $this->callbacks, 'pj_login_step2' ) );
		add_action( 'wp_ajax_pj-login-step-3', array( $this->callbacks, 'pj_login_step3' ) );
		add_action( 'wp_ajax_pj-login-step-4', array( $this->callbacks, 'pj_login_step4' ) );
		add_action( 'wp_ajax_pj-new-login', array( $this->callbacks, 'pj_success_login' ) );
		// Generate ajax endpoints.
		add_action( 'wp_enqueue_scripts', array( $this, 'pj_generate_endpoints' ), 99 );
		add_action( 'wp_ajax_pj-error-page', array( $this->callbacks, 'pj_error_page' ) );
		add_action( 'wp_ajax_nopriv_pj-error-page', array( $this->callbacks, 'pj_error_page' ) );
		add_action( 'init', array( $this, 'generate_cookie' ) );
	}

	/**
	 * Function Set Pages
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_pages() {
		$this->pages = array(
			array(
				'page_title' => 'PJ Integration',
				'menu_title' => 'PJ Integration',
				'capability' => 'manage_options',
				'menu_slug'  => 'picklejar_integration_plugin',
				'callback'   => array( $this->callbacks, 'pj_settings_dashboard' ),
				'icon_url'   => $this->plugin_url . '/assets/images/logo-icon.svg',
				'position'   => 110,
			),
		);
	}

	/**
	 * Function Get Option Name
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_name() {
		return 'picklejar_integration_plugin';
	}

	/**
	 * Function Get Option Page Name
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_page_name() {
		return 'picklejar_integration_plugin';
	}

	/**
	 * Function Get Option Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_group() {
		return 'picklejar_integration_plugin_dashboard';
	}

	/**
	 * Function Get Page Option Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_page_option_group() {
		return 'picklejar_integration_plugin_page_dashboard';
	}

	/**
	 * Function Set Settings
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_settings() {
		$args[] = array(
			'option_group' => $this->get_option_group(),
			'option_name'  => $this->get_option_name(),
			'callback'     => array( $this->callbacks_mngr, 'pj_settings_sanitize' ),
		);

		$args[] = array(
			'option_group' => $this->get_page_option_group(),
			'option_name'  => $this->get_option_page_name(),
			'callback'     => array( $this->callbacks_mngr, 'pj_settings_sanitize' ),
		);

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
				'id'       => 'picklejar_integration_admin_index',
				'title'    => 'Settings Manager',
				'callback' => array( $this->callbacks_mngr, 'pj_admin_section_manager' ),
				'page'     => 'picklejar_integration_plugin',
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

		$managers_tabs = '';
		$pages_tabs    = 'PickleJar Pages';

		$args[] = array(
			'id'       => 'pj_validation_token',
			'title'    => '',
			'callback' => array( $this->callbacks_mngr, 'text_field' ),
			'page'     => $this->callbacks->get_page(),
			'section'  => $this->callbacks->get_section(),
			'args'     => array(
				'hide_label'        => true,
				'option_name'       => $this->get_option_name(),
				'label_for'         => 'pj_validation_token',
				'class'             => 'picklejar-hidden',
				'type'              => 'hidden',
				'option_name_array' => 'settings',
			),
		);

		$args[] = array(
			'id'       => 'pj_entity_type',
			'title'    => '',
			'callback' => array( $this->callbacks_mngr, 'text_field' ),
			'page'     => $this->callbacks->get_page(),
			'section'  => $this->callbacks->get_section(),
			'args'     => array(
				'hide_label'        => true,
				'option_name'       => $this->get_option_name(),
				'label_for'         => 'pj_entity_type',
				'class'             => 'picklejar-hidden remove-on-load',
				'type'              => 'hidden',
				'option_name_array' => 'settings',
			),
		);

		$args[] = array(
			'id'       => 'pj_entity_id',
			'title'    => '',
			'callback' => array( $this->callbacks_mngr, 'text_field' ),
			'page'     => $this->callbacks->get_page(),
			'section'  => $this->callbacks->get_section(),
			'args'     => array(
				'hide_label'        => true,
				'option_name'       => $this->get_option_name(),
				'label_for'         => 'pj_entity_id',
				'class'             => 'picklejar-hidden',
				'type'              => 'hidden',
				'option_name_array' => 'settings',
			),
		);

		foreach ( $this->managers as $key => $title ) {
			$args[] = array(
				'id'       => $key,
				'title'    => $title,
				'callback' => array( $this->callbacks_mngr, 'manager_checkbox_field' ),
				'page'     => $this->callbacks->get_page(),
				'section'  => $this->callbacks->get_section(),
				'args'     => array(
					'option_name'       => $this->get_option_name(),
					'label_for'         => $key,
					'class'             => 'picklejar-ui-toggle',
					'option_name_array' => 'manager',
				),
			);
		}

		foreach ( $this->managers as $key => $title ) {
			$option = get_option( 'picklejar_integration_plugin' );

			if ( 'picklejar_events_layout_manager' === $key ) {
				$title = 'Event';
			}

			if ( 'picklejar_artist_layout_manager' === $key ) {
				$title = 'Artist';
			}

			$query_params = array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			);

			$query = new WP_Query( $query_params );
			$posts = $query->posts;

			$options = array();

			foreach ( $posts as $result ) {
				if ( isset( $result->ID ) && isset( $result->post_title ) ) {
					$options[ $result->ID ] = $result->post_title;
				}
			}

			$class        = 'picklejar-d-flex picklejar-d-column picklejar-' . strtolower( $title ) . '-page-title';
			$type         = 'text';
			$class_hidden = $class;

			if ( 'Artist' === $title && false === $this->callbacks->pj_enable_CTP() ) {
				$class_hidden .= ' picklejar-hidden';
				$type          = 'hidden';
			}

			$args[] = array(
				'id'       => $key . '_page_id',
				'title'    => $title . 's List Page:',
				'callback' => array( $this->callbacks_mngr, 'pj_select_field' ),
				'page'     => $this->callbacks->get_page(),
				'section'  => $this->callbacks->get_section(),
				'args'     => array(
					'label'             => '',
					'hide_label'        => true,
					'option_name'       => $this->get_option_page_name(),
					'label_for'         => $key . '_page_id',
					'class'             => $class_hidden,
					'type'              => $type,
					'option_name_array' => 'settings',
					'options'           => $options,
				),
			);

			$args[] = array(
				'id'       => $key . '_page_details_id',
				'title'    => $title . ' Details Page:',
				'callback' => array( $this->callbacks_mngr, 'pj_select_field' ),
				'page'     => $this->callbacks->get_page(),
				'section'  => $this->callbacks->get_section(),
				'args'     => array(
					'label'             => '',
					'hide_label'        => true,
					'option_name'       => $this->get_option_page_name(),
					'label_for'         => $key . '_page_details_id',
					'class'             => $class,
					'type'              => 'text',
					'option_name_array' => 'settings',
					'options'           => $options,
				),
			);
		}
		$this->settings->set_fields( $args );
	}

	/**
	 * Picklejar Generate Admin Endpoints
	 *
	 * @return   void
	 * @since    1.0.0
	 * @access   public
	 */
	public function pj_generate_admin_endpoints() {
		wp_localize_script(
			$this->js_identifier,
			'generate_login_process',
			array(
				'url'    => "$this->login_url/api/web/v1/new-login-flow/generate-login-process",
				'nonce'  => wp_create_nonce( 'generate-login-process-ajax-nonce' ),
				'action' => 'generate-login-process',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_login_step_2',
			array(
				'url'    => $this->ajax_call_url,
				'nonce'  => wp_create_nonce( 'pj-login-step-2-nonce' ),
				'action' => 'pj-login-step-2',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_login_step_3',
			array(
				'url'    => $this->ajax_call_url,
				'nonce'  => wp_create_nonce( 'pj-login-step-3-nonce' ),
				'action' => 'pj-login-step-3',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_login_step_4',
			array(
				'url'    => $this->ajax_call_url,
				'nonce'  => wp_create_nonce( 'pj-login-step-4-nonce' ),
				'action' => 'pj-login-step-4',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_new_login',
			array(
				'url'    => "$this->login_url/api/web/v1/new-login-flow/new-login",
				'nonce'  => wp_create_nonce( 'pj-new-login-nonce' ),
				'action' => 'pj-new-login',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_music_genre',
			array(
				'url'    => "$this->login_url/api/web/v1/music-auxiliar/music-genres",
				'nonce'  => wp_create_nonce( 'pj-music-genre-nonce' ),
				'action' => 'pj-music-genre',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_entities_list_url',
			array(
				'url'    => "$this->login_url/api/web/v2/helpers/available-logged-in-user-entities/list",
				'nonce'  => wp_create_nonce( 'pj-entities-list-nonce' ),
				'action' => 'pj-entities',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_entities_url',
			array(
				'url'    => "$this->login_url/api/web/v2/helpers/available-logged-in-user-entities/list",
				'nonce'  => wp_create_nonce( 'pj-entities-list-nonce' ),
				'action' => 'pj-entities',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_create_auth_login',
			array(
				'url'    => "$this->login_url/api/web/v2/connected-apps/create-auth-token",
				'nonce'  => wp_create_nonce( 'pj-create-auth-login-nonce' ),
				'action' => 'pj-create-auth-login',
			)
		);
	}

	/**
	 * Picklejar Generate Endpoints
	 *
	 * @return   void
	 * @since    1.0.0
	 * @access   public
	 */
	public function pj_generate_endpoints() {
		wp_localize_script(
			$this->js_identifier,
			'pj_error_page',
			array(
				'url'    => $this->ajax_call_url,
				'nonce'  => wp_create_nonce( 'pj-error-page-nonce' ),
				'action' => 'pj-error-page',
			)
		);
	}

	/**
	 * Picklejar Generate Cookie
	 *
	 * @return   void
	 * @since    1.0.0
	 * @access   public
	 */
	public function generate_cookie() {
		setcookie(
			$this->cookie_name,
			$this->callbacks->get_pj_access_token(),
			time() + 3600 * 180,
			'/'
		);
	}
}
