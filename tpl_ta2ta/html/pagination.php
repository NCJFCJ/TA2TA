<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 */

defined('_JEXEC') or die;

/**
 * Renders the pagination list
 *
 * @param   array   $list  Array containing pagination information
 *
 * @return  string         HTML markup for the full pagination object
 *
 * @since   3.0
 */
function pagination_list_render($list){
	// Calculate to display range of pages
	$currentPage = 1;
	$range = 1;
	$step = 5;
	foreach ($list['pages'] as $k => $page){
		if(!$page['active']){
			$currentPage = $k;
		}
	}
	if($currentPage >= $step){
		if($currentPage % $step == 0){
			$range = ceil($currentPage / $step) + 1;
		}else{
			$range = ceil($currentPage / $step);
		}
	}

	$html = '<ul class="pagination">';
	$html .= $list['start']['data'];
	$html .= $list['previous']['data'];

	foreach($list['pages'] as $k => $page){
		if(in_array($k, range($range * $step - ($step + 1), $range * $step))){
			if(($k % $step == 0 || $k == $range * $step - ($step + 1)) && $k != $currentPage && $k != $range * $step - $step){
				$page['data'] = preg_replace('#(<a.*?>).*?(</a>)#', '$1...$2', $page['data']);
			}
		}
		$html .= $page['data'];
	}

	$html .= $list['next']['data'];
	$html .= $list['end']['data'];

	$html .= '</ul>';
	return $html;
}

/**
 * Renders an active item in the pagination block
 *
 * @param   JPaginationObject  $item  The current pagination object
 *
 * @return  string                    HTML markup for active item
 *
 * @since   3.0
 */
function pagination_item_active(&$item){
	return '<li><a title="' . $item->text . '" href="' . $item->link . '" class="pagenav">' . $item->text . '</a></li>';
}

/**
 * Renders an inactive item in the pagination block
 *
 * @param   JPaginationObject  $item  The current pagination object
 *
 * @return  string  HTML markup for inactive item
 *
 * @since   3.0
 */
function pagination_item_inactive(&$item){
	// Check for "Start" item
	if($item->text == JText::_('JLIB_HTML_START')){
		return '<li class="disabled"><a>' . $item->text. '</a></li>';
	}

	// Check for "Prev" item
	if($item->text == JText::_('JPREV')){
		return '<li class="disabled"><a>' . $item->text. '</a></li>';
	}

	// Check for "Next" item
	if($item->text == JText::_('JNEXT')){
		return '<li class="disabled"><a>' . $item->text. '</a></li>';
	}

	// Check for "End" item
	if($item->text == JText::_('JLIB_HTML_END')){
		return '<li class="disabled"><a>' . $item->text. '</a></li>';
	}

	// Check if the item is the active page
	if(isset($item->active) && ($item->active)){
		return '<li class="active hidden-phone"><a>' . $item->text . '</a></li>';
	}

	// Doesn't match any other condition, render a normal item
	return '<li class="disabled hidden-phone"><a>' . $item->text . '</a></li>';
}