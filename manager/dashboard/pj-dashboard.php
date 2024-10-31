<?php
/**
 * PickleJar Live for Artists & Venues - Dashboard.
 *
 * @package PickleJar Live for Artists & Venues
 */

use Picklejar\Api\Callbacks\DashboardCallbacks;

$dashboard_settings = new DashboardCallbacks();
$dashboard_data     = $dashboard_settings->get_data();
?>

<div class="wrap picklejar-wrap">
	<?php settings_errors(); ?>
	<div class="messages"><h1></h1></div>
	<div class="picklejar-layout-header">
		<div class="picklejar-page-header-left">
			<div class="picklejar-logo">
				<img
					width="120"
					class="picklejar-logo-small"
					src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/pj-logo-white.png"
					alt="Picklejar"
				>
			</div>
			<h1 class="picklejar-page-header-title">Picklejar Dashboard Configuration</h1>
		</div>
		<?php if ( ! empty( $dashboard_data['settings']['pj_validation_token'] ) ) : ?>
			<button class="button button-primary picklejar-bg-danger">Logout</button>
		<?php endif; ?>
	</div>
	<div class="picklejar-login">
		<?php
		if (
			! empty( $dashboard_data['settings']['pj_validation_token'] )
		) {
			?>
			<div class="">
				<h1>Status: <span class="picklejar-text-success">Activated!</span></h1>
				<form
					method="post"
					action="options.php"
					id="save-access-token"
				>
					<?php
					settings_fields( 'picklejar_integration_plugin_dashboard' );
					do_settings_sections( 'picklejar_integration_plugin' );
					submit_button();
					?>
				</form>
			</div>
		<?php } else { ?>
			<div class="form-container">
				<div class="picklejar-row">
					<div class="picklejar-notification"></div>
					<?php $dashboard_settings->pj_login_step1(); ?>
					<div class="hidden">
						<form
							method="post"
							action="options.php"
							id="save-access-token"
						>
							<?php
							settings_fields( 'picklejar_integration_plugin_dashboard' );
							do_settings_sections( 'picklejar_integration_plugin' );
							submit_button();
							?>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
