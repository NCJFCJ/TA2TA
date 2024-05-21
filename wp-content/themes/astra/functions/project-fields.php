<?php
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_65bbe7964b7c4',
	'title' => 'Project fields',
	'fields' => array(
		array(
			'key' => 'field_65bbe79792522',
			'label' => 'Summary',
			'name' => 'summary',
			'aria-label' => '',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'acfe_textarea_code' => 1,
			'maxlength' => '',
			'rows' => '',
			'placeholder' => '',
			'new_lines' => '',
		),
		array(
			'key' => 'field_65bbe8c692526',
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
			'acfe_repeater_stylised_button' => 0,
			'layout' => 'table',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Row',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_65bbe8e992527',
					'label' => 'Firstname',
					'name' => 'firstname',
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
					'parent_repeater' => 'field_65bbe8c692526',
				),
				array(
					'key' => 'field_65bbe90492528',
					'label' => 'Lastname',
					'name' => 'lastname',
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
					'parent_repeater' => 'field_65bbe8c692526',
				),
				array(
					'key' => 'field_65bbeb6ea2991',
					'label' => 'Position',
					'name' => 'position',
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
					'parent_repeater' => 'field_65bbe8c692526',
				),
				array(
					'key' => 'field_65bbe91192529',
					'label' => 'Phone',
					'name' => 'phone',
					'aria-label' => '',
					'type' => 'number',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'min' => '',
					'max' => '',
					'placeholder' => '',
					'step' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_65bbe8c692526',
				),
				array(
					'key' => 'field_65bbe9619252a',
					'label' => 'Email',
					'name' => 'email_',
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
					'parent_repeater' => 'field_65bbe8c692526',
				),
				array(
					'key' => 'field_65bbe9799252b',
					'label' => '',
					'name' => '',
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
					'parent_repeater' => 'field_65bbe8c692526',
				),
			),
		),
		array(
			'key' => 'field_65bbe9a9f103e',
			'label' => 'Grant Programs Served',
			'name' => 'grant_programs_served',
			'aria-label' => '',
			'type' => 'acfe_taxonomy_terms',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => array(
				0 => 'grant-program',
			),
			'allow_terms' => array(
				0 => 'all_grant-program',
			),
			'allow_level' => '',
			'field_type' => 'checkbox',
			'default_value' => array(
			),
			'return_format' => 'object',
			'layout' => 'vertical',
			'toggle' => 1,
			'save_terms' => 1,
			'load_terms' => 1,
			'choices' => array(
			),
			'ui' => 0,
			'multiple' => 0,
			'allow_null' => 0,
			'ajax' => 0,
			'placeholder' => '',
			'search_placeholder' => '',
			'allow_custom' => 0,
			'other_choice' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'projects',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'left',
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
