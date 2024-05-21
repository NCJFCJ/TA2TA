<?php
/**
 * Document Image
 *
 * This template can be overridden by copying it to yourtheme/document-library-pro/grid-card/image.php.
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

<div class="dlp-grid-card-featured-img">

	<?php 
	
	$attachment_id = get_post_meta($document->get_id(), '_dlp_attached_file_id', true);
	$attachment = wp_get_attachment_url($attachment_id);
	$image_url = str_replace('.pdf','-pdf.jpg', $attachment);
	$image_url = str_replace('--pdf','-pdf', $image_url); //some cases have double -- causes image not to be found
	$image_string = '<div class="container-image-document"><img class="image-document" src="' . $image_url . '" /></div>';
	
	echo  $image_string;

	?>

</div>
