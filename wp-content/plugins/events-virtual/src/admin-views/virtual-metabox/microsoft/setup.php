<?php
/**
 * View: Virtual Events Metabox Microsoft API link controls.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/microsoft/setup.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.13.0
 *
 * @version 1.13.0
 *
 * @link    http://m.tri.be/1aiy
 *
 * @var string               $api_id                  The ID of the API rendering the template.
 * @var array<string,string> $attrs                   Associative array of attributes of the API account.
 * @var string               $account_label           The label used to designate the account of an API.
 * @var string               $account_name            The api account name.
 * @var string               $checked                 The variable to use for which input to be considered checked.
 * @var string               $generation_toggle_label The label of the accordion button to show the generation links.
 * @var array<string,string> $generation_urls         A map of the available URL generation labels and URLs.
 * @var string               $generate_label          The label used to designate the next step in generation of an API connection.
 * @var array<string,string> $hosts                   An array of users to be able to select as a host, that are formatted to use as options.
 * @var string               $remove_link_url         The URL to remove the event API connection.
 * @var string               $remove_link_label       The label of the button to remove the event an API connection link.
 * @var array<string,string> $remove_attrs            Associative array of attributes of the remove link.
 * @var string               $message                 A html message to display.
 * @var array<string|string> $message_classes         An array of message classes.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

$metabox_id = 'tribe-events-virtual';
?>

<div
	id="tribe-events-virtual-meetings-api"
	class="tec-events-virtual-meetings-api-container tec-events-virtual-meetings-api-details"
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

		<div
			<?php tribe_classes( $message_classes ); ?>
			role="alert"
		>
			<?php echo $message; ?>
		</div>

		<div class="tec-events-virtual-meetings-api-details__title">
			<?php echo esc_html( _x( 'Microsoft Meeting', 'Title for Microsoft Meeting creation.', 'events-virtual' ) ); ?>
		</div>

		<div class="tec-events-virtual-meetings-api__account">
			<?php echo esc_html( $account_label ); ?><?php echo esc_html( $account_name ); ?>
		</div>

		<?php
			if ( 1 === $hosts['hosts_count'] ) {
				$this->template( 'virtual-metabox/api/single-host', $hosts );
			} else {
				$this->template( 'components/dropdown', $hosts );
			}
		?>

		<?php
			$this->template( 'virtual-metabox/api/type-options', [
					'api_id'                => $api_id,
					'checked'               => $checked,
					'generation_urls'       => $generation_urls,
					'password_requirements' => [],
					'metabox_id'            => $metabox_id
				]
			);
		?>

		<span class="tec-events-virtual-meetings-api-details__create-link-wrapper">
			<a
				class="button tec-events-virtual-meetings-api-action__create-link"
				href="<?php echo esc_url( $generation_urls['teamsForBusiness'][0] ); ?>"
			>
				<?php echo esc_html( $generate_label ); ?>
			</a>
		</span>

		<?php $this->template( '/components/loader' ); ?>

	</div>
</div>
