<?php
/**
 * PickleJar Live for Artists & Venues - Base Controller Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class BaseController
 *
 * @since 1.0.0
 * @package Picklejar\Base
 */
class BaseController {

	protected const ARTIST_LAYOUT_MANAGER                      = 'picklejar_artist_layout_manager';
	protected const ARTIST_DEFAULT_PAGE_TITLE                  = 'PJ Artists';
	protected const ARTIST_DEFAULT_PAGE_SLUG                   = 'pj-artist';
	protected const ARTIST_DEFAULT_PAGE_TEMPLATE               = 'pj-artist-template';
	protected const ARTIST_DEFAULT_PAGE_TEMPLATE_VALUE         = 'PJ Artists Template';
	protected const ARTIST_DETAILS_DEFAULT_PAGE_TITLE          = 'Artist Details';
	protected const ARTIST_DETAILS_DEFAULT_PAGE_SLUG           = 'pj-artist-details';
	protected const ARTIST_DETAILS_DEFAULT_PAGE_TEMPLATE       = 'pj-artist-details-template';
	protected const ARTIST_DETAILS_DEFAULT_PAGE_TEMPLATE_VALUE = 'PJ Artists Details Template';

	protected const EVENT_LAYOUT_MANAGER                       = 'picklejar_events_layout_manager';
	protected const EVENTS_DEFAULT_PAGE_TITLE                  = 'On Stage';
	protected const EVENTS_DEFAULT_PAGE_SLUG                   = 'pj-on-stage';
	protected const EVENTS_DEFAULT_PAGE_TEMPLATE               = 'pj-events-template';
	protected const EVENTS_DEFAULT_PAGE_TEMPLATE_VALUE         = 'PJ Events Template';
	protected const EVENTS_DETAILS_DEFAULT_PAGE_TITLE          = 'Events Details';
	protected const EVENTS_DETAILS_DEFAULT_PAGE_SLUG           = 'pj-events-details';
	protected const EVENTS_DETAILS_DEFAULT_PAGE_TEMPLATE       = 'pj-event-details-template';
	protected const EVENTS_DETAILS_DEFAULT_PAGE_TEMPLATE_VALUE = 'PJ Event Details Template';


	/**
	 * Verify If Elementor is Active.
	 *
	 * @var bool
	 */
	protected $is_elementor_active = false;

	/**
	 * Plugin Path.
	 *
	 * @var string
	 */
	protected $plugin_path;

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	protected $plugin_url;

	/**
	 * Plugin.
	 *
	 * @var string
	 */
	protected $plugin;

	/**
	 * Plugin Images.
	 *
	 * @var string
	 */
	protected $plugin_images;

	/**
	 * Plugin Social Images.
	 *
	 * @var string
	 */
	protected $plugin_social_images;

	/**
	 * Managers.
	 *
	 * @var array|string[]
	 */
	protected array $managers = array();

	/**
	 * Social Networks List.
	 *
	 * @var array|string[]
	 */
	protected array $social_networks_list = array();

	/**
	 * Plugin Domain.
	 *
	 * @var string
	 */
	protected $plugin_domain;

	/**
	 * JS Identifier.
	 *
	 * @var string
	 */
	protected $js_identifier;

	/**
	 * Global Configuration.
	 *
	 * @var array|string[][]
	 */
	protected array $global_configuration = array(
		'color' => array(
			'primary'   => '#FDA901',
			'secondary' => '#1E0700',
		),
	);

	/**
	 * Style Id.
	 *
	 * @var string
	 */
	protected $style_id = 'pj-styles';

	/**
	 * Login Url
	 *
	 * @var string
	 */
	protected $login_url = 'https://api.pkle.live'; // "https://api.pkle.live";

	/**
	 * Ajax Call Url.
	 *
	 * @var string
	 */
	protected $ajax_call_url = '/wp-admin/admin-ajax.php';

	/**
	 * Template Layouts.
	 *
	 * @var string[]
	 */
	protected array $template_layouts;

	/**
	 * BaseController constructor.
	 */
	public function __construct() {
		$this->plugin_path          = plugin_dir_path( dirname( __FILE__, 2 ) );
		$this->plugin_url           = plugin_dir_url( dirname( __FILE__, 2 ) );
		$this->plugin               = plugin_basename( dirname( __FILE__, 3 ) ) . '/picklejar-integration-plugin.php';
		$this->plugin_images        = $this->plugin_url . 'images';
		$this->plugin_social_images = $this->plugin_images . '/social_networks/';
		$this->plugin_domain        = 'picklejar-integration';
		$this->js_identifier        = 'pj-js-dashboard';
		$this->social_networks_list = array(
			'Facebook'  => 'https://www.facebook.com/',
			'Instagram' => 'https://www.instagram.com/',
			'Linkedin'  => 'https://www.linkedin.com/in/',
			'Youtube'   => 'https://www.youtube.com/user/',
			'Twitter'   => 'https://www.twitter.com/',
		);
		$this->pj_is_elementor_enabled();
		$this->template_layouts = array(
			'Grid'   => array(
				'value' => 'grid',
				'icon'  => 'eicon-posts-grid',
			),
			'Slider' => array(
				'value' => 'slider',
				'icon'  => 'eicon-slider-push',
			),
		);

		$get_options = get_option( 'picklejar_integration_plugin' );
		if ( isset( $get_options ) && isset( $get_options['settings'] ) ) {
			$settings = get_option( 'picklejar_integration_plugin' )['settings'];
			if ( ! empty( $settings['pj_validation_token'] ) ) {
				$this->managers = array(
					'picklejar_events_layout_manager' => 'Activate Events Layout Manager',
					'picklejar_artist_layout_manager' => 'Activate Artist Layout Manager',
				);
				asort( $this->managers );
			}
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'pj_plugin_admin_enqueue' ) );
	}

	/**
	 * Picklejar Plugin Admin Enqueue.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_plugin_admin_enqueue() {
		wp_enqueue_style( $this->style_id, $this->plugin_url . 'assets/css/dashboard.css', null, '5.4.4' );
		wp_enqueue_script( 'pj-jquery_validate', $this->plugin_url . 'assets/js/jquery.validate.min.js', array( 'jquery' ), 'v1.19.5', true );
	}

	/**
	 * Activated.
	 *
	 * @param string $key Key.
	 *
	 * @return false
	 */
	protected function activated( $key ) {
		$post_data = get_option( 'picklejar_integration_plugin' );

		return isset( $post_data[ $key ] ) ? $post_data[ $key ] : false;
	}

	/**
	 * Function Get Categories.
	 *
	 * @param integer|null $post_id post id.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function get_categories( $post_id ) {
		$category_detail = get_the_category( $post_id );
		foreach ( $category_detail as $cd ) {
			echo wp_kses_post( $cd->cat_name );
		}
	}

	/**
	 * Function Get Top Category.
	 *
	 * @param integer|null $post_id The Post ID.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_top_category( $post_id ) {
		$cats        = get_the_category( $post_id ); // category object.
		$top_cat_obj = array();

		foreach ( $cats as $cat ) {
			if ( 0 === $cat->parent ) {
				$top_cat_obj[] = $cat;
			}
		}

		return $top_cat_obj;
	}

	/**
	 * Picklejar Disable Gutenberg for specific post type.
	 *
	 * @param string $current_status Status.
	 * @param string $post_type Post Type.
	 *
	 * @return false|mixed
	 */
	protected function prefix_disable_gutenberg(
		$current_status,
		$post_type
	) {
		// Use your post type key instead of 'product'.
		if ( 'product' === $post_type ) {
			return false;
		}

		return $current_status;
	}

	/**
	 * Function Render Event Grid Layout Field.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	protected function pj_render_event_grid_layout_field() {
		return include plugin_dir_path( __DIR__ ) . '../manager/events-layout/partials/pj-render-event-grid-layout-field.php';
	}

	/**
	 * Function Render Event Slider Layout Field.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	protected function render_event_slider_layout_field() {
		return include plugin_dir_path( __DIR__ ) . '../manager/events-layout/partials/pj-render-event-slider-layout-field.php';
	}

	/**
	 * Function Save Posts Data.
	 *
	 * @param mixed $post_data Post Data.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function save_posts_data( $post_data ) {
		$sanitized = array();
		foreach ( $post_data as $key => $detail ) {
			if ( is_array( $detail ) ) :
				foreach ( $detail as $field => $value ) :
					if ( is_array( $value ) ) :
						foreach ( $value as $field_child => $value_child ) :
							$sanitized[ $key ][ $field ][ $field_child ] = $this->parse_field( $field_child, $value_child );
						endforeach;
					else :
						$sanitized[ $key ][ $field ] = $this->parse_field( $field, $value );
					endif;
				endforeach;
			else :
				$sanitized[ $key ] = $this->parse_field( $key, $detail );
			endif;
		}

		return $sanitized;
	}

	/**
	 * Function Parse Field
	 *
	 * @param string $field Field.
	 * @param mixed  $value Value.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function parse_field(
		$field,
		$value
	) {
		return 'title_label' === $field ? sanitize_text_field( stripslashes( $value ) ) : sanitize_text_field( $value );
	}

	/**
	 * Function Picklejar Register Post Type.
	 *
	 * @param array $post_data The Post Data.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function pj_register_post_type( $post_data ) {
		$post_type = array(
			'name'                  => $post_data['plural_name'],
			'singular_name'         => $post_data['singular_name'],
			'menu_name'             => $post_data['plural_name'],
			'name_admin_bar'        => $post_data['singular_name'],
			'archives'              => $post_data['singular_name'] . ' Archives',
			'attributes'            => $post_data['singular_name'] . ' Attributes',
			'parent_item_colon'     => 'Parent ' . $post_data['singular_name'],
			'all_items'             => 'All ' . $post_data['plural_name'],
			'add_new_item'          => 'Add New ' . $post_data['singular_name'],
			'add_new'               => 'Add New',
			'new_item'              => 'New ' . $post_data['singular_name'],
			'edit_item'             => 'Edit ' . $post_data['singular_name'],
			'update_item'           => 'Update ' . $post_data['singular_name'],
			'view_item'             => 'View ' . $post_data['singular_name'],
			'view_items'            => 'View ' . $post_data['plural_name'],
			'search_items'          => 'Search ' . $post_data['singular_name'],
			'not_found'             => 'No ' . $post_data['singular_name'] . ' found',
			'not_found_in_trash'    => 'No ' . $post_data['singular_name'] . ' found in trash',
			'featured_image'        => 'Featured Image',
			'set_featured_image'    => 'Set Feature Image',
			'remove_featured_image' => 'Remove Feature image',
			'use_featured_image'    => 'Use Feature image',
			'insert_into_item'      => 'Insert into ' . $post_data['singular_name'],
			'uploaded_to_this_item' => 'Upload to this ' . $post_data['singular_name'],
			'items_list'            => $post_data['plural_name'] . ' List',
			'items_list_navigation' => $post_data['singular_name'] . ' List',
			'filter_items_list'     => 'Filter ' . $post_data['plural_name'] . ' List',
			'label'                 => $post_data['singular_name'],
			'description'           => $post_data['plural_name'] . ' Custom Post Type',
			'supports'              => $post_data['supports'],
			'show_in_rest'          => $post_data['show_in_rest'] ?? true,
			'taxonomies'            => $post_data['taxonomies'] ?? array( 'post' ),
			'rewrite'               => $post_data['rewrite'] ?? null,
			'hierarchical'          => $post_data['hierarchical'] ?? false,
			'public'                => $post_data['public'] ?? true,
			'show_ui'               => $post_data['show_ui'] ?? true,
			'show_in_menu'          => $post_data['show_ui'] ?? true,
			'menu_position'         => $post_data['menu_position'] ?? 20,
			'show_in_admin_bar'     => $post_data['show_ui'] ?? true,
			'show_in_nav_menus'     => $post_data['show_ui'] ?? true,
			'can_export'            => $post_data['show_ui'] ?? true,
			'has_archive'           => $post_data['show_ui'] ?? true,
			'exclude_from_search'   => $post_data['exclude_from_search'] ?? false,
			'publicly_queryable'    => $post_data['publicly_queryable'] ?? true,
			'capability_type'       => $post_data['capability_type'],
		);

		$args = array(
			'labels'              => array(
				'name'                  => $post_type['name'],
				'singular_name'         => $post_type['singular_name'],
				'menu_name'             => $post_type['menu_name'],
				'name_admin_bar'        => $post_type['name_admin_bar'],
				'archives'              => $post_type['archives'],
				'attributes'            => $post_type['attributes'],
				'parent_item_colon'     => $post_type['parent_item_colon'],
				'all_items'             => $post_type['all_items'],
				'add_new_item'          => $post_type['add_new_item'],
				'add_new'               => $post_type['add_new'],
				'new_item'              => $post_type['new_item'],
				'edit_item'             => $post_type['edit_item'],
				'update_item'           => $post_type['update_item'],
				'view_item'             => $post_type['view_item'],
				'view_items'            => $post_type['view_items'],
				'search_items'          => $post_type['search_items'],
				'not_found'             => $post_type['not_found'],
				'not_found_in_trash'    => $post_type['not_found_in_trash'],
				'featured_image'        => $post_type['featured_image'],
				'set_featured_image'    => $post_type['set_featured_image'],
				'remove_featured_image' => $post_type['remove_featured_image'],
				'use_featured_image'    => $post_type['use_featured_image'],
				'insert_into_item'      => $post_type['insert_into_item'],
				'uploaded_to_this_item' => $post_type['uploaded_to_this_item'],
				'items_list'            => $post_type['items_list'],
				'items_list_navigation' => $post_type['items_list_navigation'],
				'filter_items_list'     => $post_type['filter_items_list'],
			),
			'label'               => $post_type['label'],
			'menu_icon'           => $post_data['icon'],
			'description'         => $post_type['description'],
			'supports'            => $post_type['supports'],
			'show_in_rest'        => $post_type['show_in_rest'],
			'taxonomies'          => $post_type['taxonomies'] ?? null,
			'rewrite'             => $post_type['rewrite'],
			'hierarchical'        => $post_type['hierarchical'],
			'public'              => $post_type['public'],
			'show_ui'             => $post_type['show_ui'],
			'show_in_menu'        => $post_type['show_in_menu'],
			'menu_position'       => $post_type['menu_position'],
			'show_in_admin_bar'   => $post_type['show_in_admin_bar'],
			'show_in_nav_menus'   => $post_type['show_in_nav_menus'],
			'can_export'          => $post_type['can_export'],
			'has_archive'         => $post_type['has_archive'],
			'exclude_from_search' => $post_type['exclude_from_search'],
			'publicly_queryable'  => $post_type['publicly_queryable'],
			'capability_type'     => $post_type['capability_type'],
		);
		register_post_type( $post_data['post_type'], $args );
	}

	/**
	 * Function Picklejar Get Missing Image.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function pj_get_missing_image() {
		return plugin_dir_url( dirname( __FILE__, 2 ) ) . '/assets/images/missing_image.png';
	}

	/**
	 * Function Picklejar Get Avatar Image.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function pj_get_avatar_image() {
		return plugin_dir_url( dirname( __FILE__, 2 ) ) . '/assets/images/avatar_img.png';
	}

	/**
	 * Function Picklejar Get Event Container Background Image.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function pj_get_event_container_background_image() {
		return plugin_dir_url( dirname( __FILE__, 2 ) ) . '/assets/images/event_bg_img.png';
	}

	/**
	 * Function Generate Default Page.
	 *
	 * @param string       $page_title Page Title.
	 * @param string       $default_slug Default Slug.
	 * @param string       $template_id Template Id.
	 * @param mixed        $content Content.
	 * @param string       $type Type.
	 * @param integer|null $update_page Update Page.
	 * @param integer|null $new_page_id New Page Id.
	 * @param integer|null $parent_id Parent Id.
	 *
	 * @return bool
	 */
	protected function generate_default_page(
		$page_title,
		$default_slug,
		$template_id,
		$content = '',
		$type = '',
		$update_page = false,
		$new_page_id = null,
		$parent_id = null
	) {
		$option = get_option( 'picklejar_integration_plugin' );

		if ( ! empty( $new_page_id ) ) {
			$this->pj_trigger_update_post( $new_page_id, $template_id );

			return $new_page_id;
		}

		$obj_page = get_page_by_path( $default_slug, 'OBJECT', 'page' );

		if ( ! empty( $obj_page ) && true === $update_page ) {
			$this->pj_trigger_update_post( $obj_page->ID, $template_id );
			$this->pj_update_picklejar_integration_option( $type, $option, $obj_page->ID );

			return $obj_page->ID;
		}

		if ( true === $update_page ) {
			$slug        = $default_slug;
			$new_page_id = wp_insert_post(
				array(
					'comment_status' => 'close',
					'ping_status'    => 'close',
					'post_author'    => 1,
					'post_title'     => ucwords( $page_title ),
					'post_name'      => $slug,
					'post_status'    => 'publish',
					'post_content'   => $content,
					'post_type'      => 'page',
					'post_parent'    => $parent_id,
				)
			);

			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
				update_post_meta( $new_page_id, '_wp_page_template', $template_id );
				$this->pj_update_picklejar_integration_option( $type, $option, $new_page_id );

			}
		}

		return $new_page_id;
	}


	/**
	 * Function Picklejar Update Post Page.
	 *
	 * @param string       $default_page_title Default Page Title.
	 * @param string       $default_slug Default Slug.
	 * @param integer|null $template_id Template Id.
	 * @param string       $type Type.
	 * @param mixed        $content Content.
	 * @param integer|null $stored_event_page_id Stored Event Page Id.
	 * @param integer|null $new_page_id New Page Id.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function pj_update_post_page(
		$default_page_title,
		$default_slug,
		$template_id,
		$type,
		$content,
		$stored_event_page_id,
		$new_page_id
	) {
		$trigger_update           = false;
		$result_saved_events_page = null;

		if ( ! empty( $stored_event_page_id ) ) {
			$result_saved_events_page = get_post( $stored_event_page_id )->ID ?? null;
		}

		if ( ( empty( $new_page_id ) && empty( $stored_event_page_id ) && null === $result_saved_events_page ) || ! empty( $new_page_id ) || empty( get_post( $stored_event_page_id ) ) ) {
			$trigger_update = true;
		}

		if ( true === $trigger_update ) {
			$this->generate_default_page(
				$default_page_title,
				$default_slug,
				$template_id,
				$content,
				$type,
				true,
				$new_page_id,
				null
			);
		}
	}

	/**
	 * Function Picklejar TriggerUpdate Post.
	 *
	 * @param integer|null $post_id Post Id.
	 * @param integer|null $template_id Template Id.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function pj_trigger_update_post(
		$post_id,
		$template_id
	) {
		$time        = strtotime( 'today' );
		$update_post = array(
			'ID'            => $post_id,
			'post_status'   => 'publish',
			'post_date'     => date( 'Y-m-d H:i:s', $time ),
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $time ),
		);
		$success     = wp_update_post( $update_post );

		if ( $success && ! is_wp_error( $success ) ) {
			update_post_meta(
				$post_id,
				'_wp_page_template',
				$template_id
			);
		}
	}

	/**
	 * Function Picklejar Update Picklejar Integration Option.
	 *
	 * @param string       $type Type.
	 * @param array        $option Option.
	 * @param integer|null $post_id Post Id.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function pj_update_picklejar_integration_option(
		$type,
		$option,
		$post_id
	) {
		if ( ! empty( $type ) && isset( $option['settings'] ) ) {
			$option['settings'][ $type ] = $post_id;
			update_option( 'picklejar_integration_plugin', $option );
		}
	}


	/**
	 * Function Disable Trash For Page.
	 *
	 * @param string       $page_title Page Title.
	 * @param integer|null $template_id Template Id.
	 *
	 * @return int|void
	 */
	protected function disable_trash_for_page(
		$page_title,
		$template_id
	) {
		$obj_page = get_page_by_title( $page_title, 'OBJECT', 'page' );

		if ( ! empty( $obj_page ) && 'Events' === $page_title ) {
			return $obj_page->ID;
		}
	}

	/**
	 * Function Remove Row Actions Post.
	 *
	 * @param array $actions Array of actions.
	 *
	 * @return mixed|void
	 */
	protected function remove_row_actions_post( $actions ) {
		if ( get_post_type() === 'post' ) {
			unset( $actions['clone'] );
			unset( $actions['trash'] );

			return $actions;
		}
	}

	/**
	 * Function Remove Row Actions Page.
	 *
	 * @param array $actions Array of actions.
	 *
	 * @since 1.0.0
	 */
	protected function remove_row_actions_page( $actions ) {

	}

	/**
	 * Function Picklejar Is Elementor Enabled.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function pj_is_elementor_enabled() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$this->is_elementor_active = is_plugin_active( 'elementor/elementor.php' );
	}

	/**
	 * Function Picklejar Elementor Enabled Clear Parameter.
	 *
	 * @param string $parameter Parameter.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	protected function pj_elementor_enabled_clear_parameter( $parameter ) {
		return $this->is_elementor_active ? null : $parameter;
	}
}
