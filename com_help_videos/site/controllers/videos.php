<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Events list controller class.
 */
class Help_videosControllerVideos extends Help_videosController{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Videos', $prefix = 'Help_videosModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}