<?php
/**
 * Virtual Event Metabox Table Head.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container/head.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.0.0
 *
 * @var Template $this                       Template instance we are using.
 * @var WP_Post  $post                       Post we are dealing with.
 * @var Metabox  $metabox                    The metabox instance.
 * @var boolean  $block_editor_compatibility If we are dealing with a metabox compatibility.
 */

use Tribe\Events\Virtual\Metabox;
use Tribe__Template as Template;

$metabox_id = Metabox::$id;
?>
<?php wp_nonce_field( Metabox::$nonce_action, "{$metabox_id}[virtual-nonce]" ); ?>
<thead>
<?php if ( empty( $block_editor_compatibility ) ) : ?>
	<tr>
		<td colspan="2" class="tribe_sectionheader">
			<h4>
				<?php
				// Note: we specifically do NOT use the template-tag functions in the admin!
				echo esc_html(
					sprintf(
					/* Translators: single event term. */
						__( 'Virtual %1$s', 'events-virtual' ),
						tribe_get_event_label_singular()
					)
				);
				?>
			</h4>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<td class="tribe-configure-virtual-button__container">
			<div>
				<button
					class="tribe-configure-virtual-button button tribe-dependent"
					type="button"
					data-depends="#<?php echo esc_attr( "{$metabox_id}-setup" ); ?>"
					data-condition-not-checked
				>
					<?php
					echo esc_html(
						sprintf(
						/* Translators: single event term. */
							__(
								'Configure Virtual %1$s',
								'events-virtual'
							),
							tribe_get_event_label_singular()
						)
					);
					?>
				</button>
			</div>
			<div class="screen-reader-text">
				<label for="<?php echo esc_attr( "{$metabox_id}-setup" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-setup" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[virtual]" ); ?>"
						type="checkbox"
						value="yes"
						<?php checked( tribe_is_truthy( $post->virtual ) ); ?>
					/>
					<?php
					echo esc_html(
						sprintf(
						/* Translators: single event term. */
							_x(
								'Mark as a virtual %1$s',
								'Event State of being virtual-only checkbox label',
								'events-virtual'
							),
							tribe_get_event_label_singular_lowercase()
						)
					);
					?>
				</label>
			</div>
		</td>
	</tr>
</thead>
