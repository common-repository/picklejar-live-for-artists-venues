<?php
/**
 * Picklejar Live for Artists & Venues - PJArtistPageCallbacks
 *
 * @package PickleJar Live for Artists & Venues
 */

namespace Picklejar\Api\Callbacks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Picklejar\Base\BaseController;
use Picklejar\Models\Artist\PJArtistFilters;
use Picklejar\Models\Artist\PJArtistLayoutStyle;

/**
 * Class PJArtistPageCallbacks
 *
 * @extends BaseController
 * @since 1.0.0
 * @package Picklejar\Api\Callbacks
 */
class PJArtistPageCallbacks extends BaseController {
	/**
	 * Function to get current page.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_page() {
		return 'picklejar_integration_artist_layouts_style';
	}

	/**
	 * Function to get option index.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_index() {
		return $this->get_page() . '_index';
	}

	/**
	 * Function to get nonce.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_nonce() {
		return $this->get_page() . '_nonce';
	}

	/**
	 * Function to get section.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_section() {
		return $this->get_option_index();
	}

	/**
	 * Function to get plugin key.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_plugin_key() {
		return '_' . $this->get_page() . '_key';
	}

	/**
	 * Function to get menu slug.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_menu_slug() {
		return $this->get_page();
	}

	/**
	 * Function to get option name.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_name() {
		return 'picklejar_integration_plugin_artist_configuration';
	}

	/**
	 * Function to get option group.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_option_group() {
		// picklejar_integration_plugin_events_configuration_settings.

		return $this->get_option_name() . '_settings';
	}

	/**
	 * Function to Show Picklejar Layout Settings Section Manager.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_layout_settings_section_manager() {
		// Manage PJ Events Page.
		echo '<hr/>';
	}

	/**
	 * Function to Sanitize Picklejar Artist Layout.
	 *
	 * @param mixed $input input.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_layout_sanitize( $input ) {
		$manager_callback = new ManagerCallbacks();

		return $manager_callback->pj_layout_settings_sanitize( $input );
	}

	/**
	 * Function to ge Picklejar Artist List Global Configuration.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_get_artist_list_global_configuration_data() {
		return get_option( $this->get_option_name() );
	}

	/**
	 * Function to get Picklejar Artist Layout Styles.
	 *
	 * @param integer $post post.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_pj_artist_layout_styles_data( $post = null ) {
		$artist_data_model = new PJArtistLayoutStyle( $post );

		return $artist_data_model->get_pj_artist_settings_data();
	}

	/**
	 * Function to get Picklejar Artist Filters.
	 *
	 * @param integer $post post.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function get_pj_artist_filters_data( $post = null ) {
		$artist_data_model = new PJArtistFilters( $post );

		return $artist_data_model->get_pj_artist_filters_data();
	}

	/**
	 * Function to get pj_artist_filters_query.
	 *
	 * @param integer $post post.
	 *
	 * @return false|string
	 * @access public
	 */
	public function get_pj_artist_filters_query( $post = null ) {
		$artist_data_model = $this->get_pj_artist_filters_data( $post );
		$query             = array(
			'page' => 0,
		);
		foreach ( $artist_data_model as $filter_key => $filter_value ) {
			if ( ! empty( $filter_value ) ) {
				$query[ $filter_key ] = $filter_value;
			}
		}

		return wp_json_encode( $query );
	}

	/**
	 * Function to get post data by id.
	 *
	 * @param integer $post_id post id.
	 *
	 * @return mixed array|false
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_data_by_id( $post_id ) {
		return get_post_meta( $post_id, $this->get_plugin_key(), true );
	}

	/**
	 * Function to generate Picklejar Artist Layout Input.
	 *
	 * @param array $args array of arguments.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_layout_input( $args ) {
		$name        = $args['name'];
		$option_name = $args['option_name'];
		$id          = $args['id'] ?? $name;
		$class       = isset( $args['class'] ) ? ' ' . $args['class'] : '';
		$layout_type = array( 'grid', 'slider' );
		$value       = isset( get_option( $this->get_option_name() )[ $name ] ) ? get_option( $this->get_option_name() )[ $name ] : '';
		?>
		<?php foreach ( $layout_type as $layout ) : ?>
			<div class="picklejar-form-group picklejar-card border">
				<label for="<?php echo esc_attr( $id . '-' . $layout ); ?>">
					<span class="label">
						<input
							type="radio"
							id="<?php echo esc_attr( $id . '-' . $layout ); ?>"
							name="<?php echo esc_attr( $option_name ) . '[' . esc_attr( $name ) . ']'; ?>"
							value="<?php echo esc_attr( $layout ); ?>"
							class="<?php echo esc_attr( $class ); ?>" <?php echo $value === $layout ? 'checked' : ''; ?>
						>
						<span class="label">
							<?php echo esc_html( $layout ); ?>
						</span>
					</span>
				</label>
				<?php
				switch ( $layout ) :
					case 'grid':
						$this->pj_render_artist_grid_layout_field();
						break;

					case 'slider':
						$this->render_artist_slider_layout_field();
						break;
				endswitch;
				?>
			</div>
			<?php
		endforeach;
	}

	/**
	 * Function to render Picklejar Artist Grid Layout Field.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_render_artist_grid_layout_field() {
		return include plugin_dir_path( __DIR__ ) . '../../manager/artist-layout/partials/pj-render-artist-grid-layout-field.php';
	}

	/**
	 * Function to render Picklejar Artist Slider Layout Field
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function render_artist_slider_layout_field() {
		return include plugin_dir_path( __DIR__ ) . '../../manager/artist-layout/partials/pj-render-artist-slider-layout-field.php';
	}

	/**
	 * Function to get Picklejar Artist Layout Parameters.
	 *
	 * @param array  $attr array of attributes.
	 * @param array  $data array of data.
	 * @param string $filters_query array of filters.
	 *
	 * @return mixed
	 * @since 1.0.0
	 * @access public
	 */
	public function get_artist_layout_params(
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
				'title'                       => ! empty( $data['title_label'] ) ? $data['title_label'] : 'Artists',
				'title_color'                 => ! empty( $data['title_color'] ) ? $data['title_color'] : $primary_color,
				'title_alignment'             => ! empty( $data['title_alignment'] ) ? $data['title_alignment'] : 'center',
				'id'                          => '',
				'filters'                     => $filters,
			),
			$attr
		);
	}

	/**
	 * Function to get Picklejar Artist Layout Template.
	 *
	 * @param array $attr array of attributes.
	 *
	 * @return false|string
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_layout_template( $attr = array() ) {
		$artists_data = $this->get_pj_artist_layout_styles_data();
		$params       = $this->get_artist_layout_params( $attr, $artists_data, '' );

		$layout                      = $params['layout'];
		$avatar_profile_text_color   = $params['avatar_profile_text_color'];
		$avatar_profile_border_color = $params['avatar_profile_border_color'];
		$classes                     = "picklejar-{$layout}-layout {$params['class']}";
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
			<?php if ( ! empty( $container_background_image ) ) { ?>
				style="background-image: url(<?php echo esc_html( $container_background_image ); ?>"
			<?php } ?>
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
									<div class="picklejar-event-card-header"></div>
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
												<div class="picklejar-event-card-header"></div>
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
	 * Function get Picklejar Artists List.
	 *
	 * @param array $attr array of attributes.
	 *
	 * @return false|string
	 * @since 1.0.0
	 * @access public
	 */
	public function pj_artist_list( $attr = array() ) {
		wp_enqueue_style( $this->style_id . 'icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', null, '5.4.4' );

		$filters_query = '';
		if ( ! empty( $attr['id'] ) ) {
			$events_layout_controller = new PJArtistPostTypeCallbacks();
			$post_id                  = $attr['id'];
			$post_data                = $events_layout_controller->get_post_meta_data( $post_id );
			$data                     = $this->get_pj_artist_layout_styles_data( $post_data );
			$filters_query            = $this->get_pj_artist_filters_query( $post_data );
		} else {
			$data = $this->get_pj_artist_layout_styles_data();
		}

		$params = $this->get_artist_layout_params( $attr, $data, $filters_query );

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
			<?php if ( ! empty( $container_background_image ) ) : ?>
				style="background-image: url(<?php echo esc_html( $container_background_image ); ?>"
			<?php endif; ?>
		>
			<?php if ( ! empty( $title ) ) : ?>
				<div class="picklejar-row <?php echo esc_attr( $classes ); ?>">
					<div class="picklejar-col-xs-12">
						<h1
							class="picklejar-title"
							style="<?php echo esc_attr( "color: {$title_color}; text-align: {$title_alignment}" ); ?>"
						>
							<?php echo esc_attr( $title ); ?>
						</h1>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'pj-artist-template' === $current_page_template || 'front-end-templates/template-artists.php' === $current_page_template ) : ?>
				<form class="picklejar-row picklejar-entity-filter-form">
					<div class="picklejar-col-xs-12 picklejar-search-entity-filter-container">
						<div class="picklejar-search-entity-filter-form picklejar-form-group picklejar-input-group">
							<input
								name="handle"
								class="picklejar-form-control picklejar-filter-search-term-input"
								data-pj-entity="artist"
								type="search"
								placeholder="Find an artist"
								value=""
							>
							<button
								type="button"
								class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
								data-target=".picklejar-entities-row"
								data-pj-entity="artist"
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
							<?php esc_html_e( 'Show More Filters', 'pj-domain' ); ?>
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
														data-pj-entity="artist"
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
					id="pj-artist-<?php echo esc_attr( $current_id ); ?>"
					data-pj-entity="artist"
					data-pj-artist-details-page="<?php echo esc_url( get_permalink( get_page_by_path( 'pj-events-details' ) ) ); ?>"
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
					id="pj-loader-indicator-<?php echo esc_attr( $current_id ); ?>"
					data-pj-entity-target="#pj-artist-<?php echo esc_attr( $current_id ); ?>>"
					data-pj-entity="artist"
				></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Function get Picklejar Style Settings Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_style_settings_group() {
		return 'styles';
	}

	/**
	 * Function get Picklejar Filter Settings Group
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 */
	public function get_filter_settings_group() {
		return 'filters';
	}
}
