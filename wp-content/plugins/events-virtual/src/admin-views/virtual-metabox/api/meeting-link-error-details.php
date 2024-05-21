<?php
/**
 * View: Virtual Events Metabox API failed request details and controls.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/meeting-link-error-details.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string               $api_id                   The ID of the API rendering the template.
 * @var array<string,string> $attrs                    Associative array of attributes of the API account.
 * @var string               $remove_link_url          The URL to remove the event API connection.
 * @var string               $remove_link_label        The label of the button to remove the event API connection.
 * @var array<string,string> $remove_attrs             Associative array of attributes of the remove link.
 * @var bool                 $is_authorized            Whether the user authorized the API integration to create connections.
 * @var string               $error_title              The title of the error container.
 * @var string               $error_message            The message of the error container.
 * @var string               $error_details_title      The title of the error container details.
 * @var string               $error_body               The error message for the API.
 * @var string               $link_url                 The URL to generate an API connection.
 * @var string               $link_label               The label of the button to generate an API connection.
 */

$remove_link_label = _x(
	'Dismiss',
	'Accessible label of the control to dismiss a failure to generate an API connection.',
	'events-virtual'
);

?>

<div
	id="tribe-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>"
	class="tribe-dependent tec-events-virtual-meetings-api-container tec-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>-details"
	<?php tribe_attributes( $attrs ) ?>
>
	<div class="tec-events-virtual-meetings-video-source__inner tec-events-virtual-meetings-api-details__inner">
		<a
			class="tec-events-virtual-meetings-api-details__remove-link"
			href="<?php echo esc_url( $remove_link_url ); ?>"
			aria-label="<?php echo esc_attr( $remove_link_label ); ?>"
			title="<?php echo esc_attr( $remove_link_label ); ?>"
			<?php tribe_attributes( $remove_attrs ) ?>
		>
			×
		</a>

		<div class="tec-events-virtual-meetings-api-error__title">
			<?php echo esc_html( $error_title ); ?>
		</div>

		<div class="tec-events-virtual-meetings-api-standard-details__wrapper tec-events-virtual-meetings-api-error__message-wrapper">
			<p class="tec-events-virtual-meetings-api-error__message">
				<?php echo esc_html( $error_message ); ?>
			</p>
		</div>

		<div class="tec-events-virtual-meetings-api-error__details-header">
			<?php echo esc_html( $error_details_title ); ?>
		</div>

		<div class="tec-events-virtual-meetings-api-standard-details__wrapper tec-events-virtual-meetings-api-error__details-wrapper">
			<p class="tec-events-virtual-meetings-api-error__details-body">
				<?php echo wp_kses_post( $error_body ); ?>
			</p>
		</div>

		<div class="tec-events-virtual-meetings-api-error__link-wrapper">
			<?php if ( $is_authorized ) : ?>

				<a
					class="button button-secondary tribe-events-virtual-meetings-api__create-link"
					href="<?php echo esc_url( $link_url ); ?>"
				>
					<?php echo esc_html( $link_label ); ?>
				</a>

			<?php else : ?>

				<a
					class="tec-events-virtual-meetings-api-error__link-connect"
					href="<?php echo esc_url( $link_url ); ?>"
				>
					<?php echo esc_html( $link_label ); ?>
				</a>

			<?php endif; ?>
		</div>
	</div>
</div>
