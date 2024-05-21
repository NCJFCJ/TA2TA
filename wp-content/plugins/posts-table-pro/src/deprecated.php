<?php

use Barn2\Plugin\Posts_Table_Pro\Data\Abstract_Table_Data;
use Barn2\Plugin\Posts_Table_Pro\Table_Args;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Table\Table_Data_Interface;

/**
 * @deprecated 2.3 Replaced by Barn2\Plugin\Posts_Table_Pro\Table_Args
 */
class Posts_Table_Args extends Table_Args {

	public function __construct( array $args = [] ) {
		_deprecated_function( __METHOD__, '2.3', Table_Args::class );
		parent::__construct( $args );
	}

}

/**
 * @deprecated 2.3 Replaced by Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Table\Table_Data_Interface
 */
interface Posts_Table_Data extends Table_Data_Interface {

}

/**
 * @deprecated 2.3 Replaced by Barn2\Plugin\Posts_Table_Pro\Data\Abstract_Table_Data
 */
abstract class Abstract_Posts_Table_Data extends Abstract_Table_Data {

	public function __construct( $post, $links = '' ) {
		_deprecated_function( __METHOD__, '2.3', Abstract_Table_Data::class );
		parent::__construct( $post, $links );
	}

}
