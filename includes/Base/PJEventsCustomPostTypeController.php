<?php
/**
 * PickleJar Live for Artists & Venues - Events Custom Post Type Controller Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Base;

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Api\Callbacks\ManagerCallbacks;
use Picklejar\Api\Callbacks\PJEventsPostTypeCallbacks;
use Picklejar\Api\SettingsApi;
use Picklejar\Models\Events\PJEventsFilters;
use Picklejar\Models\Events\PJEventsLayoutStyle;
use WP_Post;

/**
 * Class PJEventsCustomPostTypeController
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Base
 */
class PJEventsCustomPostTypeController extends BaseController {

	/**
	 * Variable Callbacks.
	 *
	 * @since 1.0.0
	 * @var PJEventsPostTypeCallbacks
	 * @access private
	 */
	private $callbacks;

	/**
	 * Variable Manager Callbacks.
	 *
	 * @since 1.0.0
	 * @var ManagerCallbacks
	 * @access private
	 */
	private $manager_callbacks;

	/**
	 * Variable Settings.
	 *
	 * @since 1.0.0
	 * @var SettingsApi
	 * @access private
	 */
	private $settings;

	/**
	 * Variable Model Event.
	 *
	 * @since 1.0.0
	 * @var PJEventsFilters
	 * @access private
	 */
	private $model_event;

	/**
	 * Variable Model Event Settings.
	 *
	 * @since 1.0.0
	 * @var PJEventsLayoutStyle
	 * @access private
	 */
	private $model_event_settings;

	/**
	 * Variable Dashboard Callbacks.
	 *
	 * @since 1.0.0
	 * @var DashboardCallbacks
	 * @access private
	 */
	private $dashboard_callbacks;

	/**
	 * Variable Picklejar Events Page Id
	 *
	 * @since 1.0.0
	 * @var int
	 * @access private
	 */
	private $pj_get_events_page_id;

	/**
	 * Function Register.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function register() {
		if ( ! $this->activated( self::EVENT_LAYOUT_MANAGER ) ) {
			return;
		}

		$this->settings = new SettingsApi();

		$this->callbacks = new PJEventsPostTypeCallbacks();

		$this->model_event = new PJEventsFilters();

		$this->model_event_settings = new PJEventsLayoutStyle();

		$this->manager_callbacks = new ManagerCallbacks();

		$this->dashboard_callbacks = new DashboardCallbacks();

		add_action( 'init', array( $this, 'pj_create_default_events_page' ) );

		add_action( 'init', array( $this, 'pj_create_default_event_details_page' ) );

		if ( $this->dashboard_callbacks->pj_enable_CTP() ) {

			add_action( 'init', array( $this, 'pj_events_ctp' ) );

			add_action( 'admin_init', array( $this, 'add_event_settings_metaboxes_sidebar' ) );

			add_action( 'admin_init', array( $this, 'add_event_parameters_metaboxes_sidebar' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_section_before_meta_boxes' ) );

			add_action( 'save_post', array( $this, 'save_meta_box' ) );

		}

		$this->set_shortcode_page();

		// Register custom templates.
		add_filter( 'theme_page_templates', array( $this, 'pj_event_page_template_register' ), 10, 3 );
		add_filter( 'template_include', array( $this, 'pj_event_page_template_list_select' ), 99 );
		add_action( 'wp_ajax_submit_testimonial', array( $this, 'submit_event' ) );

		add_filter( 'manage_' . $this->callbacks->get_post_type() . '_posts_columns', array( $this, 'add_shortcode_colum' ), 10, 2 );
		add_action( 'manage_' . $this->callbacks->get_post_type() . '_posts_custom_column', array( $this, 'show_column' ), 10, 2 );

		// Generate ajax endpoints.
		add_action( 'wp_enqueue_scripts', array( $this, 'pj_generate_endpoints' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'pj_generate_endpoints' ) );
		add_filter( 'the_content', array( $this, 'pj_replace_events_page_content_with_shortcode' ), 999 );
	}

	/**
	 * Function Picklejar Replace Events page Content With Shortcode.
	 *
	 * @param string $content Content.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_replace_events_page_content_with_shortcode( $content ) {
		$this->pj_get_events_page_id = (string) $this->dashboard_callbacks->pj_get_events_page_id();
		$current_id                  = (string) get_the_ID();
		if ( $current_id === $this->pj_get_events_page_id && is_singular() && is_main_query() ) {
			return do_shortcode( '[pj_events_list]' );
		}

		return $content;
	}

	/**
	 * Function Picklejar Event Page Template List.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_event_page_template_list() {
		return array(
			self::EVENTS_DEFAULT_PAGE_TEMPLATE         => self::EVENTS_DEFAULT_PAGE_TEMPLATE_VALUE,
			self::EVENTS_DETAILS_DEFAULT_PAGE_TEMPLATE => self::EVENTS_DETAILS_DEFAULT_PAGE_TEMPLATE_VALUE,
		);
	}

	/**
	 * Function Picklejar Event Page Template Register.
	 *
	 * @param array   $page_templates page templates.
	 * @param string  $theme theme.
	 * @param WP_Post $post post.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_event_page_template_register(
		$page_templates,
		$theme,
		$post
	) {
		$templates = $this->pj_event_page_template_list();

		foreach ( $templates as $key => $template ) {
			$page_templates[ $key ] = $template;
		}

		return $page_templates;
	}

	/**
	 * Function Picklejar Event Page Template List Select.
	 *
	 * @param mixed $template template.
	 *
	 * @return mixed|string template.
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_event_page_template_list_select( $template ) {
		global $post;

		$page_temp_slug = get_page_template_slug( $post->ID );
		$templates      = $this->pj_event_page_template_list();

		if ( isset( $templates[ $page_temp_slug ] ) ) {
			$template = plugin_dir_path( dirname( __FILE__, 2 ) ) . 'templates/' . $page_temp_slug . '.php';
		}

		return $template;
	}

	/**
	 * Function Set Shortcode Page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function set_shortcode_page() {
		$subpage = array(
			array(
				'parent_slug' => 'edit.php?post_type=' . $this->callbacks->get_post_type(),
				'edit_title'  => 'Shortcodes',
				'page_title'  => 'Testimonials',
				'menu_title'  => 'Shortcodes',
				'capability'  => 'manage_options',
				'menu_slug'   => 'pj_events_shortcode',
				'callback'    => array( $this->callbacks, 'shortcode_page' ),
			),
		);

		$this->settings->add_sub_pages( $subpage )->register();
	}

	/**
	 * Function Picklejar Events Custom Post Type.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_events_ctp() {
		$option = array(
			'post_type'           => $this->callbacks->get_post_type(),
			'singular_name'       => 'PJ Event',
			'plural_name'         => 'PJ Events',
			'icon'                => $this->plugin_url . '/assets/images/event-icon.svg',
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
	 * Function Picklejar Events Page Default Title.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public static function pj_get_events_page_default_title() {
		return self::EVENTS_DEFAULT_PAGE_TITLE;
	}

	/**
	 * Function Picklejar Events Page Default Slug.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public static function pj_get_events_page_default_slug() {
		return self::EVENTS_DEFAULT_PAGE_SLUG;
	}

	/**
	 * Function Picklejar Events Page Template.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public static function pj_get_events_page_template() {
		return self::EVENTS_DEFAULT_PAGE_TEMPLATE;
	}

	/**
	 * Function Picklejar Events Details Page Default Title.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public static function pj_get_events_details_page_default_title() {
		return self::EVENTS_DETAILS_DEFAULT_PAGE_TITLE;
	}

	/**
	 * Function Picklejar Events Page Details Default Slug.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access private
	 */
	private static function pj_get_events_details_page_default_slug() {
		return self::EVENTS_DETAILS_DEFAULT_PAGE_SLUG;
	}

	/**
	 * Function Picklejar Events Page Details Template.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access private
	 */
	private static function pj_get_events_details_page_template() {
		return self::EVENTS_DETAILS_DEFAULT_PAGE_TEMPLATE;
	}


	/**
	 * Function Picklejar Create Default Events Page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_create_default_events_page() {
		$default_page_title   = $this->pj_get_events_page_default_title();
		$default_slug         = $this->pj_get_events_page_default_slug();
		$template_id          = $this->pj_get_events_page_template();
		$type                 = 'picklejar_events_layout_manager_page_id';
		$stored_event_page_id = $this->dashboard_callbacks ? $this->dashboard_callbacks->pj_get_events_page_id() : '';
		$content              = '[pj_events_list]';
		$this->pj_update_post_page( $default_page_title, $default_slug, $template_id, $type, $content, $stored_event_page_id, null );
	}

	/**
	 * Function Picklejar Create Default Events Details Page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_create_default_event_details_page() {
		$default_page_title   = $this->pj_get_events_details_page_default_title();
		$default_slug         = $this->pj_get_events_details_page_default_slug();
		$template_id          = $this->pj_get_events_details_page_template();
		$type                 = 'picklejar_events_layout_manager_page_details_id';
		$stored_event_page_id = $this->dashboard_callbacks ? $this->dashboard_callbacks->pj_get_events_details_page_id() : '';
		$content              = '';
		$this->pj_update_post_page( $default_page_title, $default_slug, $template_id, $type, $content, $stored_event_page_id, null );
	}

	/**
	 * Function Picklejar Event Parameters Metaboxes Sidebar.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function add_event_parameters_metaboxes_sidebar() {
		add_meta_box(
			'event_parameters',
			'PJ Events Filters',
			array( $this, 'pj_render_event_filters_box' ),
			$this->callbacks->get_post_type(),
			'side',
			'default',
			null
		);
	}

	/**
	 * Function Picklejar Event Settings Metaboxes Sidebar.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function add_event_settings_metaboxes_sidebar() {
		add_meta_box(
			'event_parameters_settings_layout',
			'Event Layout Styling',
			array( $this, 'pjRenderEventSettingsParametersBox' ),
			$this->callbacks->get_post_type(),
			'side',
			'default',
			null
		);
	}

	/**
	 * Function Picklejar Add Section Before Metaboxes.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function add_section_before_meta_boxes() {
		add_meta_box(
			'event_parameters_preview',
			'Events Preview',
			array( $this, 'pj_render_section_before_beta_boxes' ),
			$this->callbacks->get_post_type(),
		);
	}

	/**
	 * Function Picklejar Render Section Before Beta Metaboxes.
	 *
	 * @param WP_Post $post post.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_render_section_before_beta_boxes( $post ) {
		if ( $post->ID ) :
			?>
			<h2>Shortcode:</h2>
			<code>[pj_events_list id="<?php echo esc_attr( $post->ID ); ?>"]</code>
			<?php
			echo wp_kses_post( $this->callbacks->pj_render_event_preview() );
		endif;
	}

	/**
	 * Function Picklejar Render Event Filters Box.
	 *
	 * @param WP_Post $post post.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_render_event_filters_box( $post ) {
		wp_nonce_field(
			$this->callbacks->get_page(),
			$this->callbacks->get_nonce()
		);

		$data = $this->callbacks->get_post_meta_data( $post->ID );

		$this->manager_callbacks->render_general_input_field(
			$this->model_event->get_pj_event_data(),
			$this->callbacks->get_page(),
			$data,
			$post->ID
		);
	}

	/**
	 * Function Picklejar Render Event Settings Parameters Box.
	 *
	 * @param WP_Post $post post.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pjRenderEventSettingsParametersBox( $post ) {
		wp_nonce_field(
			$this->callbacks->get_page(),
			$this->callbacks->get_nonce()
		);

		$data = $this->callbacks->get_post_meta_data( $post->ID );

		$this->manager_callbacks->render_general_input_field(
			$this->model_event_settings->get_pj_event_inputs_settings(),
			$this->callbacks->get_page(),
			$data,
			$post->ID
		);
	}

	/**
	 * Function Picklejar Save Meta Box.
	 *
	 * @param integer|null $post_id post id.
	 *
	 * @return int|null
	 * @since 1.0.0
	 * @access public
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
	 * Function Picklejar Generate Endpoints.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_generate_endpoints() {
		wp_localize_script(
			$this->js_identifier,
			'pj_event_list',
			array(
				'url'            => "$this->login_url/api/web/v1/events/search-events",
				'nonce'          => wp_create_nonce( 'picklejar-event-list-nonce' ),
				'action'         => 'picklejar-event-list',
				'entityFilter'   => $this->dashboard_callbacks->pj_get_entity_parameters(),
				'renderTemplate' => $this->dashboard_callbacks->pj_get_event_details_page_template(),
			)
		);

		wp_localize_script(
			$this->js_identifier,
			'pj_event_details',
			array(
				'url'      => "$this->login_url/api/web/v1/events/view",
				'nonce'    => wp_create_nonce( 'picklejar-event-details-nonce' ),
				'action'   => 'picklejar-event-details',
				'entityId' => $this->dashboard_callbacks->pj_get_entity_id(),
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
			'pj_geolocation_autocomplete',
			array(
				'url'    => "$this->login_url/api/web/geocode/default/autocomplete",
				'nonce'  => wp_create_nonce( 'picklejar-geolocation-autocomplete-nonce' ),
				'action' => 'picklejar-geolocation-autocomplete',
			)
		);
		wp_localize_script(
			$this->js_identifier,
			'pj_geolocation_reverse_query',
			array(
				'url'    => "$this->login_url}/api/web/geocode/default/reverse-query",
				'nonce'  => wp_create_nonce( 'picklejar-geolocation-reverse-query-nonce' ),
				'action' => 'picklejar-geolocation-reverse-query',
			)
		);
	}

	/**
	 * Function Add Shortcode Column.
	 *
	 * @param array $columns columns.
	 *
	 * @return array|string[]|void[] columns
	 * @since 1.0.0
	 * @access public
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
	 * Function Show Column.
	 *
	 * @param string       $column_key column key.
	 * @param integer|null $post_id post id.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function show_column(
		$column_key,
		$post_id
	) {
		echo wp_kses_post( '<code>[pj_artist_list id="' . $post_id . '"]</code>' );
	}
}
