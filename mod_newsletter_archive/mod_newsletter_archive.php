<?php
/**
* @package  mod_newsletter_archive
* @copyright (C) 2016 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

/* ---=== Generate a listing of archived newsletters ===--- */

// get a list of all files
$files = scandir(JPATH_SITE . '/images/newsletters');

// remove anything that isn't a PDF
foreach($files as $key => $file){
	if(substr($file, -4, 4) !== '.pdf'){
		unset($files[$key]);
	}
}

// reverse the array so the newest items are always on top
$files = array_reverse($files);

// loop through and display each
$lastYear = '';
foreach($files as $file){
	// check if a new year heading is required
	$curYear = substr($file, 0, 4);
	$firstRecord = ($file === reset($files));
	if($curYear != $lastYear){
		if(!$firstRecord){
			echo '</ul>';
		}
		echo '<h4>' . $curYear . '</h4><ul>';
		$lastYear = $curYear;
	}

	echo '<li><a href="/images/newsletters/' . $file . '" target="_blank">' . date('F', strtotime(substr($file, 0, -4)))  . '</a>' . ($firstRecord ? ' <span class="label label-warning">New</span>' : '') . '</li>';
}
echo '</ul>';