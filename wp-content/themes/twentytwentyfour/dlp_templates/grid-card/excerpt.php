<?php
/**
 * Document Excerpt
 *
 * This template can be overridden by copying it to yourtheme/dlp_templates/grid-card/excerpt.php.
 *
 * HOWEVER, on occasion Barn2 will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version   1.0
 * @package   Document_Library_Pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="dlp-grid-card-excerpt">
	<?php 
		$org = get_post_meta($document->get_id(), 'organization_for_library', true);
		$link = get_post_meta($document->get_id(), 'external_link_for_library', true);
		$data_string = '<br/><br/> By: <a target="_blank" href="' . $link . '">' . $org . '</a>';
		$content .= $data_string;
		echo  $content;
		
	?>
</div>
