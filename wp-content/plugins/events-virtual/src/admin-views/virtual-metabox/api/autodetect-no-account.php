<?php
/**
 * View: Virtual Events Metabox API Autodetect no account message.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/autodetect-no-account.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string,string> $classes_wrap     An array of classes for the toggle wrap.
 * @var string               $message          The message to display.
 * @var string               $setup_link_url   The URL to setup an API's accounts.
 * @var string               $setup_link_label The label of the button to setup an API's accounts.
 * @var array<string,string> $wrap_attrs       Associative array of attributes of the dropdown wrap.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

$wrap_classes = [ 'tec-events-virtual-meetings-control', 'tec-events-virtual-meetings-control--message' ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

if ( empty( $wrap_attrs ) ) {
	$wrap_attrs = [];
}
?>

<div
	<?php tribe_classes( $wrap_classes ); ?>
	<?php tribe_attributes( $wrap_attrs ) ?>
>
	<div class="tec-events-virtual-meetings-autodetect-api__message-inner">
		<?php echo $message; ?>
		<a
			class="tec-events-virtual-meetings-api__setup-link"
			href="<?php echo esc_url( $setup_link_url ); ?>"
		>
			<?php echo esc_html( $setup_link_label ); ?>
		</a>

	</div>
</div>
