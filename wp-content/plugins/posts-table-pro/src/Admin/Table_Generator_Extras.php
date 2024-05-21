<?php
namespace Barn2\Plugin\Posts_Table_Pro\Admin;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Extra_Fields;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;

/**
 * Setup the list of extra fields for the table generator's edit page.
 */
class Table_Generator_Extras extends Extra_Fields {

    /**
	 * {@inheritdoc}
	 */
    public function get_extra_fields() {
		return [
			[
				'type'  => 'text',
				'label' => __( '%s per page', 'posts-table-pro' ),
				'name'  => 'rows_per_page',
			],
			[
				'type'        => 'text',
				'label'       => __( '%s limit', 'posts-table-pro' ),
				'description' => __( 'The maximum number of %contentType% in one table.', 'posts-table-pro' ),
				'name'        => 'post_limit',
			],
			[
				'type'    => 'select',
				'label'   => __( 'Search box', 'posts-table-pro' ),
				'name'    => 'search_box',
				'options' => Util::parse_array_for_dropdown(
					[
						'top'    => __( 'Above table', 'posts-table-pro' ),
						'bottom' => __( 'Below table', 'posts-table-pro' ),
						'both'   => __( 'Above and below table', 'posts-table-pro' ),
						'false'  => __( 'Hidden', 'posts-table-pro' )
					]
				),
			],
			[
				'type'        => 'checkbox',
				'label'       => __( 'Caching', 'posts-table-pro' ),
				'description' => __( 'Cache table contents to improve load times.', 'posts-table-pro' ),
				'name'        => 'cache',
			],
			[
				'type'        => 'text',
				'label'       => __( 'Button text', 'posts-table-pro' ),
				'description' => sprintf( __( 'If your table uses the "button" column. <a href="%s" target="_blank">Read more</a>', 'posts-table-pro' ), 'https://barn2.com/kb/posts-table-button-column' ),
				'name'        => 'button_text',
			],
		];
	}

}