<?php
/**
 * View: Virtual Events Metabox an API account selection.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/accounts.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://m.tri.be/1aiy
 *
 * @var string               $api_id                   The ID of the API rendering the template.
 * @var array<string,string> $attrs                    Associative array of attributes of the API account.
 * @var string               $select_url               The URL to select an API account.
 * @var string               $select_label             The label used to designate the next step after selecting an API Account.
 * @var array<string,string> $accounts                 An array of users to be able to select as a host, that are formatted to use as options.
 * @var string               $remove_link_url          The URL to remove the API connection from the event.
 * @var string               $remove_link_label        The label of the button to remove the API connection from the event.
 * @var array<string,string> $remove_attrs             Associative array of attributes of the remove link.
 * @var string               $title                    The title of API integration.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

$metabox_id = 'tribe-events-virtual';
?>

<div
	id="tribe-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>"
	class="tribe-dependent tec-events-virtual-meetings-api-container tec-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>-details"
	<?php tribe_attributes( $attrs ) ?>
>

	<div
		class="tec-events-virtual-meetings-video-source__inner tec-events-virtual-meetings-api-details__inner tec-events-virtual-meetings-api-details__inner-accounts"
	>
		<a
			class="tec-events-virtual-meetings-api-details__remove-link"
			href="<?php echo esc_url( $remove_link_url ); ?>"
			aria-label="<?php echo esc_attr( $remove_link_label ); ?>"
			title="<?php echo esc_attr( $remove_link_label ); ?>"
			<?php tribe_attributes( $remove_attrs ) ?>
		>
			×
		</a>

		<div class="tec-events-virtual-meetings-api-details__title">
			<?php echo esc_html( $title ); ?>
		</div>

		<?php $this->template( 'components/dropdown', $accounts ); ?>

		<span class="tec-events-virtual-meetings-api-details__create-link-wrapper">
			<a
				class="button tec-events-virtual-meetings-api-action__account-select-link"
				href="<?php echo esc_url( $select_url ); ?>"
			>
				<?php echo esc_html( $select_label ); ?>
			</a>
		</span>

		<?php $this->template( '/components/loader' ); ?>

	</div>
</div>
