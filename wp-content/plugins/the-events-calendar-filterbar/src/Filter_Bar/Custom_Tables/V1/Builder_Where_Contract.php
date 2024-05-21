<?php
/**
 * Interface Builder_Where_Contract in order to allow custom filters to override the `where` clause created by
 * the filters.
 *
 * @since 5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */
namespace TEC\Filter_Bar\Custom_Tables\V1;

use Tribe\Events\Filterbar\Views\V2\Filters_Stack;

/**
 * Interface Builder_Where_Contract
 *
 * @since 5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */
interface Builder_Where_Contract {
	/**
	 * Allow external stacks to override the value of the builder.
	 *
	 * @param Filters_Stack $stack The current stack running against the query.
	 *
	 * @return string A where method to build the SQL query.
	 */
	public function build_where( Filters_Stack $stack );
}