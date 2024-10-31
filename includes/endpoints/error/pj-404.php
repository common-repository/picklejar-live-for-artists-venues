<?php
/**
 * PickleJar Live for Artists & Venues  - 404 page.
 *
 * @since 1.0.0
 * @package PickleJar Live for Artists & Venues
 */

$admin_email         = get_bloginfo( 'admin_email' );
$response['content'] = '
<div class="picklejar-error-page picklejar-text-center">
  <h1>Ups! Something went wrong</h1>
  <p>We are having issues displaying this page. Please try again or contact support
    <a href="mailto: ' . $admin_email . '">' . $admin_email . '
    </a>
  </p>
</div>
';

return wp_send_json( $response );

