<?php

namespace Barn2\Plugin\Posts_Table_Pro\Data;

/**
 * Gets post data for the content column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Content extends Abstract_Table_Data {

	public function get_data() {
		$content = apply_filters( 'the_content', get_the_content( '' ) );

		return apply_filters( 'posts_table_data_content', $content, $this->post );
	}

}
