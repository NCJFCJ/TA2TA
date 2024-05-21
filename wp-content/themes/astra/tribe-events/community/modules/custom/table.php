<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox Table For Custom Fields
 * This is used to add the table to the event submission form that contains custom field inputs.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom/table.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since   4.6.3
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 * @var array $fields  List of form fields.
 * @var int   $post_id Current Post ID.
 */
?>
<?php

	$org_post_id = get_post_id_for_organization();
	$projects = organizations_grant_projects();
	$grant_projects = $projects[ $org_post_id ];
	$grant_projects_choices = [];
	$award_choices = [];
	$grant_program_choices = [];
	foreach($grant_projects as $grant_project){
		$grant_projects_choices[] = $grant_project['project_title'];
		$award_choices[$grant_project['project_title']] = $grant_project['award_number'];
		$grant_program_choices[$grant_project['award_number']] = $grant_project['grant_programs'];
	}
	// echo '<pre>';
	// print_r($grant_projects_choices); echo '<br>';
	// print_r($award_choices); echo '<br>';
	// print_r($grant_program_choices); echo '<br>';
	// echo '</pre>'; 
	// die();
?>

<table class="tribe-section-content">
	<colgroup>
		<col class="tribe-colgroup tribe-colgroup-label">
		<col class="tribe-colgroup tribe-colgroup-field">
	</colgroup>

	<?php foreach ( $fields as $field ) : ?>
		<?php
		$field_name  = $field['name'];
		$field_label = $field['label'];
		$field_type  = $field['type'];
		$field_id    = sanitize_html_class( 'tribe_custom-' . $field_name . '-' . $field_label );

		$value = get_post_meta( $post_id, $field_name, true );

		// Possibly save field value on failed form submit so user doesn't have to re-enter.
		if ( empty( $value ) && ! empty( $_POST[ $field_name ] ) ) {
			$value = $_POST[ $field_name ];
		}

		/**
		 * Allows setting and/or modifying the field's value.
		 *
		 * @since 4.9.1 Removed `None` from the radio option.
		 *
		 * @param mixed  $value    The existing field value.
		 * @param string $name     The field name.
		 * @param int    $event_id The event's post ID.
		 */
		$value = apply_filters( 'tribe_events_community_custom_field_value', $value, $field_name, $post_id );

		// Configure options
		$options = [];

		switch ( $field_type ) {
			case 'checkbox':
			case 'radio':
			case 'dropdown':
				// Handle values that may be a "|"-delimited string.
				if ( ! is_array( $value ) ) {
					$value = explode( '|', $value );
				}

				// Handle the value in its more common form as an array.
				$value = array_filter( $value );
				$value = array_map( 'trim', $value );

				$field_name = stripslashes( $field_name );

				// Add Blank None option for Radio and Dropdown
				if ( 'checkbox' === $field_type ) {
					// Field supports multiple values.
					$field_name .= '[]';
				} elseif ( 'dropdown' === $field_type ) {
					// Field supports empty value.
					$options[''] = 'None';
				}

				break;
			default:
				$value = is_array( $value ) ? $value[0] : $value;
				$value = trim( stripslashes( $value ) );

				break;
		}

		// Options defined in the panel
		$field_values = explode( "\n", $field['values'] );

		if($field_label == 'Grant Project'){
			$field_values = $grant_projects_choices;
		}
		
		if($field_label == 'Topic Area'){
			$field_values = ta2ta_get_terms_list( get_topic_areas_Obj() );
		}
		if($field_label == 'Target Audiences'){
			$field_values = ta2ta_get_terms_list( get_target_audiences_Obj() );
		}
		// if($field_label == 'Grant Programs'){
		// 	$field_values = ta2ta_get_terms_list( get_grant_programs_Obj() );
		// }

		// Add options after any potential starting options.
		$options = array_merge( $options, $field_values );
		$options = array_map( 'trim', $options );

		$field_classes = [
			'tribe-section-content-row',
			'tribe-field-type-' . $field_type,
		];

		$field_classes = array_map( 'sanitize_html_class', $field_classes );
		$field_classes = implode( ' ', $field_classes );

		$data = compact( [
			'fields',
			'post_id',
			'field',
			'field_classes',
			'field_name',
			'field_label',
			'field_type',
			'field_id',
			'value',
			'options',
		] );

		tribe_get_template_part( 'community/modules/custom/table-row', null, $data );
		?>
	<?php endforeach; ?>
</table>
