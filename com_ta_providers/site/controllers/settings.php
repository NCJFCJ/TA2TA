<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * TA Provider Settings controller class
 */
class Ta_providersControllerSettings extends Ta_providersController{
	
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
	public function getModel($name = 'Settings', $prefix = 'Ta_providersModel', $config = array()){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Settings', 'Ta_providersModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Validate the posted data.
		$data = $this->validate($data);
		
		// Check for errors.
		if($data === false){
			// Save the data in the session.			
			$app->setUserState('com_ta_providers.edit.settings.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_ta_providers&view=settings', false));
			return false;
		}else{	
			// Handle the file upload.
			$this->logoUpload($data);
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_ta_providers.edit.settings.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_ta_providers&view=settings', false));
			return false;
		}

    // Redirect to the list screen.
    $this->setMessage(JText::_('COM_TA_PROVIDERS_ITEM_SAVED_SUCCESSFULLY'));
    $menu = JFactory::getApplication()->getMenu();
    $item = $menu->getActive();
    $this->setRedirect(JRoute::_($item->link, false));

		// Flush the data from the session.
		$app->setUserState('com_ta_providers.edit.settings.data', null);
	}

	/**
	 * Handles the logo upload logic
	 */
	function logoUpload($data){
		if(!empty($data['logo'])){
			// check if the logo is in the temporary folder
			$tmpPath = JPATH_SITE . '/media/com_ta_providers/tmp/' . $data['logo'];
			if(file_exists($tmpPath)){
				// process the logo image

				// grab variables
				$height = filter_var($data['logoHeight'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				$width = filter_var($data['logoWidth'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				$x = filter_var($data['logoX'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				$y = filter_var($data['logoY'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				
				// crop and resize
				$this->cropAndResizeImage($tmpPath, $tmpPath, $width, $height, $x, $y);

				// move the tmp file into its permenant home
				rename($tmpPath, JPATH_SITE . '/media/com_ta_providers/logos/' . $data['logo']);
			}
		}
	}


	/**
	 * Crops an image uploaded by the user and resizes it.
	 */
	protected function cropAndResizeImage($thumb_image_name, $image, $width, $height, $x, $y){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		$newImage = imagecreatetruecolor(450,280);
		switch($imageType){
			case 'image/gif':
				$source=imagecreatefromgif($image); 
				break;
	    case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$source=imagecreatefromjpeg($image); 
				break;
	    case 'image/png':
			case 'image/x-png':
				$source=imagecreatefrompng($image); 
				break;
	  	}
  	imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
		imagealphablending($newImage, false);
		imagesavealpha($newImage, true);

		imagecopyresampled($newImage,$source,0,0,$x,$y,450,280,$width,$height);
		switch($imageType){
			case 'image/gif':
	  		imagegif($newImage,$thumb_image_name); 
				break;
    	case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
	  		imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case 'image/png':
			case 'image/x-png':
				imagepng($newImage,$thumb_image_name);  
				break;
	    }
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}
    
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		$app = JFactory::getApplication();

		// name
		if(empty($data['name'])){
			// name is required
			$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_REQUIRED'), 'warning');
			return false;
		}else{
			$data['name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);
			if(strlen($data['name']) > 150){
				// name too long
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_TOO_LONG'), 'warning');
				return false;
			}
			if(strlen($data['name']) < 4){
				// name too short
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_TOO_SHORT'), 'warning');
				return false;
			}
			if(!preg_match('/^[a-zA-Z0-9-@&():,\[\]\'\"\-\.\/\\ ]*$/', $data['name'])){
				// name is invalid
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_INVALID'), 'warning');
				return false;
			}
		}

		// website
		if(!empty($data['website'])){
			$data['website'] = filter_var($data['website'], FILTER_SANITIZE_URL);
			if(strlen($data['website']) > 255){
				// website too long
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE_TOO_LONG'), 'warning');
				return false;
			}
			if(strlen($data['website']) < 7){
				// website is too short
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE_TOO_SHORT'), 'warning');
				return false;
			}
			if(!filter_var($data['website'], FILTER_VALIDATE_URL)){
				// website is not a valid URL
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE_INVALID'), 'warning');
				return false;
			}
		}

		// logo
		if(!empty($data['logo'])){
			$data['logo'] = filter_var($data['logo'], FILTER_SANITIZE_STRING);
			if(strlen($data['logo']) > 50){
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_LOGO_INVALID'), 'error');
				return false;
			}
			if(strlen($data['logo']) < 5){
				$app->enqueueMessage(JText::_('COM_TA_PROVIDERS_SETTINGS_LOGO_INVALID'), 'error');
				return false;
			}
		}

		return $data;
	}
}