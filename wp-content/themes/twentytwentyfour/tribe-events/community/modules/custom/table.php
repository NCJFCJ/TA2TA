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
	$award_choices_js = [];
	$award_choices = [];
	$grant_program_choices_js = [];
	$grant_program_choices = [];
	foreach($grant_projects as $grant_project){
		$grant_projects_choices[] = $grant_project['project_title'];
		$award_choices_js[$grant_project['project_title']] = $grant_project['award_number'];
		$grant_program_choices_js[$grant_project['award_number']] = $grant_project['grant_programs'];
	}
	#setup choices by default
	$award_choices_options = get_field( 'awards', 'options', true );
	foreach( $award_choices_options as $a ){
		$award_choices[] = $a['number'];
	}

	$grant_program_choices_options = get_field( 'grant_program', 'options', true );
	foreach( $grant_program_choices_options as $g ){
		$grant_program_choices[] = $g['item'];
	}
	
	#Grant programs have already default choices in case of edit
	$selected_grant_project = get_post_meta( $post_id, '_ecp_custom_3', true );
	$selected_award = get_post_meta( $post_id, '_ecp_custom_5', true );
	if( ! empty($selected_grant_project) && $selected_grant_project != ''){
		$award_choices[] = $award_choices_js[ $selected_grant_project ];
		$grant_program_choices = $grant_program_choices_js[ $selected_award ];
	}

	#setup more choices by default
	$topic_areas_choices_options = get_field( 'topic_area', 'options', true );
	$topic_areas_choices = [];
	foreach( $topic_areas_choices_options as $c ){
		$topic_areas_choices[] = $c['item'];
	}

	$target_audiences_choices = [];
	$target_audiences_choices_options = get_field( 'target_audience', 'options', true );
	foreach( $target_audiences_choices_options as $a ){
		$target_audiences_choices[] = $a['item'];
	}

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
		
		if($field_label == 'Award'){
			$field_values = $award_choices;
		}

		if($field_label == 'Grant Programs'){
			$field_values = $grant_program_choices;
		}
		
		if($field_label == 'Topic Area'){
			$field_values = $topic_areas_choices;
		}

		if($field_label == 'Target Audiences'){
			$field_values = $target_audiences_choices;
		}


		// Add options after any potential starting options.
		$options = array_merge( $options, $field_values );
		$options = array_map( 'trim', $options );

		$field_classes = [
			'tribe-section-content-row',
			'tribe-field-type-' . $field_type, 
			'tribe_' . $field_label
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

<script>
	jQuery( document ).ready(function() {
		var grant_prog_choices = <?php echo json_encode($grant_program_choices_js);?>;
		var award_choices = <?php echo json_encode($award_choices_js);?>;
		var gp_selected = jQuery('#tribe_custom-_ecp_custom_3-GrantProject option:selected').text();
		var ga_selected = jQuery('#tribe_custom-_ecp_custom_5-Award option:selected').text();

		if(jQuery.trim(gp_selected) == 'Select Grant Project'){
			jQuery('.tribe-section-content-row.tribe-field-type-dropdown.tribe_Award').hide();
		}
		if(jQuery.trim(ga_selected) === 'Select Grant Project'){
			jQuery('.tribe-section-content-row.tribe-field-type-checkbox.tribe_GrantPrograms').hide();
		}
		jQuery('#tribe_custom-_ecp_custom_3-GrantProject').select2().on("change", function(e) {
			jQuery('.tribe-section-content-row.tribe-field-type-dropdown.tribe_Award').show();
			var select_grant_project = jQuery(this).val();
			var a_choices = [];
			a_choices.push(award_choices[ select_grant_project ]);
			options = '{id:';
			jQuery('#tribe_custom-_ecp_custom_5-Award option[value]').remove();
			var afield = jQuery('#tribe_custom-_ecp_custom_5-Award');
			afield.append( jQuery('<option></option>').val('').html('Select Award') );
			jQuery.each(a_choices, function(val, text) {
				afield.append( jQuery('<option></option>').val(a_choices).html(a_choices) );
			});
		});
		jQuery('#tribe_custom-_ecp_custom_5-Award').select2().on("change", function(e) {
			jQuery('.tribe-section-content-row.tribe-field-type-checkbox.tribe_GrantPrograms').show();
			var select_award = jQuery(this).val();
			var choices = grant_prog_choices[ select_award ];
			var field = jQuery('.tribe-section-content-row.tribe-field-type-checkbox.tribe_GrantPrograms');
			var html='<td class="tribe-section-content-label"><label for="tribe_custom-_ecp_custom_6-GrantPrograms" class="">Grant Programs: </label></td>';
			html+='<td class="tribe-section-content-field tribe-columns-list">';
			var html_loop='';
			i = 0;
			if (Array.isArray(choices)){
				choices.forEach(element => {
					html_loop += '<label for="tribe_custom-_ecp_custom_6-GrantPrograms-'+ i + '">'+
					'<input id="tribe_custom-_ecp_custom_6-GrantPrograms-' + i +'" type="checkbox" name="_ecp_custom_6[]" class="" value="' + element + '"> ' + element + '</label>';
					i++;
				});
			}
			html = html+ html_loop + '</td>';
			field[0].innerHTML = html;
		});
	});
</script>