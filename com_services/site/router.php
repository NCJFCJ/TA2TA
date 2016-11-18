<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @param	array	A named array
 * @return	array
 */
function ServicesBuildRoute(&$query){
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
 * index.php?/com_services/alias
 */
function ServicesParseRoute($segments){
	$vars = array();

	$vars['alias'] = str_replace(':', '-', array_pop($segments));
	if(strpos($_SERVER['REQUEST_URI'], 'registration')){
		$vars['view'] = 'registration';
	}else{
		$vars['view'] = 'portal';
	}

	return $vars;
}