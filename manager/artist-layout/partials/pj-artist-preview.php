<?php
/**
 * PickleJar Live for Artists & Venues - Artist Page Callbacks.
 *
 * @package PickleJar Live for Artists & Venues
 */

global $post;

use Picklejar\Api\Callbacks\PJArtistPageCallbacks;
use Picklejar\Api\Callbacks\PJArtistPostTypeCallbacks;

$events_data_model = new PJArtistPageCallbacks();
$filters_query     = null;
if ( isset( $post ) ) {
	$events_layout_controller = new PJArtistPostTypeCallbacks();
	$the_post_id              = $post->ID;
	$post_data                = $events_layout_controller->get_post_meta_data( $the_post_id );
	$events_data              = $events_data_model->get_pj_artist_layout_styles_data( $post_data );
	$filters_query            = $events_data_model->get_pj_artist_filters_query( $post_data );
} else {
	$events_data = $events_data_model->get_pj_artist_layout_styles_data();
}
$title_alignment             = $events_data['title_alignment'];
$the_title                   = $events_data['title_label'];
$layout                      = $events_data['layout'];
$title_color                 = $events_data['title_color'];
$avatar_profile_border_color = $events_data['avatar_profile_border_color'];
$avatar_profile_text_color   = $events_data['avatar_profile_text_color'];
$location_text_color         = $events_data['location_text_color'];
$container_background_image  = $events_data['container_background_image'];
$items_to_show               = $events_data['items_to_show'];
$filters                     = ! empty( $filters_query ) && '[]' !== $filters_query ? $filters_query : null;
$shortcode_params            = '';
?>
<div class="picklejar-wrapper">
	<div class="picklejar-layout-preview">
		<div class="picklejar-row">
			<div id="picklejar-preview">
				<?php
				echo do_shortcode(
					'[pj_artist_layout_template 
                      avatar_profile_text_color=' . $avatar_profile_text_color . '
                      avatar_profile_border_color=' . $avatar_profile_border_color . '
                      container_background_image=""
                      layout=' . $layout . '
                      location_text_color=' . $location_text_color . '
                      title_label=""
                      title_alignment=' . $title_alignment . '
                      title_color=""
                      items_to_show="1"
                      filters=' . $filters . '
                      ]'
				);
				?>
			</div>
			<?php
			$shortcode_params .= ! empty( $avatar_profile_border_color ) ? " avatar_profile_border_color={$avatar_profile_border_color}" : null;
			$shortcode_params .= ! empty( $avatar_profile_text_color ) ? " avatar_profile_text_color={$avatar_profile_text_color}" : null;
			$shortcode_params .= ! empty( $container_background_image ) ? " container_background_image={$container_background_image}" : null;
			$shortcode_params .= ! empty( $layout ) ? " layout={$layout}" : null;
			$shortcode_params .= ! empty( $location_text_color ) ? " location_text_color={$location_text_color}" : null;
			$shortcode_params .= ! empty( $title_alignment ) ? " title_alignment={$title_alignment}" : null;
			$shortcode_params .= ! empty( $the_title ) ? ' title_label="' . $the_title . '"' : null;
			$shortcode_params .= ! empty( $title_color ) ? " title_color={$title_color}" : null;
			$shortcode_params .= ! empty( $items_to_show ) ? " items_to_show={$items_to_show}" : null;
			$shortcode_params .= ! empty( $the_post_id ) ? " id={$the_post_id}" : null;
			$shortcode_params .= ! empty( $filters ) ? " filters={$filters}" : null;
			?>
			<div id="picklejar-layout-example-preview">
				<?php echo do_shortcode( '[pj_artist_list ' . $shortcode_params . '"]' ); ?>
			</div>
		</div>
	</div>
</div>

