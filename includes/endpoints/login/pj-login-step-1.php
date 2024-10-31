<?php
/**
 * PickleJar Live for Artists & Venues  - Login Step 1.
 *
 * @since 1.0.0
 * @package PickleJar Live for Artists & Venues
 */

?>
<div id="step-1">
	<form
		method="post"
		id="generate-login-process"
	>
		<div class="picklejar-steps">
			<span class="picklejar-step material-symbols-outlined active">radio_button_checked</span>
			<span class="picklejar-step material-symbols-outlined disabled">radio_button_unchecked</span>
			<span class="picklejar-step material-symbols-outlined disabled">radio_button_unchecked</span>
			<span class="picklejar-step material-symbols-outlined disabled">radio_button_unchecked</span>
		</div>

		<h1 class="text-primary">
			Welcome Back
		</h1>
		<p>if the number below is correct, just click submit</p>

		<div class="picklejar-form-group">
			<label for="phone_number">Phone Number</label>
			<input
				class="picklejar-form-control widefat"
				id="phone_number"
				type="tel"
				name="phone_number"
				value=""
			>
		</div>

		<div class="picklejar-form-group picklejar-text-center">
			<button
				type="submit"
				class="ajax-call picklejar-btn picklejar-btn-primary"
			>
				Login
			</button>
		</div>
	</form>
</div>
<div id="picklejar-step-form"></div>
