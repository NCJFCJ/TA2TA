<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Table_Generator;
/**
 * Makes it possible to attach the generator to different classes.
 */
trait Generator_Aware
{
    /**
     * Get the instance of the generator.
     *
     * @return Table_Generator
     */
    public function get_generator()
    {
        return $this->generator;
    }
    /**
     * Attach generator to the instance.
     *
     * @param Table_Generator $generator
     * @return self
     */
    public function attach_table_generator(Table_Generator $generator)
    {
        $this->generator = $generator;
        return $this;
    }
}
