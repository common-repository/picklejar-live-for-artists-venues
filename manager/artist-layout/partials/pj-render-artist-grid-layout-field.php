<?php
/**
 * PickleJar Live for Artists & Venues - Artist Grid Layout.
 *
 * @package PickleJar Live for Artists & Venues
 */

$i = 0;
?>
<div class="picklejar-row">
	<?php while ( $i < 6 ) : ?>
		<div class="picklejar-col-4 event-column p-1">
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
