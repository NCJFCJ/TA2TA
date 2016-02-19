<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

// require the helper
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');

/**
 * Library model.
 */
class LibraryModelitem extends JModelAdmin{

	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_LIBRARY';	
	 
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true){
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_library.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form)){
			return false;
		}

		return $form;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Item', $prefix = 'LibraryTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_library.edit.item.data', array());

		if(empty($data)){
			$data = $this->getItem();
			
			// Support for target audiences
			$data->target_audiences = '';
			if($data->id){
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('target_audience'));
				$query->from($db->quoteName('#__library_target_audiences'));
				$query->where($db->quoteName('library_item') . ' = ' . $data->id);
				$db->setQuery($query);
				$data->target_audiences = implode(',',$db->loadColumn());
			}            
		}

		return $data;
	}
	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table){
		jimport('joomla.filter.output');

		if(empty($table->id)){
			// Set ordering to the last item if not set
			if (@$table->ordering === ''){
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tapd_items');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   12.2
	 */
	public function save($data){
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();

		if((!empty($data['tags']) && $data['tags'][0] != '')){
			$table->newTags = $data['tags'];
		}

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		/* --- Process Approved and Modified Dates --- */
		
		$curDateTime = gmdate('Y-m-d H:i:s');
		$user = $user = JFactory::getUser();
			
		// Support for modified fields
		if($data['id']){
			$data['modified'] = $curDateTime;
			$data['modified_by'] = $user->id;
		}else{
			$data['created'] = $curDateTime;
			$data['created_by'] = $user->id;
		}

		// Support for deleted fields
		if($data['state'] == 0){
			if($data['id']){
				// if this is an existing item, check its state first, if it didn't change, don't update the deleted field as the last person was the deleter
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('state'));
				$query->from($db->quoteName('#__library'));
				$query->where($db->quoteName('id') . ' = ' . $data['id']);
				$db->setQuery($query);
				$old_state = $db->loadResult();
				if($data['state'] != $old_state){
					// this user was the deleter
					$data['deleted'] = $curDateTime;
					$data['deleted_by'] = $user->id;
				}
			}else{
				// the user deleted this record when they created it.
				$data['deleted'] = $curDateTime;
				$data['deleted_by'] = $user->id;
			}
		}
		
		/* --- Check for Target Audiences --- */

		if(!isset($data['target_audiences']))
		{
			$this->setError(JText::_('COM_LIBRARY_NO_TARGET_AUDIENCES'));
			return false;
		}

		/* --- Process File Upload --- */
		
		// Check whether a file upload should be processed
		$fileUpload = false;
		$old_base_file_name = '';
		if(is_uploaded_file($_FILES['jform']['tmp_name']['file']) || $data['id'] == 0){
			$fileUpload = true;
			// create an empty class to hold information on the file we are uploading
			$file = new stdClass;
			$file->name = '';
			$file->type = '';
			$file->tmp_name = '';
			$file->error = 0;
			$file->size = '';
			
			// get information about the uploaded file
			if(isset($_FILES['jform'])){
				$file->name = $_FILES['jform']['name']['file'];
				$file->type = $_FILES['jform']['type']['file'];
				$file->tmp_name = $_FILES['jform']['tmp_name']['file'];
				$file->error = $_FILES['jform']['error']['file'];
				$file->size = $_FILES['jform']['size']['file'];
			}else{
				$this->setError(JText::_('COM_LIBRARY_NO_FILE'));
			    return false;
			}
			
			// check if an error occured
			if($file->error){
				switch($file->error){
					case 1:
						$this->setError(JText::_('COM_LIBRARY_FILE_TOO_LARGE'));
			        	return false;
	
			        case 2:
						$this->setError(JText::_('COM_LIBRARY_FILE_TOO_LARGE'));
			        	return false;
	 
			        case 3:
						$this->setError(JText::_('COM_LIBRARY_FILE_UPLOAD_FAILED'));
			        	return false;
	 
			        case 4:
						if($data['id'] <= 0){
							$this->setError(JText::_('COM_LIBRARY_NO_FILE'));
			        		return false;
						}
				}
			}
			
			//check for filesize
			if($file->size > 26210000){
				$this->setError(JText::_('COM_LIBRARY_FILE_TOO_LARGE'));
				return false;
			}
			
			//check the file extension is ok
			$filePathInfo = pathinfo('/'.$file->name);
			if($filePathInfo['extension'] != 'pdf'){
				$this->setError(JText::_('COM_LIBRARY_INVALID_FILE_TYPE'));
				return false;
			}
			
			//lose any special characters in the filename
			$file->name = strtolower(preg_replace('/[^A-Za-z0-9]/i', '-', $filePathInfo['filename']));
			
			// determine and set the base file name
			$base_file_name = substr($file->name, 0, 82);
			$data['base_file_name'] = $base_file_name;
			
			// if the user is changing the file (editing), grab the information for the previous file
			if($data['id'] > 0){
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('base_file_name'));
				$query->from($db->quoteName('#__library'));
				$query->where($db->quoteName('id') . ' = ' . $data['id']);
				$db->setQuery($query);
				$old_base_file_name = $db->loadResult();
			}
		}
		
		// Allow an exception to be thrown.
		try{
			// Load the row if saving an existing record.
			if ($pk > 0){
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if (!$table->bind($data)){
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check()){
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
			if (in_array(false, $result, true)){
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store()){
				$this->setError($table->getError());
				return false;
			}
			
			$db = $this->getDBO();
						
			/* -- complete the file upload -- */

			if($fileUpload){
				// construct the file path
				$file_name = $table->id.'-'.$base_file_name;
				$uploadPath = JPATH_SITE . '/media/com_library/resources/' . $file_name . '.pdf';
							
				//move the uploaded file to its permenant home
				if(!JFile::upload($file->tmp_name, $uploadPath)) {
					$this->setError(JText::_('COM_LIBRARY_FILE_MOVE_ERROR'));
					
					// erase this record so we don't end up with a record missing a file
					$query = $db->getQuery(true);
					$query->delete($db->quoteName('#__library'));
					$query->where($db->quoteName('id') . '=' . $db->quote($table->id));
					$db->setQuery($query,0,1);
					$db->query();
					
				    return false;
				}
				
				/* -- create the document image -- */
				
				$imagePath = JPATH_SITE . '/media/com_library/covers/' . $file_name . '.png';
				$im = new Imagick();
				$im->readimage($uploadPath . '[0]');
				$im->setImageFormat('png');
				$im->cropThumbnailImage(250,323);
				$im->writeImage($imagePath);
				$im->clear();
				$im->destroy();
				
				// TO DO: Actually after thinking about it maybe in your upload script instead of preventing them from uploading a file if there isn't enough space you send us an E-Mail notifying us when there is less that 25 GB of free space.  This way it can give us time to figure out what to do if the system gets close to being overloaded.
				
				/* -- remove the old file information -- */
				
				if(!empty($old_base_file_name)){
					// first, make sure the names are not the same
					if($old_base_file_name != $base_file_name){
						// delete the pdf file
						$old_file_name = $table->id . '-' . $old_file_name;
						$old_file_path = JPATH_SITE . '/media/com_library/resources/' . $old_file_name . '.pdf';
						if(file_exists($old_file_path)){
							unlink($old_file_path);
						}
						
						// delete the cover image
						$old_image_path = JPATH_SITE . '/media/com_library/covers/' . $old_file_name . '.jpg';
						if(file_exists($old_image_path)){
							unlink($old_image_path);
						}
					}
				}
			}
		 			
			/* -- process target audiences -- */
			
			if(isset($data['target_audiences'])){
				// store the data locally	
				$target_audiences = $data['target_audiences'];
				
				// remove the data from the array
				unset($data['target_audiences']);
				
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__library_target_audiences'));
				$query->where(array(
					$db->quoteName('library_item') . '=' . $db->quote($table->id),
					$db->quoteName('target_audience') . 'NOT IN(' . implode(',', $target_audiences) . ')'
				));
				$db->setQuery($query);
				$db->query();
				
				// now, add in any new records
				$query = 'INSERT INTO ' . $db->quoteName('#__library_target_audiences');
				$query .= '(' . $db->quoteName('library_item') . ',' . $db->quoteName('target_audience') . ') VALUES ';
				$values = array();
				foreach($target_audiences as $target_audience){
					$values[] = '(' . $db->quote($table->id) . ',' . $db->quote($target_audience) . ')';
				}
				$query .= implode(',', $values);
				$query .= ' ON DUPLICATE KEY UPDATE ' . $db->quoteName('target_audience') . '=' . $db->quoteName('target_audience') .';';
				$db->setQuery($query);
				$db->query();
			}else{
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__library_target_audiences'));
				$query->where($db->quoteName('library_item') . '=' . $db->quote($table->id));
				$db->setQuery($query);
				$db->query();
			}

			/* --- Send an approval email to the library moderators --- */

			// only send if this was a new library item
			if($data['id'] == 0){

				// generate an access token for this item and save it to the database
				$accessToken = LibraryHelper::generateAccessToken($table->id);

				// get the recipients for the email
				$recipients = JComponentHelper::getParams('com_library')->get('approval_emails');
				
				// only continue if there are email recipients
				if($recipients){

					// get all of the target audiences
					$target_audience_map = array();
					$query = $db->getQuery(true);
			    $query->select($db->quoteName(array(
			    	'id',
			    	'name'
			    )));
					$query->from($db->quoteName('#__target_audiences'));
					$query->where($db->quoteName('state') . ' = ' . $db->quote('1'));
					$query->order('name ASC');
					$db->setQuery($query);
					$possible_target_audiences = $db->loadObjectList();

					foreach($possible_target_audiences as $ptas){
						$target_audience_map[$ptas->id] = $ptas->name;
					}

					// get the name of the project
					$query = $db->getQuery(true);
					$query->select($db->quoteName('title'));
					$query->from($db->quoteName('#__tapd_provider_projects'));
					$query->where($db->quoteName('id') . ' = ' . $db->quote($data['project']));
					$db->setQuery($query, 0, 1);
					$project_name = $db->loadResult();

					// get the information for this user
					$user = JFactory::getUser();

					// get the organization of the user
					$user_org = LibraryHelper::getUserOrg();

					// get the organization of the document
					$doc_org = LibraryHelper::getOrg($data['org']);

					// create a mailer object	
					$mailer = JFactory::getMailer();
					
					$mailer->isHTML(true);
					$mailer->Encoding = 'base64';
					
					// set the sender to the site default
					$config = JFactory::getConfig();
					$sender = array( 
			    $config->get('config.mailfrom'),
			    $config->get('config.fromname'));

					$mailer->setSender($sender);
					
					// set the recipients
					if(strpos($recipients, ',') >= 0){
						$recipients = explode(',', $recipients);
					}
					$mailer->addRecipient($recipients);

					// set the message subject
					$mailer->setSubject('[TA2TA] New Library Item Pending Approval');

					// prepare the message content
					$message = '<p style="margin-bottom: 20px;text-align:left;">The following resource was added to the TA2TA Resource Library by ' . $user->name . ' from ' . $user_org->name . '. Please review the details and choose whether to publish (available to the public) or archive (available only to NCJFCJ and OVW) the resource.</p>';
					$message .= '<table style="border-bottom:1px solid #DDD;border-top:1px solid #DDD;padding:20px 0 30px 0;">';
					$message .= '<tbody>';
					$message .= '<tr>';
					$message .= '<td valign="top"><a href="' . str_replace('administrator/', '', JURI::base()) . 'media/com_library/resources/' . $table->id . '-' . $data['base_file_name'] . '.pdf" target="_blank"><img src="' . str_replace('administrator/', '', JURI::base()) . 'media/com_library/covers/' . $table->id . '-' . $data['base_file_name'] . '.png" alt="' . $data['name'] . '" width="150" style="margin-right: 20px;"></a></td>';
					$message .= '<td valign="top"><h3 style="margin: 0 0 5px 0;">' . $data['name'] . '</h3><h5 style="margin-bottom:15px;margin-top:0;"><strong><a href="' . $doc_org->website . '" style="text-decoration:none;" target="_blank">' . $doc_org->name . '</a></strong></h5><p><strong>Project:</strong> ' . $project_name . '</p><p>' . $data['description'] . '</p>';

					if(isset($target_audiences)){
						$message .= '<p style="margin-bottom:0;text-decoration:underline;">Target Audiences</p><table><tr><td><ul style="margin-top:0;padding-left:0;">';
						for($i = 0; $i < count($target_audiences); $i++){
							if($i == round(count($target_audiences) / 2)){
								$message .= '</ul></td><td><ul style="margin-top:0;padding-left:0;">';
							}
							if(array_key_exists($target_audiences[$i], $target_audience_map)){
								$message .= '<li>' . $target_audience_map[$target_audiences[$i]] . '</li>';
							}
						}
						$message .= '</ul></td></tr></table>';
					}

					$message .= '<div style="float:left;margin-right:15px;padding-top:10px;"><a href="' . str_replace('administrator/', '', JURI::base()) . 'media/com_library/resources/' . $table->id . '-' . $data['base_file_name'] . '.pdf" style="background-color:#428BCA;border-radius:4px;color:#FFF;padding:12px 24px;text-align:center;text-decoration:none;" target="_blank">Download</a></div><div style="float:left;margin-right:15px;padding:10px 0 0 0;"><a href="' . str_replace('administrator/', '', JURI::base()) . 'my-account/library/approve.html?token=' . $accessToken . '&state=2" style="background-color:#DA4F49;border-radius:4px;color:#FFF;padding:12px 24px;text-align:center;text-decoration:none;" target="_blank">Archive</a></div><div style="float:left;margin-right:15px;padding:10px 0 0 0;"><a href="' . str_replace('administrator/', '', JURI::base()) . 'my-account/library/approve.html?token=' . $accessToken . '&state=1" style="background-color:#5BB75B;border-radius:4px;color:#FFF;padding:12px 24px;text-align:center;text-decoration:none;" target="_blank">Publish</a></div></td>';
					$message .= '</tr>';
					$message .= '</tbody>';
					$message .= '</table>';
					$message .= '<p style="margin-top:30px;">If you have any questions regarding this resource, please contact the NCJFCJ at <a href="mailto:info@ta2ta.org">info@ta2ta.org</a>.</p>';
	
					$mailer->setBody(LibraryHelper::buildEmail('New Library Resource', $message));

					// send the message, if it errors out, just ignore it as we don't want the user affected
					$mailer->Send();
				}
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
		}catch (Exception $e){
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)){
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}
}