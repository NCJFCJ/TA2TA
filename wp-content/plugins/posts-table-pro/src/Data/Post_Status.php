<?php

namespace Barn2\Plugin\Posts_Table_Pro\Data;

/**
 * Gets post data for the post status column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Status extends Abstract_Table_Data {

	public function get_data() {
		return apply_filters( 'posts_table_data_status', ucfirst( $this->post->post_status ), $this->post );
	}

}
