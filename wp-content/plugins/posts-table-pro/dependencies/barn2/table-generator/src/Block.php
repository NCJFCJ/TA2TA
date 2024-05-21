<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits\Paths;
use JsonSerializable;
/**
 * Handles registration and booting of the Gutenberg block.
 */
class Block implements JsonSerializable
{
    use Paths;
    /**
     * The label of the block.
     *
     * @var string
     */
    public $label;
    /**
     * Instructions to display inside the placeholder.
     *
     * @var string
     */
    public $instructions;
    /**
     * Description of the block.
     *
     * @var string
     */
    public $description;
    /**
     * URL to the documentation article containing the full list of options.
     *
     * @var string
     */
    public $options_doc_url;
    /**
     * Slug of the plugin registering the block.
     *
     * @var string
     */
    public $plugin_slug;
    /**
     * Instance of the table generator.
     *
     * @var Table_Generator
     */
    private $generator;
    /**
     * Initialize the gutenberg block.
     *
     * @param Table_Generator $generator
     */
    public function __construct(Table_Generator $generator)
    {
        $this->generator = $generator;
        $this->set_library_path($generator->get_library_path());
        $this->set_library_url($generator->get_library_url());
        $this->set_plugin_slug($generator->get_plugin()->get_slug());
    }
    /**
     * Get the slug of the plugin registering the block.
     *
     * @return string
     */
    public function get_plugin_slug()
    {
        return $this->plugin_slug . '-block';
    }
    /**
     * Set the slug of the plugin registering the block.
     *
     * @param string $slug
     * @return self
     */
    public function set_plugin_slug(string $slug)
    {
        $this->plugin_slug = $slug;
        return $this;
    }
    /**
     * Get the label of the block.
     *
     * @return string
     */
    public function get_label()
    {
        return $this->label;
    }
    /**
     * Set the label of the block.
     *
     * @param string $label
     * @return self
     */
    public function set_label(string $label)
    {
        $this->label = $label;
        return $this;
    }
    /**
     * Get the instructions of the block.
     *
     * @return string
     */
    public function get_instructions()
    {
        return $this->instructions;
    }
    /**
     * Set instructions for the block.
     *
     * @param string $instructions
     * @return self
     */
    public function set_instructions(string $instructions)
    {
        $this->instructions = $instructions;
        return $this;
    }
    /**
     * Get description of the block.
     *
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }
    /**
     * Set the description of the block.
     *
     * @param string $string
     * @return self
     */
    public function set_description(string $string)
    {
        $this->description = $string;
        return $this;
    }
    /**
     * Set the url to the documentation article containing the full list of options.
     *
     * @param string $url
     * @return self
     */
    public function set_options_doc_url(string $url)
    {
        $this->options_doc_url = $url;
        return $this;
    }
    /**
     * Retrieve the URL to the documentation article containing the full list of options.
     *
     * @return string
     */
    public function get_options_doc_url()
    {
        return $this->options_doc_url;
    }
    /**
     * Enqueue assets for the block.
     *
     * @return void
     */
    public function assets()
    {
        $file_name = 'block';
        $integration_script_path = 'assets/build/' . $file_name . '.js';
        $integration_script_asset_path = $this->get_library_path() . 'assets/build/' . $file_name . '.asset.php';
        $integration_script_asset = \file_exists($integration_script_asset_path) ? require $integration_script_asset_path : ['dependencies' => [], 'version' => \filemtime($integration_script_path)];
        $script_url = $this->get_library_url() . $integration_script_path;
        \wp_register_script($this->get_plugin_slug(), $script_url, $integration_script_asset['dependencies'], $integration_script_asset['version'], \true);
        \wp_register_style($this->get_plugin_slug(), $this->get_library_url() . 'assets/build/block.css', [], $integration_script_asset['version']);
        \wp_add_inline_script($this->get_plugin_slug(), 'const Barn2TableBlock = ' . \wp_json_encode($this), 'before');
    }
    /**
     * Boot the block.
     *
     * @return void
     */
    public function boot()
    {
        \add_action('admin_enqueue_scripts', [$this, 'assets']);
        \register_block_type('barn2-table-generator/' . $this->get_plugin_slug(), ['$schema' => 'https://schemas.wp.org/trunk/block.json', 'apiVersion' => 2, 'category' => 'widgets', 'icon' => 'editor-table', 'supports' => ['html' => \false], 'title' => $this->get_label(), 'description' => $this->get_description(), 'editor_style' => $this->get_plugin_slug(), 'editor_script' => $this->get_plugin_slug(), 'render_callback' => [$this, 'render_block']]);
    }
    /**
     * Render the block.
     *
     * @param array    $attributes     The array of attributes for this block.
     * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
     * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
     * @return string
     */
    public function render_block($attributes, $content, $block_instance)
    {
        $table_id = isset($attributes['tableID']) ? \absint($attributes['tableID']) : \false;
        $additional_options = isset($attributes['shortcodeAttributes']) ? \html_entity_decode(\esc_attr($attributes['shortcodeAttributes'])) : '';
        if (!empty($additional_options)) {
            $additional_options = \explode("\n", $additional_options);
            $additional_options = \implode(' ', $additional_options);
        }
        \ob_start();
        echo \do_shortcode('[' . $this->generator->get_shortcode() . ' id="' . $table_id . '" ' . $additional_options . ']');
        return \ob_get_clean();
    }
    /**
     * Json configuration for the block.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['blockLabel' => $this->get_label(), 'blockInstructions' => $this->get_instructions(), 'tablesApiRoute' => $this->generator->get_api_route('tables')->get_api_route(), 'restNonce' => \wp_create_nonce('wp_rest'), 'blockName' => 'barn2-table-generator/' . $this->get_plugin_slug(), 'pluginName' => $this->generator->get_plugin()->get_name(), 'optionsUrl' => $this->get_options_doc_url(), 'settingsPage' => $this->generator->get_plugin()->get_settings_page_url()];
    }
}
