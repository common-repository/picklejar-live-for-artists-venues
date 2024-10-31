<?php
/**
 * PickleJar Live for Artists & Venues - Login Step 4.
 *
 * @since 1.0.0
 * @package PickleJar Live for Artists & Venues
 */

$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );
if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'pj-login-step-4-nonce' ) ) :
	if ( empty( $_GET['authKey'] ) ) : ?>
		<div class="picklejar-steps step-4">
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined active picklejar-text-danger">cancel</span>
		</div>
		<h2>Missing Authorization Key</h2>
	<?php else : ?>
		<div class="picklejar-steps">
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined success">check_circle</span>
			<span class="picklejar-step material-symbols-outlined active">radio_button_checked</span>
		</div>
		<div class="picklejar-text-center">
			<h1 class="text-primary">
				Profile activation success
			</h1>
			<p>Redirecting to dashboard</p>
			<div class="picklejar-loader">
				<div class="picklejar-loading"></div>
			</div>
		</div>
		<?php
	endif;
else :
	?>
	<div class="picklejar-steps step-4">
		<span class="picklejar-step material-symbols-outlined success">check_circle</span>
		<span class="picklejar-step material-symbols-outlined success">check_circle</span>
		<span class="picklejar-step material-symbols-outlined success">check_circle</span>
		<span class="picklejar-step material-symbols-outlined active picklejar-text-danger">cancel</span>
	</div>
	<h2>Invalid Nonce</h2>
<?php endif; ?>
<?php die(); ?>
