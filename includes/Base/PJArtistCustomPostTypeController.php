<?php
/**
 * PickleJar Live for Artists & Venues - Artist Custom Post Type Controller Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Api\Callbacks\ManagerCallbacks;
use Picklejar\Api\Callbacks\PJArtistPostTypeCallbacks;
use Picklejar\Api\SettingsApi;
use Picklejar\Models\Artist\PJArtistFilters;
use Picklejar\Models\Artist\PJArtistLayoutStyle;
use WP_Post;

/**
 * Class PJArtistCustomPostTypeController
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Base
 */
class PJArtistCustomPostTypeController extends BaseController {

	/**
	 * Variable Callbacks.
	 *
	 * @since 1.0.0
	 * @var PJArtistPostTypeCallbacks
	 */
	public $callbacks;

	/**
	 * Variable Manager Callbacks.
	 *
	 * @since 1.0.0
	 * @var ManagerCallbacks
	 */
	public $manager_callbacks;

	/**
	 * Variable Settings.
	 *
	 * @var SettingsApi
	 */
	public $settings;

	/**
	 * Variable Model Artist.
	 *
	 * @since 1.0.0
	 * @var PJArtistFilters
	 */
	public $model_artist;

	/**
	 * Variable Model Artist Settings.
	 *
	 * @since 1.0.0
	 * @var PJArtistLayoutStyle
	 */
	public $model_artist_settings;

	/**
	 * Variable Dashboard Callbacks.
	 *
	 * @since 1.0.0
	 * @var DashboardCallbacks
	 */
	public $dashboard_callbacks;

	/**
	 * Function Register.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		if ( ! $this->activated( self::ARTIST_LAYOUT_MANAGER ) ) {
			return;
		}

		$this->settings = new SettingsApi();

		$this->callbacks = new PJArtistPostTypeCallbacks();

		$this->manager_callbacks = new ManagerCallbacks();

		$this->model_artist = new PJArtistFilters();

		$this->model_artist_settings = new PJArtistLayoutStyle();

		$this->dashboard_callbacks = new DashboardCallbacks();

		add_action( 'init', array( $this, 'pj_create_default_artist_details_page' ) );

		if ( $this->dashboard_callbacks->pj_enable_CTP() ) {

			add_action( 'init', array( $this, 'pj_artist_ctp' ) );

			add_action( 'init', array( $this, 'pj_create_default_artists_page' ) );

			add_action( 'admin_init', array( $this, 'add_artist_settings_metaboxes_sidebar' ) );

			add_action( 'admin_init', array( $this, 'add_artist_parameters_metaboxes_sidebar' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_section_before_meta_boxes' ) );

			add_action( 'save_post', array( $this, 'save_meta_box' ) );
		}

		$this->set_shortcode_page();

		// Register custom templates.
		add_filter( 'theme_page_templates', array( $this, 'pj_artist_page_template_register' ), 10, 3 );
		add_filter( 'template_include', array( $this, 'pj_artist_page_template_list_select' ), 99 );

		add_filter( 'manage_' . $this->callbacks->get_post_type() . '_posts_columns', array( $this, 'add_shortcode_colum' ), 10, 2 );
		add_action( 'manage_' . $this->callbacks->get_post_type() . '_posts_custom_column', array( $this, 'show_column' ), 10, 2 );

		// Generate ajax endpoints.
		add_action( 'wp_enqueue_scripts', array( $this, 'pj_generate_endpoints' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'pj_generate_endpoints' ) );
		add_filter( 'the_content', array( $this, 'pjReplaceArtistPageContentWithShortcode' ), 999 );
	}

	/**
	 * Function Picklejar Replace Artist Page Content With ShortCode
	 *
	 * @param mixed $content The Content.
	 *
	 * @return mixed The Content.
	 * @since 1.0.0
	 */
	public function pjReplaceArtistPageContentWithShortcode( $content ) {
		$event_page_id = (string) $this->dashboard_callbacks->pj_get_artist_page_id();
		$current_id    = (string) get_the_ID();
		if ( $current_id === $event_page_id && is_singular() && is_main_query() ) {
			return do_shortcode( '[pj_artist_list]' );
		}

		return $content;
	}

	/**
	 * Function Picklejar Page Template List Select
	 *
	 * @return array The Page Template List.
	 * @since 1.0.0
	 */
	public function pj_artist_page_template_list() {
		return array(
			self::ARTIST_DEFAULT_PAGE_TEMPLATE         => self::ARTIST_DEFAULT_PAGE_TEMPLATE_VALUE,
			self::ARTIST_DETAILS_DEFAULT_PAGE_TEMPLATE => self::ARTIST_DETAILS_DEFAULT_PAGE_TEMPLATE_VALUE,
		);
	}

	/**
	 * Function Picklejar Page Template Register
	 *
	 * @param array   $page_templates The Page Templates.
	 * @param string  $theme The Theme.
	 * @param WP_Post $post post.
	 *
	 * @return array The Page Templates.
	 * @since 1.0.0
	 */
	public function pj_artist_page_template_register(
		$page_templates,
		$theme,
		$post
	) {
		$templates = $this->pj_artist_page_template_list();

		foreach ( $templates as $key => $template ) {
			$page_templates[ $key ] = $template;
		}

		return $page_templates;
	}

	/**
	 * Function Picklejar Page Template List Select
	 *
	 * @param string $template The Template.
	 *
	 * @return mixed|string The Template.
	 * @since 1.0.0
	 */
	public function pj_artist_page_template_list_select( $template ) {
		global $post;

		$page_temp_slug = get_page_template_slug( $post->ID );
		$templates      = $this->pj_artist_page_template_list();

		if ( isset( $templates[ $page_temp_slug ] ) ) {
			$template = plugin_dir_path( dirname( __FILE__, 2 ) ) . 'templates/' . $page_temp_slug . '.php';
		}

		return $template;
	}

	/**
	 * Function Set Shortcode Page
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function set_shortcode_page() {
		$subpage = array(
			array(
				'parent_slug' => 'edit.php?post_type=' . $this->callbacks->get_post_type(),
				'edit_title'  => 'Shortcodes',
				'page_title'  => 'Artists',
				'menu_title'  => 'Shortcodes',
				'capability'  => 'manage_options',
				'menu_slug'   => 'pj_artist_shortcode',
				'callback'    => array( $this->callbacks, 'shortcode_page' ),
			),
		);

		$this->settings->add_sub_pages( $subpage )->register();
	}

	/**
	 * Function Picklejar Enable Artist Custom Post Type.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_artist_ctp() {
		$option = array(
			'post_type'           => $this->callbacks->get_post_type(),
			'singular_name'       => 'PJ Artists',
			'plural_name'         => 'PJ Artists',
			'icon'                => $this->plugin_url . '/assets/images/artist-icon.svg',
			'supports'            => array( 'title' ),
			'capability_type'     => 'page',
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'public'              => false,
			'show_in_rest'        => true,
			'rewrite'             => false,
			'show_ui'             => true,
			'query_var'           => false,
		);

		$this->pj_register_post_type( $option );
	}

	/**
	 * Function Picklejar Get Artist Page Default Title
	 *
	 * @return string The Title.
	 * @since 1.0.0
	 */
	private static function pj_get_artist_page_default_title() {
		return self::ARTIST_DEFAULT_PAGE_TITLE;
	}

	/**
	 * Function Picklejar Get Artist Page Default Slug
	 *
	 * @return string The Slug.
	 * @since 1.0.0
	 */
	private static function pj_get_artist_page_default_slug() {
		return self::ARTIST_DEFAULT_PAGE_SLUG;
	}

	/**
	 * Function Picklejar Get Artist Page Page Template
	 *
	 * @return string The Template.
	 * @since 1.0.0
	 */
	private static function pj_get_artist_page_template() {
		return self::ARTIST_DEFAULT_PAGE_TEMPLATE;
	}

	/**
	 * Function Picklejar Get Artist Details Page Default Title
	 *
	 * @return string The Title.
	 * @since 1.0.0
	 */
	private static function pj_get_artist_details_page_default_title() {
		return self::ARTIST_DETAILS_DEFAULT_PAGE_TITLE;
	}

	/**
	 * Function Picklejar Get Artist Details Page Default Slug
	 *
	 * @return string The Slug.
	 * @since 1.0.0
	 */
	private static function pj_get_artist_page_details_default_slug() {
		return self::ARTIST_DETAILS_DEFAULT_PAGE_SLUG;
	}

	/**
	 * Function Picklejar Get Artist Page Details Template
	 *
	 * @return string The Template.
	 * @since 1.0.0
	 */
	private static function pj_get_artist_page_details_template() {
		return self::ARTIST_DETAILS_DEFAULT_PAGE_TEMPLATE;
	}

	/**
	 * Function Picklejar Create Default Artists Page
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_create_default_artists_page() {
		$default_page_title   = $this->pj_get_artist_page_default_title();
		$default_slug         = $this->pj_get_artist_page_default_slug();
		$template_id          = $this->pj_get_artist_page_template();
		$type                 = 'picklejar_artist_layout_manager_page_id';
		$stored_event_page_id = $this->dashboard_callbacks ? $this->dashboard_callbacks->pj_get_artist_page_id() : '';
		$content              = '[pj_artist_list]';
		$this->pj_update_post_page( $default_page_title, $default_slug, $template_id, $type, $content, $stored_event_page_id, null );
	}

	/**
	 * Function Picklejar Create Default Artist Details Page
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_create_default_artist_details_page() {
		$default_page_title   = $this->pj_get_artist_details_page_default_title();
		$default_slug         = $this->pj_get_artist_page_details_default_slug();
		$template_id          = $this->pj_get_artist_page_details_template();
		$type                 = 'picklejar_artist_layout_manager_page_details_id';
		$stored_event_page_id = $this->dashboard_callbacks ? $this->dashboard_callbacks->pj_get_artist_details_page_id() : '';
		$content              = '';
		$this->pj_update_post_page( $default_page_title, $default_slug, $template_id, $type, $content, $stored_event_page_id, null );
	}

	/**
	 * Function Add Artist Parameters Metaboxes in sidebar
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_artist_parameters_metaboxes_sidebar() {
		add_meta_box(
			'artist_parameters',
			'PJ Artist Filters',
			array( $this, 'pj_render_artist_filters_box' ),
			$this->callbacks->get_post_type(),
			'side',
			'default',
			null
		);
	}

	/**
	 * Function Add Artist Settings Metaboxes in sidebar
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_artist_settings_metaboxes_sidebar() {
		add_meta_box(
			'artist_parameters_settings_layout',
			'PJ Artist Layout Styling',
			array( $this, 'pj_render_artist_settings_parameters_box' ),
			$this->callbacks->get_post_type(),
			'side',
			'default',
			null
		);
	}

	/**
	 * Function Add Section Before Metaboxes
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_section_before_meta_boxes() {
		add_meta_box(
			'artist_parameters_preview',
			'Artist Preview',
			array( $this, 'pj_render_section_before_beta_boxes' ),
			$this->callbacks->get_post_type(),
		);
	}

	/**
	 * Function Render Section Before Metaboxes
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_render_section_before_beta_boxes( $post ) {
		if ( $post->ID ) :?>
			<h2>Shortcode:</h2>
			<code>[pj_artist_list id="<?php echo esc_attr( $post->ID ); ?>"]</code>
			<?php echo wp_kses_post( $this->callbacks->pj_render_artist_preview() ); ?>
			<?php
		endif;
	}

	/**
	 * Function Picklejar Render Artist Filters Box
	 *
	 * @param WP_Post $post post.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_render_artist_filters_box( $post ) {
		wp_nonce_field(
			$this->callbacks->get_page(),
			$this->callbacks->get_nonce()
		);

		$data = $this->callbacks->get_post_meta_data( $post->ID );

		$this->manager_callbacks->render_general_input_field(
			$this->model_artist->get_pj_artist_data(),
			$this->callbacks->get_page(),
			$data,
			$post->ID
		);
	}

	/**
	 * Function Picklejar Render Artist Settings Parameters Box
	 *
	 * @param WP_Post $post post.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_render_artist_settings_parameters_box( $post ) {
		wp_nonce_field(
			$this->callbacks->get_page(),
			$this->callbacks->get_nonce()
		);

		$data = $this->callbacks->get_post_meta_data( $post->ID );

		$this->manager_callbacks->render_general_input_field(
			$this->model_artist_settings->get_pj_artist_inputs_settings(),
			$this->callbacks->get_page(),
			$data,
			$post->ID
		);
	}

	/**
	 * Function Picklejar Save Meta Box
	 *
	 * @param string $post_id post id.
	 *
	 * @return string $post_id
	 * @since 1.0.0
	 */
	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST[ $this->callbacks->get_nonce() ] ) && ! isset( $_POST[ $this->callbacks->get_page() ] ) ) {
			return $post_id;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ $this->callbacks->get_nonce() ] ) );

		if ( ! wp_verify_nonce( $nonce, $this->callbacks->get_page() ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$value     = sanitize_meta( $this->callbacks->get_plugin_key(), wp_unslash( $_POST[ $this->callbacks->get_page() ] ), 'post' );
		$sanitized = $this->callbacks->save_posts_data( $value );
		update_post_meta( $post_id, $this->callbacks->get_plugin_key(), $sanitized );

		return $post_id;
	}

	/**
	 * Function Picklejar Generate Endpoints
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pj_generate_endpoints() {

		wp_localize_script(
			$this->js_identifier,
			'pj_artist_list',
			array(
				'url'            => "$this->login_url/api/web/v1/search-handle/search",
				'nonce'          => wp_create_nonce( 'picklejar-artist-list-nonce' ),
				'action'         => 'picklejar-artist-list',
				'entityFilter'   => $this->dashboard_callbacks->pj_get_entity_parameters(),
				'renderTemplate' => $this->dashboard_callbacks->pj_get_artist_details_page_template(),
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_artist_details',
			array(
				'url'            => "$this->login_url/api/web/v1/artist/view",
				'nonce'          => wp_create_nonce( 'picklejar-artist-venues-details-nonce' ),
				'action'         => 'picklejar-artist-details',
				'entityId'       => $this->dashboard_callbacks->pj_get_entity_id(),
				'renderTemplate' => $this->dashboard_callbacks->pj_get_artist_details_page_template(),
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_venues_details',
			array(
				'url'            => "$this->login_url/api/web/v1/venue/view",
				'nonce'          => wp_create_nonce( 'picklejar-artist-venues-details-nonce' ),
				'action'         => 'picklejar-venue-details',
				'entityId'       => $this->dashboard_callbacks->pj_get_entity_id(),
				'renderTemplate' => $this->dashboard_callbacks->pj_get_artist_details_page_template(),
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_search_tip_button',
			array(
				'url'    => "$this->login_url/api/web/v1/tip/button-search",
				'nonce'  => wp_create_nonce( 'picklejar-search-tip-button-nonce' ),
				'action' => 'picklejar-search-tip-button',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_my_channel',
			array(
				'url'    => "$this->login_url/api/web/v2/entity-has-media",
				'nonce'  => wp_create_nonce( 'picklejar-my-channel-nonce' ),
				'action' => 'picklejar-my-channel',
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_upcoming',
			array(
				'url'            => "$this->login_url/api/web/v1/events/search-events",
				'nonce'          => wp_create_nonce( 'picklejar-upcoming-nonce' ),
				'action'         => 'picklejar-upcoming',
				'renderTemplate' => $this->dashboard_callbacks->pj_get_event_details_page_template(),
			)
		);
	}

	/**
	 * Function Picklejar Add Shortcode Column
	 *
	 * @param string $columns columns.
	 *
	 * @return array|string[]|void[]
	 */
	public function add_shortcode_colum( $columns ) {
		unset( $columns['date'] );

		return array_merge(
			$columns,
			array( 'shortcode' => __( 'Shortcode', 'pj-domain' ) ),
			array( 'date' => __( 'Date', 'pj-domain' ) )
		);
	}

	/**
	 * Function Picklejar Show Column
	 *
	 * @param string  $column_key column key.
	 * @param WP_Post $post_id post id.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function show_column(
		$column_key,
		$post_id
	) {
		echo wp_kses_post( '<code>[pj_artist_list id="' . $post_id . '"]</code>' );
	}
}
