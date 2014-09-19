<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2013 NCJFCJ.
 */

defined('JPATH_BASE') or die;

/**
 * An example custom profile plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       1.6
 */
class plgUserProfile5 extends JPlugin{

	/**
	 * @param   string     $context  The context for the data
	 * @param   integer    $data     The user id
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareData($context, $data){
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile'))){
			return true;
		}

		if (is_object($data)){
			$userId = isset($data->id) ? $data->id : 0;

			if (!isset($data->profile) and $userId > 0){
				// Load the profile data from the database.
				$db = JFactory::getDbo();
				$db->setQuery(
					'SELECT profile_key, profile_value FROM #__user_profiles' .
						' WHERE user_id = ' . (int) $userId . " AND profile_key LIKE 'profile.%'" .
						' ORDER BY ordering'
				);

				try{
					$results = $db->loadRowList();
				}catch (RuntimeException $e){
					$this->_subject->setError($e->getMessage());
					return false;
				}

				// Merge the profile data.
				$data->profile = array();

				foreach ($results as $v){
					$k = str_replace('profile.', '', $v[0]);
					$data->profile[$k] = json_decode($v[1], true);
					if ($data->profile[$k] === null){
						$data->profile[$k] = $v[1];
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param   JForm    $form    The form to be altered.
	 * @param   array    $data    The associated data for the form.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	public function onContentPrepareForm($form, $data){
		// Load user_profile plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_user_profile5', JPATH_ADMINISTRATOR);
	
		if (!($form instanceof JForm)){
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration'))){
			return true;
		}

		// Add the registration fields to the form.
		JForm::addFormPath(__DIR__ . '/profiles');
		$form->loadFile('profile', false);

		$fields = array(
			'newsletter',
			'city',
			'grant',
			'org',
			'phone',
			'region',
		);

		//Change fields description when displayed in front-end
		$app = JFactory::getApplication();
		if ($app->isSite()){
			$form->setFieldAttribute('newsletter', 'description', 'PLG_USER_PROFILE5_FIELD_NEWSLETTER_DESC', 'profile');
			$form->setFieldAttribute('city', 'description', 'PLG_USER_PROFILE5_FIELD_CITY_DESC', 'profile');
			$form->setFieldAttribute('grant', 'description', 'PLG_CUSTOM_USER_PROFILE_FIELD_GRANT_DESC', 'profile');
			$form->setFieldAttribute('org', 'description', 'PLG_CUSTOM_USER_PROFILE_FIELD_ORG_DESC', 'profile');
			$form->setFieldAttribute('phone', 'description', 'PLG_USER_PROFILE5_FIELD_PHONE_DESC', 'profile');
			$form->setFieldAttribute('region', 'description', 'PLG_USER_PROFILE5_FIELD_REGION_DESC', 'profile');
		}

		foreach ($fields as $field){
			// Case using the users manager in admin
			if ($name == 'com_users.user'){
				// Remove the field if it is disabled in registration and profile
				if ($this->params->get('register-require_' . $field, 1) == 0
					&& $this->params->get('profile-require_' . $field, 1) == 0){
					$form->removeField($field, 'profile');
				}
			}
			// Case registration
			elseif ($name == 'com_users.registration'){
				// Toggle whether the field is required.
				if ($this->params->get('register-require_' . $field, 1) > 0){
					$form->setFieldAttribute($field, 'required', ($this->params->get('register-require_' . $field) == 2) ? 'required' : '', 'profile');
				}else{
					$form->removeField($field, 'profile');
				}
			}
			// Case profile in site or admin
			elseif ($name == 'com_users.profile' || $name == 'com_admin.profile'){
				// Toggle whether the field is required.
				if ($this->params->get('profile-require_' . $field, 1) > 0){
					$form->setFieldAttribute($field, 'required', ($this->params->get('profile-require_' . $field) == 2) ? 'required' : '', 'profile');
				}else{
					$form->removeField($field, 'profile');
				}

				if ($this->params->get('profile-require_dob', 1) > 0){
					$form->setFieldAttribute('spacer', 'type', 'spacer', 'profile');
				}
			}
		}

		return true;
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param   array    $user   Holds the old user data.
	 * @param   boolean  $isnew  True if a new user is stored.
	 * @param   array    $data   Holds the new user data.
	 *
	 * @return  boolean
	 *
	 * @since 	3.1
	 * @throws  InvalidArgumentException on invalid date.
	 */
	public function onUserAfterSave($data, $isNew, $result, $error){
		$userId = JArrayHelper::getValue($data, 'id', 0, 'int');

		if ($userId && $result && isset($data['profile']) && (count($data['profile']))){
			// add to MyEmma
			if($data['profile']['newsletter'] == 1 && !empty($data['email'])){
				try{
					/*$emma = new Emma();  //$account_id, $pub_api_key, $pri_api_key, $debug = false
					$member_info = array(
						'email' => $data['email'],
						'fields' => array(
							'city' => $data['profile']['city'],
							'state_province' => $data['profile']['region'],
						),
						'group_ids' => array(2216180),
						'signup_form_id' => 1744108,
						'opt_in_subject' => 'Confirming your subscription to the TA2TA email list',
						'opt_in_message' => "Thank you for joining the TA2TA (techical assistance to technical assistance providers) email list!\r\n\r\nTo confirm your subscription, please click this link or paste it into your browser: [opt_in_url]\r\n\r\nTo ensure proper delivery of our future emails, please take a moment to add our address - [rsvp_email] - to your address book, trusted sender list, or company white list.\r\n\r\nIf you do not wish to receive our emails, or this email has reached you in error, please click this link or paste it into your browser: [opt_out_url]\r\n\r\nTA2TA is a project of the [rsvp_name].",
					);
					$emma->membersSignup($member_info);*/
				}catch(Exception $e){
					// Log this error
					error_log('Unable to add user to MyEmma');	
					echo 'Error connecting to API';
					die();
				}
			}
			try{
				// Sanitize the date
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__user_profiles'))
					->where($db->quoteName('user_id') . ' = ' . (int) $userId)
					->where($db->quoteName('profile_key') . ' LIKE ' . $db->quote('profile.%'));
				$db->setQuery($query);
				$db->execute();

				$tuples = array();
				$order = 1;

				foreach ($data['profile'] as $k => $v)
				{
					$tuples[] = '(' . $userId . ', ' . $db->quote('profile.' . $k) . ', ' . $db->quote(json_encode($v)) . ', ' . $order++ . ')';
				}

				$db->setQuery('INSERT INTO #__user_profiles VALUES ' . implode(', ', $tuples));
				$db->execute();
			}catch (RuntimeException $e){
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove all user profile information for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 *
	 * @return  boolean
	 */
	public function onUserAfterDelete($user, $success, $msg){
		if (!$success){
			return false;
		}

		$userId = JArrayHelper::getValue($user, 'id', 0, 'int');

		if ($userId){
			try{
				$db = JFactory::getDbo();
				$db->setQuery(
					'DELETE FROM #__user_profiles WHERE user_id = ' . $userId .
						" AND profile_key LIKE 'profile.%'"
				);

				$db->execute();
			}catch (Exception $e){
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}
}