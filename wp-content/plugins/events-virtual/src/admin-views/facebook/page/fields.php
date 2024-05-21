<?php
/**
 * View: Virtual Events Metabox Facebook Page Fields.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/fields.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var int                 $local_id The unique id used to save the page data.
 * @var array<string|mixed> $page     The page data.
 * @var URL                 $url      An instance of the URL handler.
 */

?>

<li
	class="tribe-settings-facebook-page-details__container"
	data-local-id="<?php echo esc_attr( $local_id ); ?>"
	data-ajax-save-access-url="<?php echo $url->to_save_access_page_link(); ?>"
>
	<div class="tribe-settings-facebook-page-details__page-row">
		<?php
		$this->template( 'facebook/page/components/text', [
			'classes_input' => [ 'tribe-settings-facebook-page-details__input', 'tribe-settings-facebook-page-details__page-name-input' ],
			'classes_wrap'  => [ 'tribe-settings-facebook-page-details__page-name' ],
			'label'         => _x( 'Page Name', 'Label for the name of the Facebook page connected to the site.', 'events-virtual' ),
			'name'          => "tec_facebook_page[]['name']",
			'placeholder'   => _x( 'Enter a Facebook Page Name', 'The placeholder for the Facebook Page name.', 'events-virtual' ),
			'screen_reader' => _x( 'Enter a Facebook Page name.', 'The screen reader text of the label for the Facebook Page name.', 'events-virtual' ),
			'page'          => $page,
			'value'         => $page['name'],
		] );
		?>

		<?php
		$this->template( 'facebook/page/components/text', [
			'classes_input' => [ 'tribe-settings-facebook-page-details__input','tribe-settings-facebook-page-details__page-id-input' ],
			'classes_wrap'  => [ 'tribe-settings-facebook-page-details__page-id' ],
			'label'         => _x( 'Page ID', 'Label for the id of the Facebook page connected to the site.', 'events-virtual' ),
			'name'          => "tec_facebook_page[]['id']",
			'placeholder'   => _x( 'Enter a Facebook Page ID', 'The placeholder for the Facebook page id.', 'events-virtual' ),
			'screen_reader' => _x( 'Enter a Facebook Page ID.', 'The screen reader text of the label for the Facebook page id.', 'events-virtual' ),
			'page'          => $page,
			'value'         => $page['page_id'],
		] );
		?>

		<div class="tribe-settings-facebook-page-details__actions tribe-settings-facebook-page-details__page-save">
			<?php if ( ! empty( $page['name'] ) || ! empty( $page['page_id'] ) ) {
				$this->template( 'facebook/page/components/update-button', [
					'page' => $page,
					'url'  => $url,
				] );
			} else {
				$this->template( 'facebook/page/components/add-button', [
					'page' => $page,
					'url'  => $url,
				] );
			} ?>
		</div>
		<?php if ( ! empty( $page['name'] ) && ! empty( $page['page_id'] ) ) {
			$this->template( 'facebook/page/components/delete-button', [
				'local_id' => $local_id,
				'page'     => $page,
				'url'      => $url,
			] );
		} ?>
	</div>

	<div class="tribe-settings-facebook-page-details__page-access-row">


		<?php if ( ! empty( $page['name'] ) && ! empty( $page['page_id'] ) ) {
			$this->template( 'facebook/page/components/status', [
				'local_id' => $local_id,
				'page'     => $page,
				'url'      => $url,
			] );
		} ?>

		<?php if ( ! empty( $page['name'] ) && ! empty( $page['page_id'] ) ) {
			$this->template( 'facebook/page/components/facebook-connect', [
				'local_id' => $local_id,
				'page'     => $page,
			] );
		} ?>

		<div class="tribe-settings-facebook-page-details__page-access-token">
			<input
				class="tribe-settings-facebook-page-details__page-access-token-input"
				type="hidden"
				name="tec_facebook_page[]['access_token']"
				value="<?php echo esc_html( $page['access_token'] ); ?>"
			>
		</div>

	</div>
</li>