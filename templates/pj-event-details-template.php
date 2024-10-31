<?php
/**
 * PickleJar Event Details Template
 *
 * @package PickleJar Live for Artists & Venues
 */

use Picklejar\Api\Callbacks\DashboardCallbacks;
use \Picklejar\Models\Events\PJEventsLayoutStyle;

get_header();
wp_enqueue_style( 'icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', null, '5.4.4' );
$callbacks           = new PJEventsLayoutStyle();
$event_data          = $callbacks->get_pj_event_settings_data();
$dashboard_callbacks = new DashboardCallbacks();
$nonce               = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );
$entity_id           = sanitize_text_field( wp_unslash( $_GET['entityId'] ?? '' ) );
?>

<?php
if (
	isset( $nonce ) &&
	(
		wp_verify_nonce( $nonce, 'picklejar-event-list-nonce' ) ||
		wp_verify_nonce( $nonce, 'picklejar-artist-list-nonce' )
	)
) :
	?>
	<div
		class="picklejar-entity-details-template picklejar-event-details-template"
		<?php if ( ! empty( $event_data['container_background_image'] ) ) : ?>
			style="background-image: url(<?php echo esc_url( $event_data['container_background_image'] ); ?>)"
		<?php endif; ?>
	>
		<div class="picklejar-container">
			<?php
			if (
				! empty( $nonce ) && (
					! empty( $entity_id ) ||
					! ( empty( $dashboard_callbacks->pj_get_entity_id() ) && $dashboard_callbacks->pj_enable_CTP() )
				)
			) : // phpcs:ignore
				?>
				<div class="picklejar-loader">
					<div class="picklejar-loading"></div>
				</div>
				<div class="picklejar-entity-detail-container picklejar-hidden">
					<div class="picklejar-row picklejar-event-detail">
						<div class="picklejar-entity-details-image"></div>
						<div class="picklejar-entity-details-information picklejar-col-12">
							<div class="picklejar-row">
								<div class="picklejar-col-12">
									<div class="picklejar-d-flex picklejar-space-between picklejar-align-end">
										<div class="picklejar-avatar"></div>
										<div class="picklejar-follow-button"></div>
										<div class="picklejar-event-details-tip"></div>
									</div>
								</div>
								<div class="picklejar-col-12">
									<h2 class="picklejar-title"></h2>
								</div>
								<div class="picklejar-row">
									<div class="picklejar-col-8 picklejar-events-details-start-date"></div>
								</div>
								<div class="picklejar-col-12">
									<p class="picklejar-event-details-description"></p>
								</div>
							</div>
						</div>
					</div>
					<div class="picklejar-row picklejar-related picklejar-hidden">
						<div class="picklejar-col-12">
							<h3 class="picklejar-title">Related Artist</h3>
						</div>
						<div class="picklejar-row">
							<div class="picklejar-col-12 picklejar-col-lg-4"></div>
							<div class="picklejar-col-12 picklejar-col-lg-4"></div>
							<div class="picklejar-col-12 picklejar-col-lg-4"></div>
						</div>
					</div>
				</div>
			<?php else : ?>
				<h2>Missing Entity Id</h2>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
<?php the_content(); ?>
<?php
get_footer();
