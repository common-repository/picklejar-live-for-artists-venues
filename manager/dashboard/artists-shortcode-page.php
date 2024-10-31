<?php
/**
 * PickleJar Live for Artists & Venues - Artists Shortcode Page.
 *
 * @package PickleJar Live for Artists & Venues
 */

wp_enqueue_style( 'icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', null, '5.4.4' );
?>

<div class="wrap picklejar-wrap">
	<h1 class="wp-heading-inline"></h1>
	<?php
	settings_errors();
	?>
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
			<h1 class="picklejar-page-header-title">Picklejar Artists Shortcode</h1>
		</div>
	</div>

	<div class="picklejar-tab-content">
		<div class="w-100">
			<h2>Configure custom shortcode</h2>

			<p>Use this shortcode to include all services list in your website. You can</p>
			<p><code>[pj_artist_list]</code></p>
			<ul class="picklejar-nav-list">
				<li class="picklejar-nav-item picklejar-nav-header">
					Configure shortcode
				</li>
				<li class="picklejar-nav-item">
					<a href="/wp-admin/admin.php?page=picklejar_integration_artist_layouts_style">
						<span class="material-symbols-outlined">grid_view</span>
						<b>Change global Artist List Shortcode layout</b>
						<span class="material-symbols-outlined">open_in_new</span>
					</a>
				</li>
				<li class="picklejar-nav-item">
					<a href="/wp-admin/post-new.php?post_type=pj_artists">
						<span class="material-symbols-outlined">tune</span>
						<b>Create an Artist List Shortcode with parameters</b>
						<span class="material-symbols-outlined">open_in_new</span>
					</a>
				</li>
			</ul>
			<h4>Example</h4>
			<div id="picklejar-layout-example-preview">
				<?php echo do_shortcode( '[pj_artist_list]' ); ?>
			</div>
		</div>
	</div>
</div>

