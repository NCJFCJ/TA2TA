<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

/**
 * The class injects the custom page header to Barn2 Settings pages
 * that make use of the table generator library.
 */
class Page_Header
{
    /**
     * Display the custom header into the options page.
     *
     * @param string $title the title to display inside the header.
     * @param array $header_links list of links to display in the header.
     * @return void
     */
    public static function display_header($title, $header_links = [])
    {
        ?>
		<div class="barn2-layout__header">
			<div class="barn2-layout__header-wrapper">
				<p class="barn2-layout__header-heading"><?php 
        echo $title;
        ?></p>
				<div class="links-area">
					<?php 
        foreach ($header_links as $link) {
            ?>
						<a href="<?php 
            echo \esc_html($link['url']);
            ?>"><?php 
            echo \esc_html($link['title']);
            ?></a>
						<span class="separator">|</span>
					<?php 
        }
        ?>
				</div>
			</div>
		</div>
		<?php 
    }
}
