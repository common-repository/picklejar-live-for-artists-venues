<?php
/**
 * PickleJar Live for Artists & Venues - Render Event Slider Layout.
 *
 * @package PickleJar Live for Artists & Venues
 */

$i = 0;
?>
<div class="w-100">
	<div class="picklejar-row">
		<div class="slider-container">
			<?php while ( $i < 4 ) : ?>
				<div class="picklejar-col-4 event-column">
					<div class="picklejar-event-card">
						<div class="skeleton-date"></div>
						<div class="skeleton-avatar">
							<div class="skeleton-image"></div>
							<div class="picklejar-d-flex picklejar-d-column">
								<div class="skeleton-name"></div>
								<div class="skeleton-name"></div>
							</div>
						</div>
					</div>
				</div>
				<?php $i ++; ?>
			<?php endwhile ?>
		</div>
		<div class="picklejar-d-flex d-justify-center flex-1">
			<div class="slider-selector selected"></div>
			<div class="slider-selector"></div>
			<div class="slider-selector"></div>
		</div>
	</div>
</div>
