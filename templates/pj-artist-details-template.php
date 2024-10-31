<?php
/**
 * PickleJar Artist Details Template
 *
 * @package PickleJar Live for Artists & Venues
 */

use Picklejar\Api\Callbacks\DashboardCallbacks;
use Picklejar\Models\Artist\PJArtistLayoutStyle;

get_header();
wp_enqueue_style( 'icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', null, '5.4.4' );
$callbacks           = new PJArtistLayoutStyle();
$artist_data         = $callbacks->get_pj_artist_settings_data();
$dashboard_callbacks = new DashboardCallbacks();
$entity_id           = sanitize_text_field( wp_unslash( $_GET['entityId'] ?? '' ) );
$nonce               = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );
?>
<?php if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'picklejar-artist-list-nonce' ) || empty( $entity_id ) ) : ?>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			ACC.EntityDetails.entityType = '<?php echo esc_attr( $dashboard_callbacks->pj_get_entity_type() ); ?>';
		});
	</script>
	<div
		class="picklejar-entity-details-template picklejar-artist-details-template"
		<?php if ( ! empty( $artist_data['container_background_image'] ) ) : ?>
			style="background-image: url(<?php echo esc_url( $artist_data['container_background_image'] ); ?>)"
		<?php endif; ?>
	>
		<div class="picklejar-container">
			<?php
			if (
				! empty( $entity_id ) ||
				! ( empty( $dashboard_callbacks->pj_get_entity_id() ) && $dashboard_callbacks->pj_enable_CTP() )
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
							<div class="picklejar-tabs-container">
								<ul class="picklejar-nav picklejar-tab-nav picklejar-d-flex picklejar-orange-presentation">
									<li
										class="picklejar-tab-item picklejar-tab-divider picklejar-fill-content picklejar-active"
										data-pj-tab="#picklejar-chanel"
									>
										<span class="material-symbols-outlined">subscriptions</span>
										<span><?php esc_attr_e( 'My Channel', 'pj-domain' ); ?></span>
									</li>
									<li
										class="picklejar-tab-item picklejar-tab-divider picklejar-fill-content"
										data-pj-tab="#picklejar-upcoming"
									>
										<span class="material-symbols-outlined">event</span>
										<span><?php esc_attr_e( 'Upcoming', 'pj-domain' ); ?></span>
									</li>
									<li
										class="picklejar-tab-item picklejar-tab-divider picklejar-fill-content"
										data-pj-tab="#picklejar-projects"
									>
										<span class="material-symbols-outlined">attach_file</span>
										<span><?php esc_attr_e( 'Projects', 'pj-domain' ); ?></span>
									</li>
									<li
										class="picklejar-tab-item picklejar-fill-content"
										data-pj-tab="#picklejar-links"
									>
										<span class="material-symbols-outlined">link</span>
										<span><?php esc_attr_e( 'Links', 'pj-domain' ); ?></span>
									</li>
								</ul>
								<div class="picklejar-tabs-content">
									<div
										id="picklejar-chanel"
										class="picklejar-tab picklejar-show"
									>
										<div class="picklejar-section picklejar-media">
											<div class="picklejar-tabs-container">
												<ul class="picklejar-nav picklejar-tab-nav picklejar-d-flex w-100">
													<li
														class="picklejar-tab-item picklejar-fill-content picklejar-active"
														data-pj-tab="#picklejar-listen"
													>
														<span class="material-symbols-outlined">music_video</span>
														<?php esc_attr_e( 'listen', 'pj-domain' ); ?>
													</li>
													<li
														class="picklejar-tab-item picklejar-fill-content"
														data-pj-tab="#picklejar-watch"
													>
														<span class="material-symbols-outlined">smart_display</span>
														<?php esc_attr_e( 'watch', 'pj-domain' ); ?>
													</li>
													<li
														class="picklejar-tab-item picklejar-fill-content"
														data-pj-tab="#picklejar-gallery"
													>
														<span class="material-symbols-outlined">photo_library</span>
														<?php esc_attr_e( 'gallery', 'pj-domain' ); ?>
													</li>
													<li
														class="picklejar-tab-item picklejar-fill-content"
														data-pj-tab="#picklejar-promos"
													>
														<span class="material-symbols-outlined">campaign</span>
														<?php esc_attr_e( 'promos', 'pj-domain' ); ?>
													</li>
												</ul>

												<div
													id="picklejar-listen"
													class="picklejar-tab picklejar-show"
												>
													<div class="picklejar-form-group picklejar-input-group">
														<input
															name="handle"
															class="picklejar-form-control picklejar-filter-search-term-input"
															data-pj-entity=""
															type="search"
															placeholder="Search"
															value=""
															disabled
														>
														<button
															type="button"
															class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
															data-target=""
															data-pj-entity=""
															disabled
														>
															<span class="material-symbols-outlined">search</span>
														</button>
													</div>

													<div
														id="picklejar-listen-items"
														class="w-100 picklejar-text-center"
													></div>
												</div>

												<div
													id="picklejar-watch"
													class="picklejar-tab"
												>
													<div class="picklejar-form-group picklejar-input-group">
														<input
															name=""
															class="picklejar-form-control picklejar-filter-search-term-input"
															data-pj-entity=""
															type="search"
															placeholder="Search"
															value=""
															disabled
														>
														<button
															type="button"
															class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
															data-target=""
															data-pj-entity=""
															disabled
														>
															<span class="material-symbols-outlined">search</span>
														</button>
													</div>
													<div
														id="picklejar-watch-items"
														class="w-100 picklejar-text-center"
													></div>
												</div>

												<div
													id="picklejar-gallery"
													class="picklejar-tab"
												>
													<div class="picklejar-form-group picklejar-input-group">
														<input
															name=""
															class="picklejar-form-control picklejar-filter-search-term-input"
															data-pj-entity=""
															type="search"
															placeholder="Search"
															value=""
															disabled
														>
														<button
															type="button"
															class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
															data-target=""
															data-pj-entity=""
															disabled
														>
															<span class="material-symbols-outlined">search</span>
														</button>
													</div>
													<div
														id="picklejar-gallery-items"
														class="w-100 picklejar-text-center"
													></div>
												</div>

												<div
													id="picklejar-promos"
													class="picklejar-tab"
												>
													<div class="picklejar-form-group picklejar-input-group">
														<input
															name="handle"
															class="picklejar-form-control picklejar-filter-search-term-input"
															data-pj-entity=""
															type="search"
															placeholder="Search"
															value=""
															disabled
														>
														<button
															type="button"
															class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
															data-target=""
															data-pj-entity=""
															disabled
														>
															<span class="material-symbols-outlined">search</span>
														</button>
													</div>

													<div
														id="picklejar-promo-items"
														class="w-100 picklejar-text-center"
													></div>
												</div>
											</div>
										</div>
									</div>
									<div
										id="picklejar-upcoming"
										class="picklejar-tab"
									>
										<span class="glyphicon glyphicon-fire glyphicon--home--feature two columns text-center"></span>
										<span class="col-md-10">
												<h3>Feature 2</h3>
												<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
											</span>
									</div>
									<div
										id="picklejar-projects"
										class="picklejar-tab"
									>
										<div class="picklejar-form-group picklejar-input-group">
											<input
												name="handle"
												class="picklejar-form-control picklejar-filter-search-term-input"
												data-pj-entity=""
												type="search"
												placeholder="Search"
												value=""
												disabled
											>
											<button
												type="button"
												class="picklejar-btn picklejar-btn-primary picklejar-btn-lg picklejar-search-filter-submit pj-loading-button"
												data-target=""
												data-pj-entity=""
												disabled
											>
												<span class="material-symbols-outlined">search</span>
											</button>
										</div>
									</div>
									<div
										id="picklejar-links"
										class="picklejar-tab"
									>
										<div class="picklejar-section picklejar-social-link">
											<div class="picklejar-header">
												<?php echo esc_attr_e( 'Social Links', 'pj-domain' ); ?></php>
											</div>

											<div class="picklejar-grid-container picklejar-social-netwokrs">
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-facebook"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/facebook.svg"
															alt="facebook-logo"
														>
														Facebook
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-instagram"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/instagram.svg"
															alt="instagram-logo"
														>
														Instagram
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-reddit"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/reddit.svg"
															alt="reddit-logo"
														>
														Reddit
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-twitch"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/twitch.svg"
															alt="twitch-logo"
														>
														Twitch
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-facebook"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/web.svg"
															alt="facebook-logo"
														>
														Web
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-spotify"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/spotify.svg"
															alt="spotify-logo"
														>
														Spotify
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-tiktok"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/tiktok.svg"
															alt="tiktok-logo"
														>
														Tiktok
													</a>
												</div>
												<div class="picklejar-social-icon">
													<a
														target="_blank"
														href=""
														id="picklejar-twitter"
													>
														<img
															src="<?php echo esc_url( $GLOBALS['picklejar_img_dir'] ); ?>/twitter.svg"
															alt="twitter-logo"
														>
														Twitter
													</a>
												</div>
											</div>
										</div>

										<div class="picklejar-section picklejar-follow-me">
											<div class="picklejar-header">
												<?php echo esc_attr_e( 'Follow Me', 'pj-domain' ); ?></php>
											</div>
											<div id="picklejar-qrLink"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="picklejar-row picklejar-related picklejar-hidden">
						<div class="picklejar-col-12">
							<h3 class="picklejar-title"><?php echo esc_attr_e( 'Related Artist', 'pj-domain' ); ?></h3>
						</div>
						<div class="picklejar-row">
							<div class="picklejar-col-12 picklejar-col-lg-4"></div>
							<div class="picklejar-col-12 picklejar-col-lg-4"></div>
							<div class="picklejar-col-12 picklejar-col-lg-4"></div>
						</div>
					</div>
				</div>
			<?php else : ?>
				<h2><?php esc_attr_e( 'Missing Entity', 'pj-domain' ); ?></h2>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
<?php the_content(); ?>
<?php
get_footer();
