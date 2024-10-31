<?php
/**
 * PickleJar Live for Artists & Venues  - Deactivate Class.
 *
 * @since   1.0.0
 * @package PickleJar Live for Artists & Venues
 */

$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );
if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'pj-login-step-2-nonce' ) ) :
	$mobile_phone        = sanitize_text_field( wp_unslash( $_GET['mobilePhone'] ?? '' ) );
	$confirmation_token  = sanitize_text_field( wp_unslash( $_GET['confirmationToken'] ?? '' ) );
	$response['success'] = true;
	$response['error']   = '';
	$response['content'] = '';

	if ( empty( $mobile_phone ) || empty( $confirmation_token ) ) :

		if ( empty( $mobile_phone ) ) :
			$response['error'] .= '<p>Mobile phone is required</p>';
		endif;
		if ( empty( $confirmation_token ) ) :
			$response['error'] .= '<p>Confirmation Token is required</p>';
		endif;
		$response['success'] = false;

	else :
		$inputs = '';
		$items  = 0;
		while ( $items < 6 ) {
			$inputs .= '<div class="picklejar-form-group otp-code"><input
            class="number-input picklejar-text-center picklejar-form-control widefat"
            name="number_' . $items . '"
            type="number"
            pattern="[0-9]*"
            placeholder="·"
            tabIndex="' . ( $items + 1 ) . '"
            maxLength="1"
        >
        </div>';
			$items ++;
		}
		?>
		<?php
		$response['content'] .= ' 
     <div class="picklejar-steps step-2">
      <span class="picklejar-step material-symbols-outlined success">check_circle</span>
      <span class="picklejar-step material-symbols-outlined active">radio_button_checked</span>
      <span class="picklejar-step material-symbols-outlined disabled">radio_button_unchecked</span>
      <span class="picklejar-step material-symbols-outlined disabled">radio_button_unchecked</span>
    </div>
    <div class="VerifySms">
      <h1 class="text-primary">
        We Want To Make Sure It&apos;s You
      </h1>
      <p>Enter the code sent to ' . $mobile_phone . '</p>
      <form
        id="otp-validation"
        autoComplete="off"
      >
        <div class="otp-input-list picklejar-d-flex">
          ' . $inputs . '
        </div>
        <div class="d-block picklejar-text-center">
          <div id="errorMessageBox"><span class="picklejar-invalid"></span></div>
          <button
            type="submit"
            id="otp-validation-submit"
            class="picklejar-btn picklejar-btn-primary"
          >
            Submit
          </button>
          <div>
            <p>
              <span
                class="picklejar-pointer"
                id="back-to-step-1"
              >Go back</span>
            </p>
          </div>
        </div>
      </form>
    </div>';
	endif;
else :
	$response['success'] = false;
	$response['error']   = 'Invalid nonce';
endif;

return wp_send_json( $response );

