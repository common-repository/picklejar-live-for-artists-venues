<?php
/**
 * PickleJar Events Page Callbacks.
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Api\Callbacks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Exception;
use Picklejar\Base\BaseController;
use Picklejar\Models\Events\PJEventsFilters;
use Picklejar\Models\Events\PJEventsLayoutStyle;
use WP_Post;

/**
 * Class PJEventsPageCallbacks
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Api\Callbacks
 */
class PJEventsPageCallbacks extends BaseController {

	/**
	 * Get Page Id
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_page() {
		return 'picklejar_integration_events_layouts_style';
	}

	/**
	 * Get Option Index Id
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_index() {
		return $this->get_page() . '_index';
	}

	/**
	 * Get Nonce
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_nonce() {
		return $this->get_page() . '_nonce';
	}

	/**
	 * Get Section
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_section() {
		return $this->get_option_index();
	}

	/**
	 * Get Plugin Key
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_plugin_key() {
		return '_' . $this->get_page() . '_key';
	}

	/**
	 * Get Menu Slug
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_menu_slug() {
		return $this->get_page();
	}

	/**
	 * Get Option Name
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_name() {
		return 'picklejar_integration_plugin_events_configuration';
	}

	/**
	 * Get Option Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_group() {
		return $this->get_option_name() . '_settings';
	}

	/**
	 * Get Layout Settings Section Manager
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_layout_settings_section_manager() {
		echo '<hr/>'; // Manage PJ Events Page.
	}

	/**
	 * Events Sanitizer
	 *
	 * @param array $input array of inputs.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_events_layout_sanitize( $input ) {
		$manager_callback = new ManagerCallbacks();

		return $manager_callback->pj_layout_settings_sanitize( $input );
	}

	/**
	 * Events Global Configuration
	 *
	 * @return false|mixed|void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_events_list_global_configuration_data() {
		return get_option( $this->get_option_name() );
	}

	/**
	 * Events Layout Data
	 *
	 * @param WP_Post $post post.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_event_layout_styles_data( $post = null ) {
		$events_data_model = new PJEventsLayoutStyle( $post );

		return $events_data_model->get_pj_event_settings_data();
	}

	/**
	 * Events filters Data
	 *
	 * @param WP_Post $post post.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_event_filters_data( $post = null ) {
		$events_data_model = new PJEventsFilters( $post );

		return $events_data_model->get_pj_event_filters_data();
	}

	/**
	 * Events Filters
	 *
	 * @param WP_Post|null $post post.
	 *
	 * @return false|string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_event_filters_query( $post = null ) {
		$events_data_model = $this->get_pj_event_filters_data( $post );
		$query             = array(
			'page' => 0,
		);
		foreach ( $events_data_model as $filter_key => $filter_value ) {
			if ( ! empty( $filter_value ) ) {
				$query[ $filter_key ] = $filter_value;
			}
		}

		return wp_json_encode( $query );
	}

	/**
	 * Get Post Data By Id
	 *
	 * @param integer|null $post_id post id.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_data_by_id( $post_id ) {
		return get_post_meta( $post_id, $this->get_plugin_key(), true );
	}

	/**
	 * Events Layout Input
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_events_layout_input( $args ) {
		$name        = $args['name'];
		$option_name = $args['option_name'];
		$id          = $args['id'] ?? $name;
		$class       = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$layout_type = array( 'grid', 'slider' );
		$value       = isset( get_option( $this->get_option_name() )[ $name ] ) ? get_option( $this->get_option_name() )[ $name ] : '';
		?>
		<?php foreach ( $layout_type as $layout ) : ?>
			<?php $layout_id = $id . '-' . $layout; ?>
			<div class="picklejar-form-group picklejar-card border">
				<label for="<?php echo esc_attr( $layout_id ); ?>">
					<span class="label">
						<input
							type="radio"
							id="<?php echo esc_attr( $layout_id ); ?>"
							name="<?php echo esc_attr( $option_name . '[' . $name . ']' ); ?>"
							value="<?php echo esc_attr( $layout ); ?>"
							class="<?php echo esc_attr( $class ); ?>" <?php echo $value === $layout ? 'checked' : ''; ?>
						>
						<span class="label">
							<?php echo esc_attr( $layout ); ?>
						</span>
					</span>
				</label>
				<?php
				switch ( $layout ) :
					case 'grid':
						$this->pj_render_event_grid_layout_field();
						break;
					case 'slider':
						$this->render_event_slider_layout_field();
						break;
				endswitch;
				?>
			</div>
			<?php
		endforeach;
	}

	/**
	 * Render Event Grid Layout
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function pj_render_event_grid_layout_field() {
		return include plugin_dir_path( __DIR__ ) . '../../manager/events-layout/partials/pj-render-event-grid-layout-field.php';
	}

	/**
	 * Render Slider Layout Field
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function render_event_slider_layout_field() {
		return include plugin_dir_path( __DIR__ ) . '../../manager/events-layout/partials/pj-render-event-slider-layout-field.php';
	}

	/**
	 * Get Events Layout params
	 *
	 * @param array  $attr array of attributes.
	 * @param array  $data array of data.
	 * @param string $filters_query string of filters.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_events_layout_params(
		$attr,
		$data,
		$filters_query
	) {
		$primary_color = $this->global_configuration['color']['primary'];
		$dark_color    = $this->global_configuration['color']['secondary'];
		$page_size     = ! empty( $data['items_to_show'] ) ? $data['items_to_show'] : 12;
		$filters       = ! empty( $filters_query ) ? $filters_query : null;

		return shortcode_atts(
			array(
				'class'                       => $data['class'] ?? '',
				'columns'                     => '4',
				'avatar_profile_border_color' => ! empty( $data['avatar_profile_border_color'] ) ? $data['avatar_profile_border_color'] : $primary_color,
				'avatar_profile_text_color'   => ! empty( $data['avatar_profile_text_color'] ) ? $data['avatar_profile_text_color'] : $primary_color,
				'container_background_image'  => ! empty( $data['container_background_image'] ) ? $data['container_background_image'] : null,
				'date_background_color'       => ! empty( $data['date_background_color'] ) ? $data['date_background_color'] : $primary_color,
				'date_text_color'             => ! empty( $data['date_text_color'] ) ? $data['date_text_color'] : $dark_color,
				'items_to_show'               => $page_size,
				'layout'                      => ! empty( $data['layout'] ) ? $data['layout'] : 'grid',
				'location_text_color'         => ! empty( $data['location_text_color'] ) ? $data['location_text_color'] : $dark_color,
				'page_full_width'             => ! ( empty( $data['page_full_width'] ) && $this->is_elementor_active ) ? true : null,
				'slider_control_color'        => $data['slider_control_color'] ?? $primary_color,
				'title'                       => ! empty( $data['title_label'] ) ? $data['title_label'] : 'Events',
				'title_color'                 => ! empty( $data['title_color'] ) ? $data['title_color'] : $primary_color,
				'title_alignment'             => ! empty( $data['title_alignment'] ) ? $data['title_alignment'] : 'center',
				'id'                          => '',
				'filters'                     => $filters,
			),
			$attr
		);
	}

	/**
	 * Events Layout Template
	 *
	 * @param array $attr array of attributes.
	 *
	 * @return false|string
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_events_layout_template( $attr = array() ) {
		$events_data = $this->get_pj_event_layout_styles_data();
		$params      = $this->get_events_layout_params( $attr, $events_data, '' );

		$layout                      = $params['layout'];
		$avatar_profile_text_color   = $params['avatar_profile_text_color'];
		$avatar_profile_border_color = $params['avatar_profile_border_color'];
		$classes                     = "picklejar-{$layout}-layout {$params['class']}";
		$date_background_color       = $params['date_background_color'];
		$color_date                  = $params['date_text_color'];
		$columns                     = $params['columns'];
		$container_background_image  = $this->pj_elementor_enabled_clear_parameter( $params['container_background_image'] );
		$location_text_color         = $params['location_text_color'];
		$page_full_width             = ! ( empty( $data['page_full_width'] ) && $this->is_elementor_active ) ? ' picklejar-full-width' : '';
		$slider_control_color        = ! empty( $params['slider_control_color'] ) ? $params['slider_control_color'] : '';
		$items_to_show               = $params['items_to_show'];
		$title                       = $this->pj_elementor_enabled_clear_parameter( $params['title'] );
		$title_alignment             = $params['title_alignment'] ?? 'center';
		$title_color                 = $params['title_color'];
		$i                           = 0;
		?>
		<?php ob_start(); ?>
		<div
			class="picklejar-container <?php echo esc_attr( $page_full_width ); ?>"
			<?php if ( ! empty( $container_background_image ) ) : ?>
				style="background-image: url(<?php echo esc_html( $container_background_image ); ?>"
			<?php endif; ?>
		>
			<?php if ( ! empty( $title ) ) : ?>
				<div class="picklejar-row">
					<div class="col">
						<h1
							class="picklejar-title"
							style="<?php echo esc_attr( "color: {$title_color}; text-align: {$title_alignment}" ); ?>"
						>
							<?php echo esc_attr( $title ); ?>
						</h1>
					</div>
				</div>
			<?php endif; ?>

			<div class="picklejar-row">
				<div class="picklejar-item-row picklejar-entities-row <?php echo esc_attr( $classes ); ?>">
					<?php if ( 'grid' === $layout ) : ?>
						<?php while ( $i < $items_to_show ) : ?>
							<div class="picklejar-entity-column picklejar-col-lg-6 picklejar-col-xl-<?php echo esc_attr( $columns ); ?>">
								<div class="picklejar-event-card">
									<div class="picklejar-event-card-header">
										<div
											class="picklejar-event-date"
											style="<?php echo esc_attr( "background-color:{$date_background_color};color: {$color_date}" ); ?>"
										>
											<div class="picklejar-day">FRI</div>
											<div class="picklejar-month">
												<div class="picklejar-divider">AUG</div>
												<div class="picklejar-divider">05</div>
											</div>
											<div class="picklejar-hour">
												<div class="">6:00 PM</div>
												<div class="">EDT</div>
											</div>
										</div>
										<div
											class="picklejar-event-date"
											style="<?php echo esc_attr( "background-color:{$date_background_color};color: {$color_date}" ); ?>"
										>
											<div class="picklejar-day">SAT</div>
											<div class="picklejar-month">
												<div class="picklejar-divider">AUG</div>
												<div class="picklejar-divider">06</div>
											</div>
											<div class="picklejar-hour">
												<div class="">6:00 PM</div>
												<div class="">EDT</div>
											</div>
										</div>
									</div>
									<div class="picklejar-event-card-footer">
										<div
											class="picklejar-avatar"
											style="border-color: <?php echo esc_attr( $avatar_profile_border_color ); ?>"
										>
											<img
												class="picklejar-avatar-image"
												src="<?php echo esc_url( $this->pj_get_avatar_image() ); ?>"
												alt="avatar-image"
											/>
										</div>
										<div class="picklejar-d-flex picklejar-d-column picklejar-contact-info">
											<div
												class="picklejar-contact"
												style="<?php echo esc_attr( "color:{$avatar_profile_text_color}" ); ?>"
											>
												@jazzbyrds
											</div>
											<small
												class="picklejar-location"
												style="<?php echo esc_attr( "color: {$location_text_color}" ); ?>"
											>
												Orillia
											</small>
										</div>
									</div>
								</div>
							</div>
							<?php $i ++; ?>
						<?php endwhile; ?>
					<?php endif; ?>

					<?php if ( 'slider' === $layout ) : ?>
						<!-- If we need navigation buttons -->
						<div
							class="swiper-button-prev picklejar-swiper-control"
							style="color:<?php echo esc_attr( $slider_control_color ); ?>"
						></div>
						<!-- Slider main container -->
						<div class="swiper picklejar-slider">
							<!-- Additional required wrapper -->
							<div class="swiper-wrapper">
								<?php while ( $i < $items_to_show ) : ?>
									<!-- Slides -->
									<div class="swiper-slide">
										<div class="picklejar-entity-column">
											<div class="picklejar-event-card">
												<div class="picklejar-event-card-header">
													<div
														class="picklejar-event-date"
														style="<?php echo esc_attr( "background-color:{$date_background_color};color: {$color_date}" ); ?>"
													>
														<div class="picklejar-day">FRI</div>
														<div class="picklejar-month">
															<div class="picklejar-divider">AUG</div>
															<div class="picklejar-divider">05</div>
														</div>
														<div class="picklejar-hour">
															<div class="">6:00 PM</div>
															<div class="">EDT</div>
														</div>
													</div>
													<div
														class="picklejar-event-date"
														style="<?php echo esc_attr( "background-color:{$date_background_color};color: {$color_date}" ); ?>"
													>
														<div class="picklejar-day">FRI</div>
														<div class="picklejar-month">
															<div class="picklejar-divider">AUG</div>
															<div class="picklejar-divider">05</div>
														</div>
														<div class="picklejar-hour">
															<div class="">6:00 PM</div>
															<div class="">EDT</div>
														</div>
													</div>
												</div>
												<div class="picklejar-event-card-footer">
													<div
														class="picklejar-avatar"
														style="border-color: <?php echo esc_attr( $avatar_profile_border_color ); ?>"
													>
														<img
															class="picklejar-avatar-image"
															src="<?php echo esc_url( $this->pj_get_avatar_image() ); ?>"
															alt="avatar-image"
														/>
													</div>
													<div class="picklejar-d-flex picklejar-d-column picklejar-contact-info">
														<div
															class="picklejar-contact"
															style="color:<?php echo esc_attr( $avatar_profile_text_color ); ?>"
														>
															@jazzbyrds
														</div>
														<small
															class="picklejar-location"
															style="<?php echo esc_attr( "color: {$location_text_color}" ); ?>"
														>
															Orillia
														</small>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php $i ++; ?>
								<?php endwhile; ?>
							</div>
							<!-- If we need scrollbar -->
							<!--div class="swiper-scrollbar"></div-->
						</div>
						<!-- If we need navigation buttons -->
						<div
							class="swiper-button-next picklejar-swiper-control"
							style="color:<?php echo esc_attr( $slider_control_color ); ?>"
						></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Events List
	 *
	 * @param array $attr array of attributes.
	 *
	 * @return false|string
	 * @throws Exception Exception class.
	 * @access public
	 */
	public function pj_events_list( $attr = array() ) {
		wp_enqueue_style( $this->style_id . 'icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', null, '5.4.4' );

		$filters_query = '';
		if ( ! empty( $attr['id'] ) ) {
			$events_layout_controller = new PJEventsPostTypeCallbacks();
			$post_data                = $events_layout_controller->get_post_meta_data( $attr['id'] );
			$data                     = $this->get_pj_event_layout_styles_data( $post_data );
			$filters_query            = $this->get_pj_event_filters_query( $post_data );
		} else {
			$data = $this->get_pj_event_layout_styles_data();
		}

		$params = $this->get_events_layout_params( $attr, $data, $filters_query );

		$current_id                  = ! empty( $params['id'] ) ? $params['id'] : random_int( 100, 999 );
		$classes                     = $params['class'];
		$avatar_profile_border_color = 'border-color: ' . $params['avatar_profile_border_color'];
		$avatar_profile_text_color   = 'color: ' . $params['avatar_profile_text_color'];
		$color_date                  = 'background-color: ' . $params['date_background_color'] . '; color: ' . $params['date_text_color'];
		$layout                      = $params['layout'];
		$location_text_color         = 'color: ' . $params['location_text_color'];
		$page_full_width             = ! ( empty( $data['page_full_width'] ) && $this->is_elementor_active ) ? ' picklejar-full-width' : '';
		$container_background_image  = $this->pj_elementor_enabled_clear_parameter( $params['container_background_image'] );
		$slider_control_color        = ! empty( $params['slider_control_color'] ) ? 'style="color: ' . $params['slider_control_color'] . '"' : '';
		$title                       = $this->pj_elementor_enabled_clear_parameter( $params['title'] );
		$title_alignment             = $params['title_alignment'] ?? 'center';
		$title_color                 = $params['title_color'];
		$query                       = $params['filters'];
		$current_page_template       = get_page_template_slug( get_queried_object_id() );
		?>
		<?php ob_start(); ?>
		<div
			class="picklejar-container picklejar-load-events<?php echo esc_attr( $page_full_width ); ?>"
			<?php if ( ! empty( $container_background_image ) ) { ?>
				style="background-image: url(<?php echo esc_html( $container_background_image ); ?>"
			<?php } ?>
		>
			<?php if ( ! empty( $title ) ) : ?>
				<div class="picklejar-row <?php echo esc_attr( $classes ); ?>">
					<div class="picklejar-col-xs-12">
						<h1
							class="picklejar-title"
							style="<?php echo esc_attr( "color: {$title_color}; text-align: {$title_alignment}" ); ?>"
						>
							<?php echo esc_html( $title ); ?>
						</h1>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'pj-events-template' === $current_page_template ) : ?>
				<form class="picklejar-row picklejar-entity-filter-form">

					<div class="picklejar-col-xs-12 picklejar-search-entity-filter-container">
						<div class="picklejar-search-entity-filter-form picklejar-form-group picklejar-input-group">
							<input
								name="search_term"
								class="picklejar-form-control picklejar-filter-search-term-input"
								data-pj-entity="event"
								type="search"
								placeholder="Find an event"
								value=""
							>
							<button
								type="button"
								class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
								data-target=".picklejar-entities-row"
								data-pj-entity="event"
							>
								<span class="material-symbols-outlined">search</span>
							</button>
						</div>
						<button
							type="button"
							class="picklejar-btn picklejar-btn-link picklejar-whitespace picklejar-show-more-filter"
							data-pj-modal="#picklejar-modal-filter-list"
						>
							<span class="material-symbols-outlined">tune</span>
							<?php echo esc_html_e( 'Show More Filters', 'pj-domain' ); ?>
						</button>
					</div>

					<div class="picklejar-col-12 picklejar-applied-filters-list"></div>

					<div
						id="picklejar-modal-filter-list"
						class="picklejar-modal fade"
						tabindex="-1"
						role="dialog"
					>
						<div
							class="picklejar-modal-dialog picklejar-search-entity-filter-form picklejar-search-entity-advanced-filter-form"
							role="document"
						>
							<div class="picklejar-modal-content">
								<div class="picklejar-modal-header">
									<button
										type="button"
										class="close"
										data-dismiss="modal"
										aria-label="Close"
									>
										<span
											class="material-symbols-outlined"
											aria-hidden="true"
										>chevron_left</span>
									</button>
									<h5
										class="picklejar-modal-title"
										id="exampleModalCenterTitle"
									>
										<?php esc_html_e( 'Filters', 'pj-domain' ); ?>
									</h5>
									<button
										type="button"
										class="picklejar-btn picklejar-clear-btn picklejar-clear-all-btn picklejar-primary"
									>
										<?php esc_html_e( 'Clear All', 'pj-domain' ); ?>
									</button>
								</div>
								<div class="picklejar-modal-body">
									<div class="picklejar-d-flex picklejar-d-column w-100">
										<div class="picklejar-filters-list">
											<!--Date Filters-->
											<div class="picklejar-dropdown picklejar-show">
												<button
													type="button"
													class="picklejar-btn-filter picklejar-d-flex pj-filters-list picklejar-dropdown-toggle picklejar-primary"
												>
													<span class="picklejar-label"><?php esc_html_e( 'Date', 'pj-domain' ); ?></span>
													<span class="material-symbols-outlined">expand_more</span>
												</button>
												<ul class="picklejar-dropdown-menu picklejar-input-group picklejar-button-group">
													<li class="picklejar-form-group">
														<input
															type="radio"
															class="picklejar-form-control picklejar-filters-date"
															id="date-today"
															name="end_date"
															value="<?php echo esc_attr( gmdate( 'Y/m/d' ) ); ?>"
														/>
														<label
															for="date-today"
															class="picklejar-pointer picklejar-form-check-input"
														>
															<?php esc_html_e( 'Today', 'pj-domain' ); ?>
														</label>
													</li>

													<li class="picklejar-form-group">
														<?php $date = strtotime( '+7 day' ); ?>
														<input
															type="radio"
															class="picklejar-form-control picklejar-filters-date"
															id="date-week"
															name="end_date"
															value="<?php echo esc_attr( gmdate( 'Y/m/d', $date ) ); ?>"
														/>
														<label
															for="date-week"
															class="picklejar-pointer picklejar-form-check-input"
														>
															<?php esc_html_e( 'This Week', 'pj-domain' ); ?>
														</label>
													</li>

													<li class="picklejar-form-group">
														<input
															type="radio"
															class="picklejar-form-control picklejar-filters-date"
															id="date-all"
															name="end_date"
															value=""
														/>
														<label
															for="date-all"
															class="picklejar-pointer picklejar-form-check-input"
														>
															<?php esc_html_e( 'All Dates', 'pj-domain' ); ?>
														</label>
													</li>
												</ul>
											</div>
											<!--/Date Filters"-->

											<!--Location Filters-->
											<div class="picklejar-dropdown picklejar-show">
												<button
													type="button"
													class="picklejar-btn-filter picklejar-d-flex pj-filters-list picklejar-dropdown-toggle"
												>
													<span class="picklejar-label"><?php esc_html_e( 'Location', 'pj-domain' ); ?></span>
													<span class="material-symbols-outlined">expand_more</span>
												</button>
												<div class="picklejar-dropdown-menu picklejar-input-group picklejar-filters-location">
													<button
														type="button"
														class="picklejar-btn picklejar-clear-btn picklejar-btn-sm picklejar-primary picklejar-clear-location picklejar-hidden"
													>
														<?php esc_html_e( 'Clear Location', 'pj-domain' ); ?>
													</button>
													<div class="picklejar-form-group">
														<input
															class="picklejar-form-control picklejar-trigger-geolocation-autocomplete"
															type="text"
															name="location"
														/>
														<input
															type="hidden"
															name="latitude"
														>
														<input
															type="hidden"
															name="longitude"
														>
														<input
															type="hidden"
															name="city"
														>
														<input
															type="hidden"
															name="state"
														>
														<input
															type="hidden"
															name="country"
														>
														<input
															type="hidden"
															name="zip"
														>
														<ul class="picklejar-geolocation-results picklejar-list"></ul>
													</div>
													<button
														type="button"
														class="picklejar-btn picklejar-clear-btn picklejar-btn-sm picklejar-primary picklejar-get-current-location"
													>
														<span class="material-symbols-outlined">location_on</span><?php esc_html_e( 'Get Current Location', 'pj-domain' ); ?>
													</button>
													<p class="hidden picklejar-geolocation-error"></p>
												</div>
											</div>
											<!--/Location Filters-->

											<!--Genre Filters"-->
											<div class="picklejar-dropdown picklejar-show">
												<button
													type="button"
													class="picklejar-btn-filter picklejar-d-flex pj-filters-list picklejar-dropdown-toggle"
												>
													<span class="picklejar-label"><?php esc_html_e( 'Genre', 'pj-domain' ); ?></span>
													<span class="material-symbols-outlined">expand_more</span>
												</button>
												<div class="picklejar-dropdown-menu picklejar-toggle-filter-form-group">
													<button
														type="button"
														class="picklejar-btn picklejar-clear-btn picklejar-btn-sm picklejar-primary picklejar-clear-genre"
													>
														<?php esc_html_e( 'Clear Genre', 'pj-domain' ); ?>
													</button>
													<ul
														class="picklejar-list picklejar-filters-genre"
														data-pj-entity="event"
													></ul>
												</div>
											</div>
											<!--/Genre Filters"-->
										</div>
									</div>
								</div>
								<div class="picklejar-action-buttons">
									<button
										type="button"
										class="picklejar-btn picklejar-btn-secondary picklejar-btn-lg picklejar-search-filter-submit picklejar-btn-block picklejar-btn-apply"
										data-target=".picklejar-entities-row"
										data-pj-entity="event"
										data-dismiss="modal"
									>
										<?php esc_html_e( 'Apply Filters', 'pj-domain' ); ?>
									</button>

								</div>
							</div>
						</div>
					</div>

				</form>
			<?php endif; ?>

			<div class="picklejar-row">
				<div
					class="picklejar-item-row picklejar-entities-row picklejar-<?php echo esc_attr( $layout ); ?>-layout"
					id="pj-event-<?php echo esc_attr( $current_id ); ?>"
					data-pj-entity="event"
					data-pj-event-details-page="<?php echo esc_url( get_permalink( get_page_by_path( 'pj-events-details' ) ) ); ?>"
					data-pj-loader-indicator="#pj-loader-indicator-<?php echo esc_attr( $current_id ); ?>"
					data-pj-layout-config='
					<?php
					echo wp_json_encode(
						array(
							'filters' => $query ?? null,
							'styles'  => array(
								'itemsToShow'              => $params['items_to_show'],
								'colorDate'                => $color_date,
								'avatarProfileBorderColor' => $avatar_profile_border_color,
								'avatarProfileTextColor'   => $avatar_profile_text_color,
								'locationTextColor'        => $location_text_color,
								'layout'                   => $params['layout'],
								'sliderControlColor'       => $slider_control_color,
							),
						)
					);
					?>
						'
				>
					<div class="picklejar-loader">
						<div class="picklejar-loading"></div>
					</div>
					<!-- Render Events -->
				</div>

				<div
					class="picklejar-infinite-scroll picklejar-hidden <?php echo esc_attr( $layout ); ?>"
					id="pj-event-loader-<?php echo esc_attr( $current_id ); ?>"
					data-pj-entity-target="#pj-event-<?php echo esc_attr( $current_id ); ?>"
					data-pj-entity="event"
				></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Style Settings Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_style_settings_group() {
		return 'styles';
	}

	/**
	 * Filter Settings Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_filter_settings_group() {
		return 'filters';
	}
}

