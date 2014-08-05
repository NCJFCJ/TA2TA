<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @param	array	A named array
 * @return	array
 */
function LibraryBuildRoute(&$query){
	$segments = array();
    
	if(isset($query['task'])){
		$segments[] = implode('/',explode('.',$query['task']));
		unset($query['task']);
	}
	if(isset($query['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/library/task/id/Itemid
 *
 * index.php?/library/id/Itemid
 */
function LibraryParseRoute($segments){
	$vars = array();
    
	// view is always the first element of the array
	$count = count($segments);
    
    if($count){
		$count--;
		$segment = array_pop($segments);
		if(is_numeric($segment)){
			$vars['id'] = $segment;
		}else{
            $count--;
            $vars['task'] = array_pop($segments) . '.' . $segment;
        }
	}

	if($count){   
        $vars['task'] = implode('.',$segments);
	}
	return $vars;
}
