<?php
/**
* @package  mod_newsletter_archive_image
* @copyright (C) 2016 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

/* ---=== Determine the first newsletter in the archive and generate an image for it and display that image ===--- */

// get a list of all files
$files = scandir(JPATH_SITE . '/images/newsletters');

// remove anything that isn't a PDF
foreach($files as $key => $file){
	if(substr($file, -4, 4) !== '.pdf'){
		unset($files[$key]);
	}
}

// reverse the array so the newest item is first
$files = array_reverse($files);

if(isset($files[0]) && !empty($files[0])){
	$file = substr($files[0], 0, -4);
	
	// check if the image does not exist, create it
	if(!file_exists(JPATH_SITE . '/images/newsletters/' . $file . '.png')){
 		$im = new Imagick();
		$im->readimage(JPATH_SITE . '/images/newsletters/' . $file . '.pdf[0]');
		$im->setImageFormat('png');
		$imWidth = $im->getImageWidth();
		$im->cropImage(520, 400, ($imWidth - 520) /2, 0);
		$im->writeImage(JPATH_SITE . '/images/newsletters/' . $file . '.png');
		$im->clear();
		$im->destroy();
	}

	echo '<a href="/images/newsletters/' . $file . '.pdf" target="_blank"><img alt="' . date('F Y', strtotime(substr($file, 0, -4))) . ' Newsletter" src="/images/newsletters/' . $file . '.png" style="border: 1px solid #AAA; box-shadow: 5px 5px 2px #CCC;"></a>';
}