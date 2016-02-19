<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Event controller class.
 */
class LibraryControllerApproval extends LibraryController{
	// default error message
	protected $errorMessage = 'An error occured, please try again later.';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Edit', $prefix = 'LibraryModel', $config = array()){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}