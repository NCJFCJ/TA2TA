<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits;

trait Paths
{
    /**
     * Path to the library.
     *
     * @var string
     */
    private $library_path;
    /**
     * URL to the library.
     *
     * @var string
     */
    private $library_url;
    /**
     * Set the path to the library.
     *
     * @param string $path
     * @return self
     */
    public function set_library_path(string $path)
    {
        $this->library_path = $path;
        return $this;
    }
    /**
     * Get the path to the library.
     *
     * @return string
     */
    public function get_library_path()
    {
        return $this->library_path;
    }
    /**
     * Set the URL to the library.
     *
     * @param string $url
     * @return self
     */
    public function set_library_url(string $url)
    {
        $this->library_url = $url;
        return $this;
    }
    /**
     * Get the URL to the library.
     *
     * @return string
     */
    public function get_library_url()
    {
        return $this->library_url;
    }
}
