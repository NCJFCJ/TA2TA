<?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_639bbd31781e1',
	'title' => 'Directory Custom Fields',
	'fields' => array(
		array(
			'key' => 'field_63ab92f1315ad',
			'label' => 'Logo',
			'name' => '_thumbnail_id',
			'aria-label' => '',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'id',
			'library' => 'uploadedTo',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
			'preview_size' => 'medium',
			'uploader' => '',
			'acfe_thumbnail' => 0,
		),
		array(
			'key' => 'field_639bbe56016c9',
			'label' => 'Organization\'s Link',
			'name' => 'external_link_for_directory',
			'aria-label' => '',
			'type' => 'url',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
		),
		array(
			'key' => 'field_639ce5c93983f',
			'label' => 'Grant Projects',
			'name' => 'grant_projects_for_directory',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => 'label-font-size-acf',
				'id' => '',
			),
			'layout' => 'block',
			'pagination' => 1,
			'rows_per_page' => 5,
			'min' => 0,
			'max' => 0,
			'collapsed' => 'field_639ce5f839840',
			'button_label' => 'Add Project',
			'sub_fields' => array(
				array(
					'key' => 'field_639ce5f839840',
					'label' => 'Project Title',
					'name' => 'project_title',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_639ce5c93983f',
				),
				array(
					'key' => 'field_639ce61e39841',
					'label' => 'Summary',
					'name' => 'summary',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'rows' => '',
					'placeholder' => '',
					'new_lines' => '',
					'acfe_textarea_code' => 0,
					'parent_repeater' => 'field_639ce5c93983f',
				),
				array(
					'key' => 'field_63ac783fce2a3',
					'label' => 'List of Award Numbers',
					'name' => 'list_of_award_numbers',
					'aria-label' => '',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'table',
					'min' => 0,
					'max' => 0,
					'collapsed' => '',
					'button_label' => 'Add Row',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_63ac785ece2a4',
							'label' => 'Award Number',
							'name' => 'award_number',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_63ac783fce2a3',
						),
						array(
							'key' => 'field_63bb55adf3560',
							'label' => 'Archive',
							'name' => 'archive',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'parent_repeater' => 'field_63ac783fce2a3',
						),
					),
					'acfe_repeater_stylised_button' => 0,
					'parent_repeater' => 'field_639ce5c93983f',
				),
				array(
					'key' => 'field_639ce68539843',
					'label' => 'Contacts',
					'name' => 'contacts',
					'aria-label' => '',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'table',
					'min' => 0,
					'max' => 0,
					'collapsed' => '',
					'button_label' => 'Add Contact',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_639ce69239844',
							'label' => 'Full Name',
							'name' => 'full_name',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_639ce68539843',
						),
						array(
							'key' => 'field_639ce6a839846',
							'label' => 'Title',
							'name' => 'title',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_639ce68539843',
						),
						array(
							'key' => 'field_639ce6ec39848',
							'label' => 'Phone Number',
							'name' => 'phone_number',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_639ce68539843',
						),
						array(
							'key' => 'field_639ce6e039847',
							'label' => 'Email',
							'name' => 'email',
							'aria-label' => '',
							'type' => 'email',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_639ce68539843',
						),
					),
					'acfe_repeater_stylised_button' => 0,
					'parent_repeater' => 'field_639ce5c93983f',
				),
				array(
					'key' => 'field_63a92a9ddf9ce',
					'label' => 'Grant Programs',
					'name' => 'grant_programs',
					'aria-label' => '',
					'type' => 'checkbox',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => 'columns-list-acf',
						'id' => '',
					),
					'choices' => array(
						'Abuse in Later Life' => 'Abuse in Later Life',
						'Campus' => 'Campus',
						'Consolidated Youth' => 'Consolidated Youth',
						'Culturally Specific Services Program (CSSP)' => 'Culturally Specific Services Program (CSSP)',
						'Disability' => 'Disability',
						'DV Homicide' => 'DV Homicide',
						'Improving Criminal Justice Response' => 'Improving Criminal Justice Response',
						'Justice for Families' => 'Justice for Families',
						'Legal Assistance for Victims' => 'Legal Assistance for Victims',
						'Rural' => 'Rural',
						'SASP Cultural' => 'SASP Cultural',
						'Sexual Assault Services Programs (SASP)' => 'Sexual Assault Services Programs (SASP)',
						'State Domestic Violence Coalitions' => 'State Domestic Violence Coalitions',
						'State Sexual Assault Coalitions' => 'State Sexual Assault Coalitions',
						'STOP Grant Program' => 'STOP Grant Program',
						'TA Initiative' => 'TA Initiative',
						'Transitional Housing' => 'Transitional Housing',
						'Tribal Coalition' => 'Tribal Coalition',
						'Tribal Criminal Jurisdictions' => 'Tribal Criminal Jurisdictions',
						'Tribal Government' => 'Tribal Government',
						'Tribal Sexual Assault Program (TSASP)' => 'Tribal Sexual Assault Program (TSASP)',
						'Underserved' => 'Underserved',
					),
					'default_value' => array(
					),
					'return_format' => 'value',
					'allow_custom' => 0,
					'layout' => 'vertical',
					'toggle' => 0,
					'save_custom' => 0,
					'custom_choice_button_text' => 'Add new choice',
					'parent_repeater' => 'field_639ce5c93983f',
				),
				array(
					'key' => 'field_639ce7f43984f',
					'label' => 'Archived',
					'name' => 'archived',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => 'Click below if you wish to archive the grant project',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'parent_repeater' => 'field_639ce5c93983f',
				),
			),
			'acfe_repeater_stylised_button' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'organizations',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
	'acfe_display_title' => '',
	'acfe_autosync' => '',
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
) );
} );

