<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ta_provider_directory/assets/css/ta_provider_directory.css');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task){
        if(task == 'provider.cancel'){
            Joomla.submitform(task, document.getElementById('provider-form'));
        }else{ 
            if(task != 'provider.cancel' && document.formvalidator.isValid(document.id('provider-form'))){
                Joomla.submitform(task, document.getElementById('provider-form'));
            }else{
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
    jQuery(document).ready(function($){
    	// draw the table 
    	reloadProjectTable();	
    });
    
    // project form functions
    var newID = 1;
    
    /**
	 * Function to close the contact form modal and clear the contents of the form
	 *
	 * @return	null
	 */
    function closeContactModal(){
    	// clear old data
    	jQuery('#contactForm input').val('');
    	jQuery('#jform_contact_state').val('1');
    	jQuery('#jform_contact_state').trigger('liszt:updated');
    	
    	// clear any old alerts
    	hideModalAlert('contactFormModal');
    	
    	// close the modal
    	jQuery('#contactFormModal').modal('hide');
    }
    
    /**
	 * Function to close the project form modal and clear the contents of the form
	 *
	 * @return	null
	 */
    function closeProjectModal(){
    	// clear old data
    	jQuery('#jform_project_state').val('1');
    	jQuery('#jform_project_state').trigger('liszt:updated');
    	jQuery('#jform_project_title, #jform_project_summary, #jform_project_contacts').val('');
    	jQuery('#projectGrants input:checked').removeAttr('checked');
    	
    	// clear any old alerts
    	hideModalAlert('projectFormModal');
    	
    	// close the modal
    	jQuery('#projectFormModal').modal('hide');
    }
    
    /**
     * Function to edit a single project
     * 
     * @return null
     */
    function editContact(){
    	var id = getSelectedIds('contactsList', true);
    	if(id != ''){
    		openContactModal(id);
    	}else{
    		// the user did not select anything
    		alert('Please select a contact.');
    	}
    }
    
    /**
     * Function to edit a single project
     * 
     * @return null
     */
    function editProject(){
    	var id = getSelectedIds('projectsList', true);
    	if(id != ''){
    		openProjectModal(id);
    	}else{
    		// the user did not select anything
    		alert('Please select a project.');
    	}
    }
    
    /**
     * Function to format a phone number
     * 
     * @return string The phone number, digits only
     */
    function formatPhoneNumber(phone){
    	if(phone){
    		var formattedNumber = '';
    		if(phone.charAt(0) == '1'){
    			// this is a 1-800 or similar type number, format accordingly
    			formattedNumber = phone.substr(0,1) + '-' + phone.substr(1,3) + '-' + phone.substr(4,3) + '-' + phone.substr(7,4);
    			// if there is an extension, add it
    			if(phone.length > 11){
    				formattedNumber += ' ext. ' + phone.substring(11);
    			}
    		}else{
    			// this is a normal number
    			formattedNumber = '(' + phone.substr(0,3) + ') ' + phone.substr(3,3) + '-' + phone.substr(6,4);
    			// if there is an extension, add it
    			if(phone.length > 10){
    				formattedNumber += ' ext. ' + phone.substring(10);
    			}
    		}
    		return formattedNumber;
    	}
    	return '';
    }
        
    /**
	 * Function to obtain the ids of all records selected in the table, optionally limited to only a portion of those items
	 *
	 * @param 	string The name of the table
	 * @param	boolean Whether to return only the first value, or all
	 *
	 * @return	mixed string if single, array of strings otherwise, false on failure
	 */
    function getSelectedIds(table, single){
    	var ids = new Array();
    	jQuery('#' + table + ' input:checked').each(function(){
    		var id = jQuery(this).attr('id');
    		if(id){
	    		ids.push(id.replace('cb',''));
	    		if(single){
	    			return false;
	    		}
    		}
    	});
    	    	
    	// check that the array contains data
    	if(ids.length > 0){
    		// if single, return only the first one
    		if(single){
    			return ids[0];
    		}else{
    			return ids;
    		}
    	}else{
    		return false;
    	}
    }
    
    /**
     * Function to hide all alert in a given parent
     * 
     * @param string The HTML ID of the parent within which the alert box will be displayed
     * 
     * @return null (removes messages to user)
     */

    function hideModalAlert(parentID){
    	var parent = jQuery('#' + parentID);
    	if(parent){
    		parent.find('.alert-wrapper').html('');
    	}
    }
    
    /**
	 * Function to open the contact form modal, changing the heading and loading information if necessary
	 *
	 * @param	string The ID of a single record
	 *
	 * @return	null
	 */
    function openContactModal(id){
    	// change the heading based on whether adding or editing a record
    	if(id != '0'){
    		jQuery('#contactFormModal .modal-header h3').text('Edit Contact');
    		jQuery('#contactSaveBtn').text('Save Changes');
    		
    		// load the data for this contact
    		contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
    		jQuery.each(contacts, function(index,contact){
    			if(contact.id == id){
    				jQuery('#jform_contact_state').val(contact.state);
    				jQuery('#jform_contact_state').trigger('liszt:updated');
    				jQuery('#jform_contact_first_name').val(contact.first_name);
					jQuery('#jform_contact_last_name').val(contact.last_name);
					jQuery('#jform_contact_title').val(contact.title);
    				jQuery('#jform_contact_phone').val(formatPhoneNumber(contact.phone));
					jQuery('#jform_contact_email').val(contact.email);
	    			
	    			// stop looping
    				return false;
    			}
    		});    	
    	}else{
    		jQuery('#contactFormModal .modal-header h3').text('Add Contact');
    		jQuery('#contactSaveBtn').text('Save');
    	}
    	
    	// set the height of this box to match that of the parent box
    	jQuery('#contactFormModal .modal-body').height(jQuery('#projectFormModal .modal-body').height());
    	
    	// set the ID
    	jQuery('#jform_contactID').val(id);
    	
    	// open the modal
    	jQuery('#contactFormModal').modal('show');
    }
    
    /**
	 * Function to open the project form modal, changing the heading and loading information if necessary
	 *
	 * @param	string The ID of a single record
	 *
	 * @return	null
	 */
    function openProjectModal(id){
    	// change the heading based on whether adding or editing a record
    	if(id != '0'){
    		jQuery('#projectFormModal .modal-header h3').text('Edit Project');
    		jQuery('#projectSaveBtn').text('Save Changes');
    		
    		// load the data for this project
    		projects = jQuery.parseJSON(jQuery('#jform_projects').val());
    		jQuery.each(projects, function(index,project){
    			if(project.id == id){
    				jQuery('#jform_project_state').val(project.state);
    				jQuery('#jform_project_state').trigger('liszt:updated');
    				jQuery('#jform_project_title').val(project.title);
    				jQuery('#jform_project_summary').val(project.summary);
    				jQuery('#jform_project_contacts').val(JSON.stringify(project.contacts));
    				if(project.grantPrograms instanceof Array){
	    				jQuery('#projectGrants input').each(function(){
	    					// check the appropriate grant programs
							if(jQuery.inArray(jQuery(this).val(), project.grantPrograms) >= 0){
	    						jQuery(this).attr('checked', true);
	    					}
	    				});
	    			}
	    			// update the contact table
	    			reloadContactTable(project.contacts);
	    			
	    			// stop looping
    				return false;
    			}
    		});    		
    	}else{
    		jQuery('#projectFormModal .modal-header h3').text('Add Project');
    		jQuery('#projectSaveBtn').text('Save');
    		var contacts = new Array();
    		jQuery('#jform_project_contacts').val(JSON.stringify(contacts));    				
    		reloadContactTable(contacts);
    	}
    	
    	// set the ID
    	jQuery('#jform_projectID').val(id);
    	
    	// open the modal
    	jQuery('#projectFormModal').modal('show');
    }
    
    /**
     * Function to publish one or more contacts
     * 
     * @return null
     */
    function publishContacts(){
    	var ids = getSelectedIds('contactsList', false);
    	if(ids.length){
    		setContactState('1', ids);
    	}else{
    		// the user did not select anything
    		alert('Please select at least one contact.');
    	}
    }
    
    /**
     * Function to publish one or more projects
     * 
     * @return null
     */
    function publishProjects(){
    	var ids = getSelectedIds('projectsList', false);
    	if(ids.length){
    		setProjectState('1', ids);
    	}else{
    		// the user did not select anything
    		alert('Please select at least one project.');
    	}
    }
    
    /**
     * Function to reload the contacts table
     * 
     * @param object List of contacts
     * 
     * @return null
     */
    
    function reloadContactTable(contacts){
    	// get the table
    	var classInt = 0;
    	var firstDrawn = false;
    	var table = jQuery('#contactsList');
    	var rows = '<tr><td colspan="4" class="center no-records">There are no contacts associated with this project. <a href="#" onclick="openContactModal(0);">Try adding one.</a></td></tr>';
    	// check if we have projects to display
    	jQuery.each(contacts, function(index,contact){
			if(!firstDrawn){
    			rows = '';
    			firstDrawn = true;
    		}
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + contact.id + '" type="checkbox" value="1" name="cid[]"></input></td><td class="center">'; 
    		// state
    		if(!(contact.state instanceof String)){
    			contact.state = (contact.state).toString();
    		}
    		
    		switch(contact.state){
    			case '-1':
    				// deleted
    				rows += '<a class="btn btn-micro active" title="Publish Item" onclick="setContactState(1,\'' + contact.id + '\');" href="javascript:void(0);"><i class="icon-trash"></i></a>';
    				break;
    			case '0':
    				// unpublished
    				rows += '<a class="btn btn-micro active" title="Publish Item" onclick="setContactState(1,\'' + contact.id + '\');" href="javascript:void(0);"><i class="icon-unpublish"></i></a>';
    				break;
    			default:
    				// published
    				rows += '<a class="btn btn-micro active" title="Unpublish Item" onclick="setContactState(0,\'' + contact.id + '\');" href="javascript:void(0);"><i class="icon-publish"></i></a>';
    		}
    		rows += '</td><td><a onclick="openContactModal(\'' + contact.id + '\');" href="#">' + contact.first_name + ' ' + contact.last_name + '</a></td><td>' + contact.id + '</td></tr>';
    		classInt = (classInt ? 0 : 1);
		});
		
		// show/hide applicable toolbar buttons
		if(firstDrawn){
			jQuery('#contactsToolbarEdit').css('visibility', 'visible');
		}else{
			jQuery('#contactsToolbarEdit').css('visibility', 'hidden');
		}
		
		// clear the check all checkbox
		jQuery('#contactsList input[name="checkall-toggle"]').attr('checked', false);
    	
    	// update the table with this content
    	table.find('tbody').html(rows);
    }
    
    /**
     * Function to reload the projects table
     * 
     * @param object List of projects
     * 
     * @return null
     */
    
    function reloadProjectTable(projects){
    	// get the table
    	var classInt = 0;
    	var firstDrawn = false;
    	var table = jQuery('#projectsList');
    	var rows = '<tr><td colspan="5" class="center no-records">There are no projects associated with this TA Provider. <a href="#" onclick="openProjectModal(0);">Try adding one.</a></td></tr>';
    	
    	// check if projects has a value, and if not grab it from the form
    	if(!projects){
    		projects = jQuery.parseJSON(jQuery('#jform_projects').val());
    	}
    	
    	// check if we have projects to display
    	jQuery.each(projects, function(index,project){
			if(!firstDrawn){
    			rows = '';
    			firstDrawn = true;
    		}
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + project.id + '" type="checkbox" value="1" name="cid[]"></input></td><td class="center">'; 
    		// state
    		if(!(project.state instanceof String)){
    			project.state = (project.state).toString();
    		}
    		switch(project.state){
    			case '-1':
    				// deleted
    				rows += '<a class="btn btn-micro active" title="Publish Item" onclick="setProjectState(1,\'' + project.id + '\');" href="javascript:void(0);"><i class="icon-trash"></i></a>';
    				break;
    			case '0':
    				// unpublished
    				rows += '<a class="btn btn-micro active" title="Publish Item" onclick="setProjectState(1,\'' + project.id + '\');" href="javascript:void(0);"><i class="icon-unpublish"></i></a>';
    				break;
    			default:
    				// published
    				rows += '<a class="btn btn-micro active" title="Unpublish Item" onclick="setProjectState(0,\'' + project.id + '\');" href="javascript:void(0);"><i class="icon-publish"></i></a>';
    		}
    		rows += '</td><td><a onclick="openProjectModal(\'' + project.id + '\');" href="#">' + project.title + '</a></td><td>' + project.created_by + '</td><td>' + project.id + '</td></tr>';
    		classInt = (classInt ? 0 : 1);
		});
		
		// show/hide applicable toolbar buttons
		if(firstDrawn){
			jQuery('#projectsToolbarEdit').css('visibility', 'visible');
		}else{
			jQuery('#projectsToolbarEdit').css('visibility', 'hidden');
		}
		
		// clear the check all checkbox
		jQuery('#projectsList input[name="checkall-toggle"]').attr('checked', false);
    	
    	// update the table with this content
    	table.find('tbody').html(rows);
    }
    
    /**
     * Function to save the contact information in the modal form
     * 
     * @return null (shows messages to user)
     */
    function saveContact(){
    	// close any residual alerts in the project modal
    	jQuery('.alert').alert('close');
    	jQuery('#contactFormModal .control-group').removeClass('error');
    	
 		/* validate the form */
    	var data = new Object;
    	var regExp;
    	
    	// id
    	data.id = jQuery('#jform_contactID').val();
    	regExp = /^n?\d+$/;
    	if(!regExp.test(data.id)){
    		// ID is missing, user is up to no good
    		showModalAlert('contactFormModal', 'error', '<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_ERROR_OCCURED'); ?>');
    		return;	
    	}
    	
    	// state
    	var state = jQuery('#jform_contact_state').val();
    	if(state == '-1' 
    	|| state == '0' 
    	|| state == '1'){
    		data.state = state;
    	}else{
    		data.state = 1;
    	}

    	// first name
    	data.first_name = jQuery('#jform_contact_first_name').val();
    	if(data.first_name){
    		// min length
    		if(data.first_name.length < 2){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
    			return;
    		}
    		// max length
    		if(data.first_name.length > 30){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
    			return;
    		}
    		// regex
    		regExp = /^[a-zA-Z-\. ]*$/;
    		if(!regExp.test(data.first_name)){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
    			return;
    		}
    	}else{
    		// field is is required, but is empty
    		showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
    		return;
    	}
    	
    	// last name
    	data.last_name = jQuery('#jform_contact_last_name').val();
    	if(data.last_name){
    		// min length
    		if(data.last_name.length < 2){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
    			return;
    		}
    		// max length
    		if(data.last_name.length > 30){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
    			return;
    		}
    		// regex
    		regExp = /^[a-zA-Z-\' ]*$/;
    		if(!regExp.test(data.last_name)){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
    			return;
    		}
    	}else{
    		// field is is required, but is empty
    		showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
    		return;
    	}

    	// title
    	data.title = jQuery('#jform_contact_title').val();
    	if(data.title){
    		// min length
    		if(data.title.length < 2){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
    			return;
    		}
    		// max length
    		if(data.title.length > 255){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
    			return;
    		}
    		// regex
    		regExp = /^[a-zA-Z()0-9 ]*$/;
    		if(!regExp.test(data.title)){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
    			return;
    		}
    	}

    	// email
    	data.email = jQuery('#jform_contact_email').val();
    	if(data.email){
    		// min length
    		if(data.email.length < 3){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
    			return;
    		}
    		// max length
    		if(data.email.length > 150){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
    			return;
    		}
    		// regex
    		regExp = /(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*:(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)(?:,\s*(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*))*)?;\s*)/;
    		if(!regExp.test(data.email)){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
    			return;
    		}
    	}
    	
    	// phone
    	data.phone = jQuery('#jform_contact_phone').val();
    	if(data.phone){
    		// regex
    		regExp = /[\d() -ext\.]/;
    		if(!regExp.test(data.phone)){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
    			return;
    		}
    		// strip formatting
    		data.phone = unformatPhoneNumber(data.phone);
    		// min length
    		if(data.phone.length < 10){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
    			return;
    		}
    		// max length
    		if(data.phone.length > 15){
    			showModalAlert('contactFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
    			return;
    		}
    	}

 		/* well, we made it, everything is probably valid */
 		
 		// get the current form contents and decode the JSON
 		var contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
 		
 		// update the current record or add a new one to the hidden field
 		if(data.id != '0'){
 			// editing
 			jQuery.each(contacts, function(index,contact){
 				// if the given element matches the current id, update that record				
 				if(contact.id == data.id){
 					contacts[index].state = data.state;
 					contacts[index].first_name = data.first_name;
 					contacts[index].last_name = data.last_name;
 					contacts[index].title = data.title;
 					contacts[index].email = data.email;
 					contacts[index].phone = data.phone;
 				}
 			});
 		}else{
 			// adding
 			data.created_by = '<?php echo $this->userName; ?>';
 			data.id = 'n' + newID;
 			contacts.push(data);
 			 			
 			// incremeent the newID
 			newID++;
 		}
 		
 		//save the updated data
 		jQuery('#jform_project_contacts').val(JSON.stringify(contacts));
    	
    	// cause the table to refresh
    	reloadContactTable(contacts);
    	
    	// close the project window
    	closeContactModal();
    }
    
    /**
     * Function to save the project information in the modal form
     * 
     * @return null (shows messages to user)
     */
    function saveProject(){
    	// close any residual alerts in the project modal
    	jQuery('.alert').alert('close');
    	jQuery('#projectFormModal .control-group').removeClass('error');
    	
 		/* validate the form */
    	var data = new Object;
    	var regExp;
    	
    	// id
    	data.id = jQuery('#jform_projectID').val();
    	regExp = /^n?\d+$/;
    	if(!regExp.test(data.id)){
    		// ID is missing, user is up to no good
    		showModalAlert('projectFormModal', 'error', '<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_ERROR_OCCURED'); ?>');
    		return;	
    	}
    	
    	// state
    	var state = jQuery('#jform_project_state').val();
    	if(state == '-1' 
    	|| state == '0' 
    	|| state == '1'){
    		data.state = state;
    	}else{
    		data.state = 1;
    	}

    	// title
    	data.title = jQuery('#jform_project_title').val();
    	if(data.title){
    		// min length
    		if(data.title.length < 2){
    			showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
    			return;
    		}
    		// max length
    		if(data.title.length > 255){
    			showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
    			return;
    		}
    		// regex
    		regExp = /^[a-zA-Z0-9():,\'\"\-\.\/ ]*$/;
    		if(!regExp.test(data.title)){
    			showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
    			return;
    		}
    	}else{
    		// field is is required, but is empty
    		showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
    		return;
    	}
    	
		// summary
    	data.summary = jQuery('#jform_project_summary').val();
    	if(data.summary){
    		// min length
    		if(data.summary.length < 10){
    			showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY')); ?>');
    			return;
    		}
    		// max length
    		if(data.summary.length > 1500){
    			showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MAX_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY')); ?>');
    			return;
    		}
    	}else{
    		// field is is required, but is empty
    		showModalAlert('projectFormModal', 'error', '<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY')); ?>');
    		return;
    	}
    	
    	// grant programs
    	data.grantPrograms = new Array();
    	jQuery('#projectGrants input:checked').each(function(){
    		data.grantPrograms.push(jQuery(this).val());
    	});
    	
    	// contacts
    	data.contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
    	
 		/* well, we made it, everything is probably valid */
 		
 		// get the current form contents and decode the JSON
 		var projects = jQuery.parseJSON(jQuery('#jform_projects').val());
 		
 		// update the current record or add a new one to the hidden field
 		if(data.id != '0'){
 			// editing
 			jQuery.each(projects, function(index,project){
 				// if the given element matches the current id, update that record				
 				if(project.id == data.id){
 					projects[index].state = data.state;
 					projects[index].title = data.title;
 					projects[index].summary = data.summary;
 					projects[index].grantPrograms = data.grantPrograms;
 					projects[index].contacts = data.contacts;
 				}
 			});
 		}else{
 			// adding
 			data.created_by = '<?php echo $this->userName; ?>';
 			data.id = 'n' + newID;
 			projects.push(data);
 			 			
 			// incremeent the newID
 			newID++;
 		}
 		
 		//save the updated data
 		jQuery('#jform_projects').val(JSON.stringify(projects));
    	
    	// cause the table to refresh
    	reloadProjectTable(projects);
    	
    	// close the project window
    	closeProjectModal();
    }
    
    /**
     * Function to change the state of a contact
     * 
     * @param state The state to be set [-1(deleted), 0(unpublished), 1(published)]
     * @param array The IDs of one or more records to be affected
     * 
     * @return null (shows messages to user)
     */
    function setContactState(state, ids){
    	// get the current form contents and decode the JSON
 		var contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
 		
 		// check to see if each project matches those to be changed
 		if(ids instanceof Array){
			// array of different projects
			jQuery.each(contacts, function(index,contact){
				if(jQuery.inArray(contact.id, ids) >= 0){
					contact.state = state;
				}
			});
		}else{
			// single project
			jQuery.each(contacts, function(index,contact){
				if(contact.id == ids){
					contact.state = state;
				}
			});
		}
		
		//save the updated data
 		jQuery('#jform_project_contacts').val(JSON.stringify(contacts));
 		
 		// cause the table to refresh
    	reloadContactTable(contacts);
    }
    
    /**
     * Function to change the state of a project
     * 
     * @param state The state to be set [-1(deleted), 0(unpublished), 1(published)]
     * @param array The IDs of one or more records to be affected
     * 
     * @return null (shows messages to user)
     */
    function setProjectState(state, ids){
    	// get the current form contents and decode the JSON
 		var projects = jQuery.parseJSON(jQuery('#jform_projects').val());
 		
 		// check to see if each project matches those to be changed
 		if(ids instanceof Array){
			// array of different projects
			jQuery.each(projects, function(index,project){
				if(jQuery.inArray(project.id, ids) >= 0){
					project.state = state;
				}
			});
		}else{
			// single project
			jQuery.each(projects, function(index,project){
				if(project.id == ids){
					project.state = state;
				}
			});
		}
		
		//save the updated data
 		jQuery('#jform_projects').val(JSON.stringify(projects));
 		
 		// cause the table to refresh
    	reloadProjectTable(projects);
    }

    /**
     * Function to display an alert
     * 
     * @param string The HTML ID of the parent within which the alert box will be displayed
     * @param string The type of alert, valid types are error, info, success, or warning (default)
     * @param string The message to be displayed
     * 
     * @return null (shows messages to user)
     */

    function showModalAlert(parentID, type, message){
    	// variables
    	var alertContent = '<div class="alert';
    	var strongText = '';
    	var parent = jQuery('#' + parentID);
    	
    	// make sure we have what we need before continuing
    	if(parent && message != ''){
    		// determine which type of alert to show
    		switch(type){
    			case 'error':
	    			alertContent += ' alert-error';
	    			strongText = 'Error!';
	    			break;
	    		case 'info':
	    			alertContent += ' alert-info';
	    			strongText = 'Information';
	    			break;
	    		case 'success':
	    			alertContent += ' alert-success';
	    			strongText = 'Success!';
	    			break;
	    		default:
	    			strongText = 'Warning!';
    		}
    		
    		// construct the rest of the alert content
    		alertContent += '"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + strongText + '</strong> ' + message + '</div>';
    	
    		// draw the alert
    		parent.find('.alert-wrapper').html(alertContent);
    	}
    }
    
    /**
     * Function to trash one or more contacts
     * 
     * @return null
     */
    function trashContacts(){
    	var ids = getSelectedIds('contactsList', false);
    	if(ids.length){
    		setContactState('-1', ids);
    	}else{
    		// the user did not select anything
    		alert('Please select at least one contact.');
    	}
    }
    
    /**
     * Function to trash one or more projects
     * 
     * @return null
     */
    function trashProjects(){
    	var ids = getSelectedIds('projectsList', false);
    	if(ids.length){
    		setProjectState('-1', ids);
    	}else{
    		// the user did not select anything
    		alert('Please select at least one project.');
    	}
    }
    
    /**
     * Function to unpublish one or more contacts
     * 
     * @return null
     */
    function unpublishContacts(){
    	var ids = getSelectedIds('contactsList', false);
    	if(ids.length){
    		setContactState('0', ids);
    	}else{
    		// the user did not select anything
    		alert('Please select at least one contact.');
    	}
    }
    
    /**
     * Function to remove formatting from a phone number
     * 
     * @return string The phone number, digits only
     */
    function unformatPhoneNumber(phone){
    	// if the phone number starts with a one, but is not followed by an 8 or 9, remove the 1
    	if(phone){
    		// remove everything but digits, and return the result
    		phone = phone.replace(/[^\d]/g, '');
    		
    		// remove a leading 1 if it is not for an 800 or 900 series number
	    	if(phone.charAt(0) == '1'
	    	&& (phone.charAt(1) != '8' 
	    	&& phone.charAt(1) != '9')){
	    		phone = phone.substring(1);
	    	}
	 		
	 		// return the result    	
	    	return phone;
	    }
	    return '';
    }
    
    /**
     * Function to unpublish one or more projects
     * 
     * @return null
     */
    function unpublishProjects(){
    	var ids = getSelectedIds('projectsList', false);
    	if(ids.length){
    		setProjectState('0', ids);
    	}else{
    		// the user did not select anything
    		alert('Please select at least one project.');
    	}
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_ta_provider_directory&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="provider-form" class="form-validate">
	<div class="row-fluid">
	    <div class="span10 form-horizontal">
	        <fieldset class="details">
				<h2><?php echo $this->item->name; ?></h2>
				<input type="hidden" id="jform_projects" name="jform[projects]" value='<?php echo json_encode($this->item->projects, JSON_HEX_APOS | JSON_HEX_QUOT); ?>' />
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</div>
	</div>
</form>			
<h3>Provider Projects</h3>				
<form action="<?php echo JRoute::_('index.php?option=com_ta_provider_directory&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="projectTableForm" id="projectTableForm" class="form-validate">
	<div class="row-fluid">
        <div class="span12 form-horizontal">
			<div class="row-fluid">
				<div class="span12">
					<div id="projects-toolbar" class="btn-toolbar">
						<div id="projects-toolbar-new" class="btn-group">
							<a class="btn btn-small btn-success" onclick="openProjectModal(0);" href="#" style="width: 120px;">
								<i class="icon-new icon-white"></i> <?php echo JText::_('JTOOLBAR_NEW'); ?>
							</a>
						</div>
						<div id="projectsToolbarEdit" style="display: inline; margin-left: 5px;">
							<div id="projects-toolbar-edit" class="btn-group">
								<a class="btn btn-small" onclick="editProject();" href="#">
									<i class="icon-edit "></i> <?php echo JText::_('JTOOLBAR_EDIT'); ?>
								</a>
							</div>
							<div class="btn-group divider"></div>
							<div id="projects-toolbar-publish" class="btn-group">
								<a class="btn btn-small" onclick="publishProjects();" href="#">
									<i class="icon-publish "></i> <?php echo JText::_('JTOOLBAR_PUBLISH'); ?>
								</a>
							</div>
							<div id="projects-toolbar-unpublish" class="btn-group">
								<a class="btn btn-small" onclick="unpublishProjects();" href="#">
									<i class="icon-unpublish "></i> <?php echo JText::_('JTOOLBAR_UNPUBLISH'); ?>
								</a>
							</div>
							<div class="btn-group divider"></div>
							<div id="projects-toolbar-trash" class="btn-group">
								<a class="btn btn-small" onclick="trashProjects();" href="#">
									<i class="icon-trash "></i> <?php echo JText::_('JTOOLBAR_TRASH'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<table id="projectsList" class="table table-striped">
						<thead>
							<tr>
								<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
								<th class="nowrap center" style="width: 1%;">State</th>
								<th class="left">Project Name</th>
								<th class="left" style="width: 20%;">Created by</th>
								<th class="nowrap center hidden-phone" style="width: 1%;">ID</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5"></td>
							</tr>
						</tfoot>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>
<div class="modal hide fade" id="projectFormModal" data-backdrop="static">
	<div class="modal-header">
		<button onclick="closeProjectModal();" type="button" class="close" aria-hidden="true">&times;</button>
		<h3>Add Project</h3>
	</div>
	<div class="modal-body">
		<div class="alert-wrapper"></div>
		<form action="<?php echo JRoute::_('index.php?option=com_ta_provider_directory&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="projectForm" id="projectForm" class="form-horizontal form-validate">  
			
		<div class="accordion" id="projectAccordion">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#projectAccordion" href="#projectInfo">Project Details</a>
					</div>
					<div id="projectInfo" class="accordion-body collapse in">
						<div class="accordion-inner">
							<fieldset class="project">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('project_state'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('project_state'); ?></div>
								</div>			
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('project_title'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('project_title'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('project_summary'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('project_summary'); ?></div>
								</div>
								<input type="hidden" id="jform_projectID" name="jform[projectID]" value="" />
							</fieldset>
						</div>
					</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#projectAccordion" href="#projectGrants">Grant Programs</a>
					</div>
					<div id="projectGrants" class="accordion-body collapse">
						<div class="accordion-inner">
							<fieldset class="programs">
								<?php 
									// print each checkbox in two columns
									$columns = 2;
									$gpColumns = array_chunk($this->grantPrograms, ceil(count($this->grantPrograms) / $columns));	
									foreach($gpColumns as $gpColumn){
										echo '<div style="float: left; width: ' . (100/$columns) . '%">';
										foreach($gpColumn as $grantProgram){
											echo '<div style="margin-bottom: 10px;"><input type="checkbox" name="grantPrograms[]" style="margin-top: -2px" value="' . $grantProgram->id . '"' . ($grantProgram->checked ? ' checked' : '') . '> ' . $grantProgram->name . ' (' . $grantProgram->fund . ')</div>';
										}
										echo '</div>';
									}
								?>
								<div class="clr"></div>
					       </fieldset>
						</div>
					</div>
				</div>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#projectAccordion" href="#projectContacts">Contacts</a>
				</div>
				<div id="projectContacts" class="accordion-body collapse">
					<div class="accordion-inner">
						<div class="row-fluid">
							<div class="span12">
								<div id="contacts-toolbar" class="btn-toolbar">
									<div id="contacts-toolbar-new" class="btn-group">
										<a class="btn btn-small btn-success" onclick="openContactModal(0);" href="#" style="width: 120px;">
											<i class="icon-new icon-white"></i> <?php echo JText::_('JTOOLBAR_NEW'); ?>
										</a>
									</div>
									<div id="contactsToolbarEdit" style="display: inline; margin-left: 5px;">
										<div id="contacts-toolbar-edit" class="btn-group">
											<a class="btn btn-small" onclick="editContact();" href="#">
												<i class="icon-edit "></i> <?php echo JText::_('JTOOLBAR_EDIT'); ?>
											</a>
										</div>
										<div class="btn-group divider"></div>
										<div id="contacts-toolbar-publish" class="btn-group">
											<a class="btn btn-small" onclick="publishContacts();" href="#">
												<i class="icon-publish "></i> <?php echo JText::_('JTOOLBAR_PUBLISH'); ?>
											</a>
										</div>
										<div id="contacts-toolbar-unpublish" class="btn-group">
											<a class="btn btn-small" onclick="unpublishContacts();" href="#">
												<i class="icon-unpublish "></i> <?php echo JText::_('JTOOLBAR_UNPUBLISH'); ?>
											</a>
										</div>
										<div class="btn-group divider"></div>
										<div id="contacts-toolbar-trash" class="btn-group">
											<a class="btn btn-small" onclick="trashContacts();" href="#">
												<i class="icon-trash "></i> <?php echo JText::_('JTOOLBAR_TRASH'); ?>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12">
								<table id="contactsList" class="table table-striped">
									<thead>
										<tr>
											<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
											<th class="nowrap center" style="width: 1%;">State</th>
											<th class="left">Name</th>
											<th class="nowrap center hidden-phone" style="width: 1%;">ID</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<td colspan="4"></td>
										</tr>
									</tfoot>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="jform_project_contacts" name="jform[project_contacts]" value="">
		</form>
	</div>
	<div class="modal-footer">
		<a href="javascript:closeProjectModal();" class="btn">Close</a>
		<a href="javascript:saveProject();" class="btn btn-primary" id="projectSaveBtn">Save</a>
	</div>
</div>
<div class="modal hide fade" id="contactFormModal" data-backdrop="static">
	<div class="modal-header">
		<button onclick="closeContactModal();" type="button" class="close" aria-hidden="true">&times;</button>
		<h3>Add Contact</h3>
	</div>
	<div class="modal-body">
		<div class="alert-wrapper"></div>
		<form action="<?php echo JRoute::_('index.php?option=com_ta_provider_directory&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="contactForm" id="contactForm" class="form-horizontal form-validate">
			<fieldset class="project">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('contact_state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('contact_state'); ?></div>
				</div>			
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('contact_first_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('contact_first_name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('contact_last_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('contact_last_name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('contact_title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('contact_title'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('contact_email'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('contact_email'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('contact_phone'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('contact_phone'); ?></div>
				</div>
				<input type="hidden" id="jform_contactID" name="jform[contactID]" value="" />
			</fieldset>
		</form>
	</div>
	<div class="modal-footer">
		<a href="javascript:closeContactModal();" class="btn">Close</a>
		<a href="javascript:saveContact();" class="btn btn-primary" id="contactSaveBtn">Save</a>
	</div>
</div>