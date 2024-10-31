<?php
/**
 * PickleJar Live for Artists & Venues - Events Layout.
 *
 * @package PickleJar Live for Artists & Venues
 */

use Picklejar\Api\Callbacks\PJEventsPageCallbacks;

$callbacks   = new PJEventsPageCallbacks();
$events_data = $callbacks->get_pj_event_layout_styles_data();

$the_title                   = $events_data['title_label'];
$layout                      = $events_data['layout'];
$title_alignment             = $events_data['title_alignment'];
$title_color                 = $events_data['title_color'];
$date_background_color       = $events_data['date_background_color'];
$date_text_color             = $events_data['date_text_color'];
$avatar_profile_border_color = $events_data['avatar_profile_border_color'];
$avatar_profile_text_color   = $events_data['avatar_profile_text_color'];
$location_text_color         = $events_data['location_text_color'];
$page_full_width             = $events_data['page_full_width'];
$container_background_image  = $events_data['container_background_image'];
$items_to_show               = $events_data['items_to_show'];
?>


<div class="wrap">
	<div class="tab-content">
		<div
			id="tab-1"
			class="tab-pane active"
		>
			<div class="tab-pane active">
				<form
					method="post"
					action="options.php"
					id="create-new"
				>
					<?php settings_errors(); ?>

					<div class="messages"><h1></h1></div>
					<div class="picklejar-layout-header sticky">
						<div class="picklejar-page-header-left">
							<div class="picklejar-logo">
								<img
									width="120"
									class="picklejar-logo-small"
									src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/pj-logo-white.png"
									alt="Picklejar"
								>
							</div>
							<h1 class="picklejar-page-header-title">Picklejar Events Layout Styling</h1></div>
						<?php submit_button(); ?>
					</div>
					<div class="picklejar-wrapper picklejar-with-sidebar">
						<div class="picklejar-layout-preview">
							<div class="picklejar-row sticky">
								<div id="picklejar-preview">
									<?php
									echo do_shortcode(
										'[pj_events_layout_template 
				                      avatar_profile_text_color=' . $avatar_profile_text_color . '
				                      avatar_profile_border_color=' . $avatar_profile_border_color . '
				                      container_background_image=' . $container_background_image . '
				                      date_background_color=' . $date_background_color . '
				                      date_text_color=' . $date_text_color . '
				                      layout=' . $layout . '
				                      location_text_color=' . $location_text_color . '
				                      page_full_width=' . $page_full_width . '
				                      title_alignment=' . $title_alignment . '
				                      title_label=""
				                      title_color=""
				                      items_to_show="1"
				                      layout=' . $layout . '
				                      width_auto="true"
				                      ]'
									);
									?>
								</div>
								<?php
								$shortcode_params  = ! empty( $layout ) ? " layout={$layout}" : null;
								$shortcode_params  = ! empty( $title_alignment ) ? " title_alignment={$title_alignment}" : null;
								$shortcode_params .= ! empty( $the_title ) ? " title_label=\"{$the_title}\"" : null;
								$shortcode_params .= ! empty( $title_color ) ? " title_color={$title_color}" : null;
								$shortcode_params .= ! empty( $date_background_color ) ? " date_background_color={$date_background_color}" : null;
								$shortcode_params .= ! empty( $date_text_color ) ? " date_text_color={$date_text_color}" : null;
								$shortcode_params .= ! empty( $avatar_profile_text_color ) ? " avatar_profile_text_color={$avatar_profile_text_color}" : null;
								$shortcode_params .= ! empty( $location_text_color ) ? " location_text_color={$location_text_color}" : null;
								$shortcode_params .= ! empty( $avatar_profile_border_color ) ? " avatar_profile_border_color={$avatar_profile_border_color}" : null;
								$shortcode_params .= ! empty( $container_background_image ) ? " container_background_image={$container_background_image}" : null;
								$shortcode_params .= ! empty( $items_to_show ) ? " items_to_show={$items_to_show}" : null;
								?>
								<div id="picklejar-layout-example-preview">
									<?php echo do_shortcode( "[pj_events_layout_template {$shortcode_params}]" ); ?>
								</div>
							</div>
						</div>
						<div class="picklejar-layout-settings">
							<?php
							settings_fields( 'picklejar_integration_plugin_events_configuration_settings' );
							do_settings_sections( 'picklejar_integration_events_layouts_style' );
							?>

						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
