<?php

namespace IAWP;

/** @internal */
class Interrupt
{
    private $template;
    public function __construct(string $template)
    {
        $this->template = $template;
    }
    public function render(?array $options = null) : void
    {
        if ($this->is_admin_page()) {
            \add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        }
        \add_action('admin_menu', function () use($options) {
            $title = \IAWP\Capability_Manager::white_labeled() ? \esc_html__('Analytics', 'independent-analytics') : 'Independent Analytics';
            \add_menu_page($title, \esc_html__('Analytics', 'independent-analytics'), \IAWP\Capability_Manager::can_view_string(), 'independent-analytics', function () use($options) {
                $this->render_page($options);
            }, 'dashicons-analytics', 3);
        });
    }
    public function enqueue_styles()
    {
        \wp_register_style('iawp-styles', \IAWPSCOPED\iawp_url_to('dist/styles/style.css'), [], \IAWP_VERSION);
        \wp_enqueue_style('iawp-styles');
    }
    private function is_admin_page() : bool
    {
        $page = $_GET['page'] ?? null;
        return \is_admin() && $page === 'independent-analytics';
    }
    private function render_page(?array $options) : void
    {
        if (\is_null($options)) {
            $options = [];
        }
        ?>
        <div id="iawp-parent" class="iawp-parent">
            <?php 
        echo \IAWPSCOPED\iawp_blade()->run('partials.interrupt-header');
        ?>
            <?php 
        echo \IAWPSCOPED\iawp_blade()->run($this->template, $options);
        ?>
        </div>
        <?php 
    }
}
