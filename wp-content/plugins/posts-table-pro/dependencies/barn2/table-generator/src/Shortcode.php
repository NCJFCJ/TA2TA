<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database\Query;
/**
 * Handles registration and rendering of the new shortcode.
 */
class Shortcode
{
    /**
     * Slug of the shortcode to register.
     *
     * @var string
     */
    protected $slug;
    /**
     * Table Generator instance.
     *
     * @var Table_Generator
     */
    protected $generator;
    /**
     * Instance of the plugin using the generator.
     *
     * @var object
     */
    protected $args_resolver;
    /**
     * Initialize the shortcode's registration.
     *
     * @param string $slug
     * @param Table_Generator $generator
     */
    public function __construct(string $slug, Table_Generator $generator)
    {
        $this->slug = $slug;
        $this->generator = $generator;
        $this->args_resolver = $generator->get_args_resolver();
        $this->boot();
    }
    /**
     * Hook into WP.
     *
     * @return void
     */
    public function boot()
    {
        \add_shortcode($this->slug, [$this, 'render_shortcode']);
    }
    /**
     * Render the shortcode.
     *
     * @param array $atts
     * @return string
     */
    public function render_shortcode($atts)
    {
        $attributes = \shortcode_atts(['id' => null], $atts);
        $table_id = $attributes['id'];
        if (empty($table_id)) {
            return;
        }
        $table = (new Query($this->generator->get_database_prefix()))->get_item($table_id);
        if (!$table instanceof Content_Table) {
            return;
        }
        return \Barn2\Plugin\Posts_Table_Pro\Table_Shortcode::do_shortcode($this->get_parameters($table, $atts));
    }
    /**
     * Get the parameters for the table.
     *
     * @param Content_Table $table the Content Table
     * @param array $attributes additional attributes that should be merged with the table.
     * @return array
     */
    private function get_parameters(Content_Table $table, $attributes)
    {
        $args = $this->args_resolver::get_site_defaults();
        // Grab the content type from the table.
        $content_type = $table->get_content_type();
        // Set the content type for the shortcode.
        $args['post_type'] = $content_type;
        // Inject mime type if exists.
        if ($mime_type = $table->get_mime_type(\true)) {
            $args['post_type'] .= ':' . $mime_type;
        }
        // Inject "category" argument if it's a post table.
        if ($table->supports_categories() && !empty($table->get_categories())) {
            $args['category'] = $table->get_categories(\true);
        }
        // Inject "tag" argument if it's a post table.
        if ($table->supports_tags() && !empty($table->get_tags())) {
            $args['tag'] = $table->get_tags(\true);
        }
        // Inject the post status argument.
        if (!empty($table->get_post_status())) {
            $args['status'] = $table->get_post_status(\true);
        }
        // Inject the "author" parameter.
        if (\post_type_supports($content_type, 'author') && !empty($table->get_author())) {
            $args['author'] = $table->get_author(\true);
        }
        // Inject the "include" (specific posts ids) parameter.
        if (!empty($table->get_specific_ids())) {
            $args['include'] = $table->get_specific_ids(\true);
        }
        // Inject the date parameters.
        if (!empty($table->get_year())) {
            $args['year'] = $table->get_year();
        }
        if (!empty($table->get_month())) {
            $args['month'] = $table->get_month();
        }
        if (!empty($table->get_day())) {
            $args['day'] = $table->get_day();
        }
        // Inject custom fields.
        if (!empty($table->get_custom_fields())) {
            $args['cf'] = $table->get_custom_fields(\true);
        }
        // Inject terms.
        if (!empty($table->get_terms())) {
            $args['term'] = $table->get_terms(\true);
        }
        // Now start the exclusion parameters.
        // Inject the "exclude category" argument if it's a post table.
        if ($table->supports_categories() && !empty($table->get_categories(\false, \true))) {
            $args['exclude_category'] = $table->get_categories(\true, \true);
        }
        // Inject the "exclude" (specific posts ids) parameter.
        if (!empty($table->get_specific_ids(\false, \true))) {
            $args['exclude'] = $table->get_specific_ids(\true, \true);
        }
        // Inject columns configuration.
        if (!empty($table->get_columns(\true))) {
            $args['columns'] = $table->get_columns(\true);
        }
        // Add support for the lazy load param.
        $lazy_load = $table->get_setting('lazy_load', \false);
        if ($lazy_load) {
            $args['lazy_load'] = \true;
        }
        // Add support for the cache param.
        $cache = $table->get_setting('cache', \false);
        if ($cache) {
            $args['cache'] = \true;
        }
        // Add support for the button_text param.
        $button_text = $table->get_setting('button_text', \false);
        if ($button_text) {
            $args['button_text'] = $button_text;
        }
        // Inject search box state.
        $search_box = $table->get_setting('search_box', \false);
        $args['search_box'] = $search_box ? 'top' : 'false';
        // Inject the sort by state.
        $sort_by = $table->get_setting('sortby', 'date');
        $args['sort_by'] = !empty($sort_by) ? $sort_by : 'date';
        // Inject sort order.
        $sort_order = $table->get_setting('sort_order', '');
        $args['sort_order'] = $sort_order;
        // Inject filters parameter.
        $args['filters'] = $table->get_filters(\true);
        // Merge remaining attributes.
        if (!empty($attributes)) {
            unset($attributes['id']);
            // ID is not needed.
            if (!empty($attributes)) {
                $args = \array_merge($args, $attributes);
            }
        }
        return $args;
    }
}
