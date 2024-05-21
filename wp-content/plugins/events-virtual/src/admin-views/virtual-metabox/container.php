<?php
/**
 * Virtual Event Metabox.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.0.0
 *
 * @var Template $this    Template instance we are using.
 * @var WP_Post  $post    Post we are dealing with.
 * @var Metabox  $metabox The metabox instance.
 */

use Tribe\Events\Virtual\Metabox;
use Tribe__Template as Template;

$metabox_id = Metabox::$id;

?>
<table id="tribe-virtual-events" class="virtual-event-wrapper eventtable">
	<?php $this->template( 'virtual-metabox/container/head' ); ?>
	<tbody
		class="tribe-dependent"
		data-depends="#<?php echo esc_attr( "{$metabox_id}-setup" ); ?>"
		data-condition-checked
	>
		<?php
		$this->template(
			'virtual-metabox/container/event-type',
			[
				'metabox_id' => $metabox_id,
				'post'       => $post,
			]
		);

		$this->template(
			'virtual-metabox/container/video-source',
			[
				'metabox_id' => $metabox_id,
				'post'       => $post,
			]
		);

		$this->template(
			'virtual-metabox/container/display',
			[
				'metabox_id' => $metabox_id,
				'post'       => $post,
			]
		);

		$this->template(
			'virtual-metabox/container/show-when',
			[
				'metabox_id' => $metabox_id,
				'post'       => $post,
			]
		);

		$this->template(
			'virtual-metabox/container/show-to',
			[
				'metabox_id' => $metabox_id,
				'post'       => $post,
			]
		);

		$this->template(
			'virtual-metabox/container/label',
			[
				'metabox_id' => $metabox_id,
				'post'       => $post,
			]
		);
		?>
	</tbody>
</table>
