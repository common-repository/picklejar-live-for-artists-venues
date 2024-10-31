<?php
/**
 * PickleJar Live for Artists & Venues Dashboard Callbacks Class.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Api\Callbacks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Pages\Dashboard;

	/**
	 * Class DashboardCallbacks
	 *
	 * @extends Dashboard
	 * @since 1.0.0
	 * @package Picklejar\Api\Callbacks
	 */
class DashboardCallbacks extends Dashboard {
	/**
	 * Function get_page
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_page() {
		return 'picklejar_integration_plugin';
	}

	/**
	 * Function get_option_index
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_index() {
		return 'picklejar_integration_admin_index';
	}

	/**
	 * Function get_nonce
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_nonce() {
		return $this->get_page() . '_nonce';
	}

	/**
	 * Function get_section
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_section() {
		return $this->get_option_index();
	}

	/**
	 * Function get_plugin_key
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_plugin_key() {
		return '_' . $this->get_page() . '_key';
	}

	/**
	 * Function get_menu_slug
	 *
	 * @return string
	 * @since 1.0.0
	 *  @access public
	 */
	public function get_menu_slug() {
		return $this->get_page();
	}

	/**
	 * Function get_option_name
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_name() {
		return 'picklejar_integration_plugin';
	}

	/**
	 * Function get_option_group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_group() {
		return $this->get_option_name() . '_settings'; // picklejar_integration_plugin_events_configuration_settings.
	}

	/**
	 * Function get_option_group
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_settings_dashboard() {
		return require_once "$this->plugin_path/manager/dashboard/pj-dashboard.php";
	}

	/**
	 * Function pj_login_step1
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_login_step1() {
		return require_once "$this->plugin_path/includes/endpoints/login/pj-login-step-1.php";
	}

	/**
	 * Function pj_login_step2
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_login_step2() {
		return require_once "$this->plugin_path/includes/endpoints/login/pj-login-step-2.php";
	}

	/**
	 * Function pj_login_step3
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_login_step3() {
		return require_once "$this->plugin_path/includes/endpoints/login/pj-login-step-3.php";
	}

	/**
	 * Function pj_login_step4
	 *
	 * @return mixed
	 * @since 1.0.0
	 *  @access public
	 */
	public function pj_login_step4() {
		return require_once "$this->plugin_path/includes/endpoints/login/pj-login-step-4.php";
	}

	/**
	 * Function pj_error_page
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_error_page() {
		return require_once "$this->plugin_path/includes/endpoints/error/pj-404.php";
	}

	/**
	 * Function pj_success_login
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_success_login() {
		return require_once "$this->plugin_path/includes/endpoints/login/pj-login-success.php";
	}

	/**
	 * Function pj_success_logout
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_events_layout_settings() {
		echo '<h1>Event Layout Settings</h1>';

		return require_once "$this->plugin_path/manager/events-layout/pj-events-layout.php";
	}

	/**
	 * Function pj_artist_layout_settings
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_layout_settings() {
		echo '<h1>Artist Layout Settings</h1>';

		return require_once "$this->plugin_path/manager/artist-layout/pj-artist-layout.php";
	}

	/**
	 * Function get_data
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_data() {
		return get_option( $this->get_option_name() );
	}

	/**
	 * Function get_settings
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_settings() {
		return $this->get_data() !== null && isset( $this->get_data()['settings'] ) ?
		$this->get_data()['settings'] : null;
	}

	/**
	 * Function get_pj_access_token
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_access_token() {
		return $this->get_settings() !== null ?
		$this->get_settings()['pj_validation_token'] : '';
	}

	/**
	 * Function pj_get_entity_type
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_entity_type() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['pj_entity_type'] ) ?
		$settings['pj_entity_type'] : null;
	}

	/**
	 * Function pj_enable_CTP
	 *
	 * @return bool
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_enable_CTP() {
		return 'Artists' !== $this->pj_get_entity_type();
	}

	/**
	 * Function pj_get_entity_id
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_entity_id() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['pj_entity_id'] ) ?
		$settings['pj_entity_id'] : null;
	}

	/**
	 * Function get_pj_api_key
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pjGetEventsPageTitle() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['picklejar_events_layout_manager_page_title'] ) ?
		$settings['picklejar_events_layout_manager_page_title'] : null;
	}

	/**
	 * Function pj_get_events_page_id
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_events_page_id() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['picklejar_events_layout_manager_page_id'] ) ?
		$settings['picklejar_events_layout_manager_page_id'] : null;
	}

	/**
	 * Function pj_get_event_page_template
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_event_page_template() {
		if ( empty( $this->pj_get_events_page_id() ) ) {
			return get_permalink( get_page_by_path( self::EVENTS_DEFAULT_PAGE_SLUG ) );
		}

		return get_permalink( $this->pj_get_events_page_id() );
	}

	/**
	 * Function pj_get_events_details_page_id
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_events_details_page_id() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['picklejar_events_layout_manager_page_details_id'] ) ?
		$settings['picklejar_events_layout_manager_page_details_id'] : null;
	}

	/**
	 * Function pj_get_event_details_page_template
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_event_details_page_template() {
		if ( empty( $this->pj_get_events_details_page_id() ) ) {
			return get_permalink( get_page_by_path( self::EVENTS_DETAILS_DEFAULT_PAGE_SLUG ) );
		}

		return get_permalink( $this->pj_get_events_details_page_id() );
	}

	/**
	 * Function pj_get_artist_page_title
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_artist_page_title() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['picklejar_artist_layout_manager_page_title'] ) ?
		$settings['picklejar_artist_layout_manager_page_title'] : null;
	}

	/**
	 * Function pj_get_artist_page_id
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_artist_page_id() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['picklejar_artist_layout_manager_page_id'] ) ?
		$settings['picklejar_artist_layout_manager_page_id'] : null;
	}

	/**
	 * Function pj_get_artist_page_template
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_artist_page_template() {
		if ( empty( $this->pj_get_artist_page_id() ) ) {
			return get_permalink( get_page_by_path( self::ARTIST_DEFAULT_PAGE_SLUG ) );
		}

		return get_permalink( $this->pj_get_artist_page_id() );
	}

	/**
	 * Function pj_get_artist_details_page_id
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_artist_details_page_id() {
		$settings = $this->get_settings();

		return ! is_null( $settings ) && isset( $settings['picklejar_artist_layout_manager_page_details_id'] ) ?
		$settings['picklejar_artist_layout_manager_page_details_id'] : null;
	}

	/**
	 * Function pj_get_artist_details_page_template
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_artist_details_page_template() {
		if ( empty( $this->pj_get_artist_details_page_id() ) ) {
			return get_permalink( get_page_by_path( self::ARTIST_DETAILS_DEFAULT_PAGE_SLUG ) );
		}

		return get_permalink( $this->pj_get_artist_details_page_id() );
	}

	/**
	 * Function return_json
	 *
	 * @param bool $status status of the request.
	 *
	 * @return bool
	 * @since 1.0.0
	 * @access public
	 */
	public function return_json( $status ) {
		return true;
	}

	/**
	 * Function pj_get_entity_type
	 *
	 * @return array of entity parameters.
	 */
	public function pj_get_entity_parameters() {
		$entity_id = $this->pj_get_entity_id();

		switch ( $this->pj_get_entity_type() ) {
			case 'Venues':
				$entity_param = 'venue_id';
				break;

			case 'Artists':
				$entity_param = 'artist_id';
				break;

			default:
				$entity_param = $this->pj_get_entity_type();
				$entity_id    = true;
		}

		return array( $entity_param => $entity_id );
	}

	/**
	 * Function pj_get_entity_id.
	 *
	 * @param string $page page object.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if ( '' !== $section['before_section'] ) {
				if ( '' !== $section['section_class'] ) {
					echo wp_kses_post( sprintf( $section['before_section'], $section['section_class'] ) );
				} else {
					echo wp_kses_post( $section['before_section'] );
				}
			}

			if ( $section['title'] ) : ?>
					<h2><?php echo esc_attr( $section['title'] ); ?></h2>
					<?php
				endif;

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}
			echo '<div class="picklejar-form-table" role="presentation">';
			$this->do_settings_fields( $page, $section['id'] );
			echo '</div>';

			if ( '' !== $section['after_section'] ) {
				echo wp_kses_post( $section['after_section'] );
			}
		}
	}

	/**
	 * Function do_settings_fields.
	 *
	 * @param string $page page object.
	 * @param string $section section object.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function do_settings_fields(
	$page,
	$section
	) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}

		$tab_list = null;
		foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {

			if ( isset( $field['args']['tab'] ) ) {
				$tab                = $field['args']['tab'];
				$tab_list[ $tab ][] = $field;
			} else {
				?>
				<?php if ( ! empty( $field['args']['class'] ) ) : ?>
					<div class="<?php echo esc_attr( $field['args']['class'] ); ?>">
				<?php else : ?>
					<div>' );
				<?php endif; ?>

					<?php if ( ! empty( $field['args']['label_for'] ) ) : ?>
					<div><label for="<?php echo esc_attr( $field['args']['label_for'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label></div>
				<?php else : ?>
					<div><?php echo esc_attr( $field['title'] ); ?></div>;
				<?php endif; ?>

					<?php
					call_user_func( $field['callback'], $field['args'] );
					echo esc_html( '</div>' );
			}
		}

		if ( count( $tab_list ) ) {
			$current_tab       = '';
			$tab_header_count  = 0;
			$tab_content_count = 0;

			echo '<div class="picklejar-tabs-container picklejar-d-flex">';
			echo '<div class="picklejar-tab-nav picklejar-d-flex">';

			foreach ( $tab_list as $tab => $field_list ) :
				$tab_data = str_replace( ' ', '', strtolower( $tab ) );
				?>
				<div
					class="picklejar-tab-item <?php echo esc_attr( 0 === $tab_header_count ? 'picklejar-active' : '' ); ?>"
					data-pj-tab=".picklejar-tab-content-<?php echo esc_attr( $tab_data ); ?>"
				>
					<?php echo esc_html( $tab ); ?>
				</div>
					<?php $tab_header_count ++; ?>
				<?php endforeach; ?>

			</div>
			</div>

			<div class="picklejar-tabs-content">

				<?php
				foreach ( $tab_list as $tab => $field_list ) {
					foreach ( $field_list as $field ) {
						$tab_data = str_replace( ' ', '', strtolower( $tab ) );
						$class    = '';

						if ( ! empty( $field['args']['class'] ) ) {
							$class = $field['args']['class'];
						}

						if ( $current_tab !== $tab && $tab_content_count > 0 ) :
							echo '</div>';
						endif;

						if ( $current_tab !== $tab ) {
							$current_tab = $tab;
							echo wp_kses_post( '<div class="picklejar-tab picklejar-tab-content-' . $tab_data . ' ' . ( 0 === $tab_content_count ? 'picklejar-show' : '' ) . '">' );
						}

						if ( ! empty( $field['args']['group_before'] ) ) :
							echo wp_kses_post( $field['args']['group_before'] );
						endif;
						?>
					<div class="picklejar-form-group <?php echo esc_attr( $class ); ?>">
						<?php if ( ! empty( $field['title'] ) ) : ?>
							<?php if ( ! empty( $field['args']['label_for'] ) ) : ?>
								<label for="<?php echo esc_attr( $field['args']['label_for'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>;
							<?php else : ?>
								<div class='picklejar-label'><?php echo esc_attr( $field['title'] ); ?></div>;
							<?php endif ?>
						<?php endif; ?>
						<?php call_user_func( $field['callback'], $field['args'] ); ?>
					</div>
						<?php
						if ( ! empty( $field['args']['group_after'] ) ) :
							echo wp_kses_post( $field['args']['group_after'] );
						endif;
					}
					$tab_content_count ++;
				}
				echo '</div>';
		}
	}
}
