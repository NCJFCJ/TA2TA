<?php
/**
 * View: Virtual Events Metabox Video Source section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container/video-source.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.0
 * @since   1.6.0 - Add support for video source dropdown and remove trash icon and move to head.php.
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string   $metabox_id The current metabox id.
 * @var \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

?>
<tr class="tribe-events-virtual-video-source">
	<td class="tribe-table-field-label tribe-events-virtual-video-source__label">
		<?php esc_html_e( 'Add Video or Meeting Link:', 'events-virtual' ); ?>
	</td>
	<td class="tribe-events-virtual-video-source__content">
		<div
			class="tribe-events-virtual-video-sources-wrap"
		>
			<?php
				/**
				 * Allow filtering of the virtual event video sources.
				 *
				 * @since 1.6.0
				 *
				 * @param array<string|string> An array of video sources.
				 * @param \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
				 */
				$video_sources =  (array) apply_filters( 'tribe_events_virtual_video_sources', [], $post );

				$source_args = [
					'label'       => '',
					'id'          => "{$metabox_id}-video-source",
					'class'       => 'tribe-events-virtual-meetings-video-source-dropdown',
					'name'        => "{$metabox_id}[video-source]",
					'selected'    =>  $post->virtual_video_source,
					'attrs'       => [
						'placeholder' 	   => _x(
							'Choose Video Source',
							'The placeholder for the dropdown to choose the virtual video source.',
							'events-virtual'
						),
						'data-selected'    => $post->virtual_video_source,
						'data-hide-search' => '1',
						'data-options'     => json_encode( $video_sources ),
					],
				];
				$this->template( 'components/dropdown', $source_args );
			 ?>
		</div>
		<div
			class="tribe-events-virtual-video-sources"
		>
			<?php $this->do_entry_point( 'video_sources' ); ?>
		</div>
	</td>
</tr>
