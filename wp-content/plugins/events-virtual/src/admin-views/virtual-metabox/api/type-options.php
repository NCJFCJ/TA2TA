<?php
/**
 * View: Virtual Events Metabox API Create Type Options.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/type-options.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 * @since 1.11.0 - Add support for password requirements.
 * @since 1.13.0 - Add support for custom checked variable.
 *
 * @version 1.13.0
 *
 * @link    http://m.tri.be/1aiy
 *
 * @var string               $api_id                The ID of the API rendering the template.
 * @var string               $checked               The variable to use for which input to be considered checked.
 * @var array<string,string> $generation_urls       A map of the available URL generation labels and URLs.
 * @var array<string,string> $password_requirements An array of password requirements when generating a meeting for an API.
 * @var string               $metabox_id            The metabox current ID.
 *
 * @see     tribe_get_event() For the format of the event object.
 */
if ( empty( $checked ) ) {
	$checked = 'meeting';
}
?>
<div class="tec-events-virtual-meetings-api-create__types">
	<?php foreach ( $generation_urls as $type => list( $generate_link_url, $generate_link_label, $disabled, $tooltip ) ) : ?>
		<?php
		$this->template( 'components/radio', [
			'metabox_id'            => 'tribe-events-virtual',
			'api_id'                => $api_id,
			'name'                  => "{$metabox_id}[{$api_id}-meeting-type]",
			'type'                  => $type,
			'link'                  => $generate_link_url,
			'label'                 => $generate_link_label,
			'classes_label'         => [ $disabled ? 'disabled' : '' ],
			'classes_wrap'          => [ 1 === count( $generation_urls ) ? 'tribe-events-virtual-hidden' : '' ],
			'checked'               => $checked,
			'disabled'              => $disabled,
			'tooltip'               => $tooltip,
			'attrs'         		=> [
				'placeholder'    =>
					_x(
						'Select a Host',
						'The placeholder for the dropdown to select a host.',
						'events-virtual'
					),
				'data-type' => $type,
				'data-password-requirements' => json_encode( $password_requirements ),
			],
		] );
		?>
	<?php endforeach; ?>
</div>
