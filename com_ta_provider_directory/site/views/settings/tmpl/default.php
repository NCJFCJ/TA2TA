<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
    jQuery(function($){
    	// draw the table 
    	reloadProjectTable();	
    	
    	$('#formSubmit').click(function(event){
    		event.preventDefault();
    		$('#form-settings')[0].submit();
    	});
    	
    	$('#formReset').click(function(event){
    		event.preventDefault();
    		location.reload();
    	});

        // check if the user is trying to add a new project or edit, and if so, open the dialog
        var editId = window.location.href.split('?edit=')[1];
        if(editId !== undefined){
            editId = parseInt(editId);
            openProjectModal(editId);
        }

        /* ----- Project Form Field Live Validation ----- */

        // title
        $('#jform_project_title').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),5)
            || !ta2ta.validate.maxLength($(this),255)
            || !ta2ta.validate.title($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // summary
        $('#jform_project_summary').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),20)
            || !ta2ta.validate.maxLength($(this),1500)){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        /* ----- Contact Form Field Live Validation ----- */

        // first name
        $('#jform_contact_first_name').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),2)
            || !ta2ta.validate.maxLength($(this),30)
            || !ta2ta.validate.name($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // last name  
        $('#jform_contact_last_name').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),2)
            || !ta2ta.validate.maxLength($(this),30)
            || !ta2ta.validate.name($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });    

        // title
        $('#jform_contact_title').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),2)
            || !ta2ta.validate.maxLength($(this),100)
            || !ta2ta.validate.title($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // email
        $('#jform_contact_email').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),3)
            || !ta2ta.validate.maxLength($(this),150)
            || !ta2ta.validate.email($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // phone
        $('#jform_contact_phone').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),10)
            || !ta2ta.validate.maxLength($(this),25) 
            || !ta2ta.validate.phone($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });
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
        ta2ta.bootstrapHelper.removeAlert(jQuery('#contactFormModal').find('.modal-body'));
    	
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
        ta2ta.bootstrapHelper.removeAlert(jQuery('#projectFormModal').find('.modal-body'));
        ta2ta.bootstrapHelper.removeAlert(jQuery('#contactGridAlertWrapper'));
    	
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
            ta2ta.bootstrapHelper.showAlert(jQuery('#contactGridAlertWrapper'), 'Please select a contact to edit by clicking the checkbox to the left of the contact name, or clicking directly on its name.');
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
            ta2ta.bootstrapHelper.showAlert(jQuery('#gridAlertWrapper'), 'Please select a project to edit by clicking the checkbox to the left of the project name, or clicking directly on its name.', false, true);
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
	    			if(project.contacts == ''){
	    				jQuery('#contactsList thead').hide();
	    			}else{
	    				jQuery('#contactsList thead').show();
	    				reloadContactTable(project.contacts);
	    			}
	    			
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
    	var rows = '<tr><td colspan="4" class="center no-records">There are no contacts associated with this project. <a onclick="openContactModal(0);">Try adding one.</a></td></tr>';
    	// check if we have projects to display
    	jQuery.each(contacts, function(index,contact){
			if(!firstDrawn){
    			rows = '';
    			firstDrawn = true;
    		}
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + contact.id + '" type="checkbox" value="1" name="cid[]"></input></td><td>' + (contact.state == 1 ? '' : '<span class="icomoon-remove"></span> ') + '<a onclick="openContactModal(\'' + contact.id + '\');" href="#">' + contact.first_name + ' ' + contact.last_name + '</a></td><td>' + contact.id + '</td></tr>';
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
		
		if(contacts == ''){
			jQuery('#contactsList thead').hide();
		}else{
			jQuery('#contactsList thead').show();
		}
    	
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
    	var rows = '<tr><td colspan="5" class="center no-records">There are no projects associated with this TA Provider. <a onclick="openProjectModal(0);">Try adding one.</a></td></tr>';
    	
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
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + project.id + '" type="checkbox" value="1" name="cid[]"></input></td><td>' + (project.state == 1 ? '' : '<span class="icomoon-remove"></span> ') + '<a onclick="openProjectModal(\'' + project.id + '\');" href="#">' + project.title + '</a></td><td>' + project.created_by + '</td><td>' + project.id + '</td></tr>';
    		classInt = (classInt ? 0 : 1);
		});
		
		// show/hide applicable toolbar buttons
		if(firstDrawn){
			jQuery('#projectsToolbarEdit').css('visibility', 'visible');
		}else{
			jQuery('#projectsToolbarEdit').css('visibility', 'hidden');
		}
		
		if(projects == ''){
			jQuery('#projectsList thead').hide();
		}else{
			jQuery('#projectsList thead').show();
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
        // variables
        var alertParent = jQuery('#contactFormModal').find('.modal-body');
        var data = new Object;
        var errors = [];
        
    	// close any residual alerts and clear validation states
    	ta2ta.bootstrapHelper.removeAlert(alertParent);
        ta2ta.bootstrapHelper.hideAllValidationStates();
        
 		/* validate the form */
    	
    	// id
        data.id = jQuery('#jform_contactID').val();
        if(!ta2ta.validate.unsigned(jQuery('#jform_contactID'))){
            errors.push('<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_ERROR_OCCURED'); ?>');
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
    	if(ta2ta.validate.hasValue(jQuery('#jform_contact_first_name'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_first_name'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_first_name'),30,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
            }

            // is name
            if(!ta2ta.validate.name(jQuery('#jform_contact_first_name'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
        }

    	// last name
    	data.last_name = jQuery('#jform_contact_last_name').val();
        if(ta2ta.validate.hasValue(jQuery('#jform_contact_last_name'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_last_name'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_last_name'),30,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
            }

            // is name
            if(!ta2ta.validate.name(jQuery('#jform_contact_last_name'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
        }

    	// title
    	data.title = jQuery('#jform_contact_title').val();
    	if(data.title){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_title'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_title'),255,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
            }

            // is title
            if(!ta2ta.validate.title(jQuery('#jform_contact_title'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
            }
        }

    	// email
    	data.email = jQuery('#jform_contact_email').val();
    	if(data.email){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_email'),3,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_email'),150,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
            }

            // is email
            if(!ta2ta.validate.email(jQuery('#jform_contact_email'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
            } 		
    	}
    	
    	// phone
    	data.phone = jQuery('#jform_contact_phone').val();
        if(data.phone){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_phone'),10,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_phone'),15,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
            }

            // is phone
            if(!ta2ta.validate.phone(jQuery('#jform_contact_phone'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
            }

            // strip formatting
            data.phone = unformatPhoneNumber(data.phone);       
        }

        // check if any errors occured
        if(errors.length){
            ta2ta.bootstrapHelper.showAlert(alertParent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
        }else{
            // no errors, continue
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
    }
    
    /**
     * Function to save the project information in the modal form
     * 
     * @return null (shows messages to user)
     */
    function saveProject(){
        // variables
        var alertParent = $('#projectFormModal').find('.modal-body');
        var data = new Object;
        var errors = [];
        
        // close any residual alerts and clear validation states
        ta2ta.bootstrapHelper.removeAlert(alertParent);
        ta2ta.bootstrapHelper.hideAllValidationStates();
    	
    	// id
        data.id = jQuery('#jform_projectID').val();
        if(!ta2ta.validate.unsigned(jQuery('#jform_projectID'))){
            errors.push('<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_ERROR_OCCURED'); ?>');
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
        if(ta2ta.validate.hasValue(jQuery('#jform_project_title'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_project_title'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_project_title'),255,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
            }

            // is title
            if(!ta2ta.validate.title(jQuery('#jform_project_title'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE')); ?>');
        }

        // summary
        data.summary = jQuery('#jform_project_summary').val();
        if(ta2ta.validate.hasValue(jQuery('#jform_project_summary'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_project_summary'),20,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_project_summary'),1500,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY')); ?>');
        }
    	
    	// grant programs
    	data.grantPrograms = [];
        jQuery('#projectGrants input:checked').each(function(){
    		data.grantPrograms.push(jQuery(this).val());
    	});
    	
    	if(data.grantPrograms.length == 0){
            errors.push('<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_GRANT_PROGRAM_REQUIRED'); ?>');
    	}
    	
    	// contacts
    	data.contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
    	
 		// check if any errors occured
        if(errors.length){
            ta2ta.bootstrapHelper.showAlert(alertParent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
        }else{
            // no errors, continue
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
            var alertParent = $('#contactGridAlertWrapper');
            ta2ta.bootstrapHelper.removeAlert(alertParent);
            ta2ta.bootstrapHelper.showAlert(alertParent, 'Please select at least one contact to trash by clicking the checkbox to the left of the contact name.');
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
            var alertParent = $('#contactGridAlertWrapper');
            ta2ta.bootstrapHelper.removeAlert(alertParent);
            ta2ta.bootstrapHelper.showAlert(alertParent, 'Please select at least one project to trash by clicking the checkbox to the left of the project name.', false, true);
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
</script>
<div class="ta-directory-settings-edit">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<p>This page allows you to manage how your organization displays in the TA Provider Directory. Each website user associated with your organization has the ability to modify this information. The information you enter here will be instantly displayed on the website, exactly as you enter it.</p>
	<h3><?php echo $this->listing->name; ?></h3>
    <form autocomplete="off" id="form-settings" action="<?php echo JRoute::_('index.php?option=com_ta_provider_directory&task=settings.save'); ?>" method="post" class="form-validate big-inputs" enctype="multipart/form-data">
		<div class="row">
       		<div class="col-xs-6">
                <div class="form-group">
                    <div class="input-group">
                        <span 
                            class="input-group-addon has-tooltip icomoon-earth"
                            data-original-title="<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_SETTINGS_WEBSITE_LABEL'); ?>"
                            data-placement="top"
                            data-toggle="tooltip">
                        </span>
                        <input
                            type="url"
                            id="jform_website"
                            name="jform[website]"
                            class="form-control"
                            placeholder="<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_SETTINGS_WEBSITE_LABEL'); ?>"
                            value="<?php echo $this->listing->website; ?>"
                            />
                        </div>
                    </div>
				</div>	
			</div>
		</div>
		<input type="hidden" id="jform_projects" name="jform[projects]" value='<?php echo json_encode($this->listing->projects, JSON_HEX_APOS | JSON_HEX_QUOT); ?>' />
		<input type="hidden" name="option" value="com_ta_provider_directory" />
		<input type="hidden" name="task" value="settings.save" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
	<h3>Provider Projects</h3>				
	<form action="/" method="post" enctype="multipart/form-data" name="projectTableForm" id="projectTableForm" class="form-validate">
		<div id="gridAlertWrapper"></div>
        <div class="row">
			<div class="col-xs-12">
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-success" onclick="openProjectModal(0);">
							<span class="icomoon-plus-circle"></span> <?php echo JText::_('TOOLBAR_NEW'); ?>
						</button>
					</div>
					<div id="projectsToolbarEdit" class="btn-group">
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-sm" onclick="editProject();">
								<span class="icomoon-edit"></span> <?php echo JText::_('TOOLBAR_EDIT'); ?>
							</button>
						</div>
						<div class="btn-group" style="display: inline-block; margin-left: 5px;">
							<button type="button" class="btn btn-default btn-sm" onclick="trashProjects();">
								<span class="icomoon-remove"></span> <?php echo JText::_('TOOLBAR_TRASH'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table id="projectsList" class="table table-striped">
					<thead>
						<tr>
							<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
							<th class="left">Project Name</th>
							<th class="left" style="width: 20%;">Created by</th>
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
	</form>
	<form>
		<div class="row">
			<div class="col-xs-12 form-actions">
				<button type="submit" class="btn btn-primary btn-lg validate" id="formSubmit"><span class="icomoon-disk"></span> Save Changes</button>
				<button type="reset" class="btn btn-default btn-lg" id="formReset"><span class="icomoon-undo"></span> Start Over</button>
			</div>
		</div>	
	</form>
</div>
<div class="modal fade" id="projectFormModal" role="modal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        	<div class="modal-header">
        		<button onclick="closeProjectModal();" type="button" class="close" aria-hidden="true">&times;</button>
        		<h4 class="modal-title">Add Project</h4>
        	</div>
        	<div class="modal-body" style="height: 350px;">
        		<form action="/" method="post" name="projectForm" id="projectForm" class="form-horizontal form-validate" role="form">  
        			<div class="panel-group" id="projectAccordion">
        					<div class="panel panel-default">
        						<div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#projectAccordion" href="#projectInfo">
                                            Project Details
                                        </a>
                                    </h4>
        						</div>
        						<div id="projectInfo" class="panel-collapse collapse in">
        							<div class="panel-body">
        								<fieldset class="project">	
        									<div class="form-group">
        										<label id="jform_project_title-lbl" class="col-sm-3 control-label required" title="" for="jform_project_title"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE'); ?></label>
        										<div class="col-sm-9">
                                                    <?php echo $this->form->getInput('project_title'); ?>
                                                </div>
        									</div>
        									<div class="form-group">
        										<label id="jform_project_summary-lbl" class="col-sm-3 control-label required" title="" for="jform_project_summary"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY'); ?></label>
        										<div class="col-sm-9">
                                                    <?php echo $this->form->getInput('project_summary'); ?>
                                                </div>
        									</div>
        									<input type="hidden" id="jform_projectID" name="jform[projectID]" value="" />
        								</fieldset>
        							</div>
        						</div>
        					</div>
        					<div class="panel panel-default">
        						<div class="panel-heading">
                                    <h4 class="panel-title">
            							 <a data-toggle="collapse" data-parent="#projectAccordion" href="#projectGrants">
                                            Grant Programs
                                        </a>
                                    </h4>
        						</div>
        						<div id="projectGrants" class="panel-collapse collapse">
        							<div class="panel-body">
        								<fieldset class="programs">
                                            <p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
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
        				<div class="panel panel-default">
        					<div class="panel-heading">
                                <h4 class="panel-title">
        				            <a data-toggle="collapse" data-parent="#projectAccordion" href="#projectContacts">
                                        Contacts
                                    </a>
                                </h4>
        					</div>
        					<div id="projectContacts" class="panel-collapse collapse">
        						<div class="panel-body">
                                    <div id="contactGridAlertWrapper"></div>
        							<div class="row">
        								<div class="col-xs-12">
        									<div id="contacts-toolbar" class="btn-toolbar">
        										<div id="contacts-toolbar-new" class="btn-group">
        											<a class="btn btn-sm btn-success" onclick="openContactModal(0);">
        												<span class="icomoon-plus-circle"></span> <?php echo JText::_('TOOLBAR_NEW_CONTACT'); ?>
        											</a>
        										</div>
        										<div id="contactsToolbarEdit" style="display: inline; margin-left: 5px;">
        											<div id="contacts-toolbar-edit" class="btn-group">
        												<a class="btn btn-default btn-sm" onclick="editContact();">
        													<i class="icomoon-edit "></i> <?php echo JText::_('TOOLBAR_EDIT'); ?>
        												</a>
        											</div>
        											<div class="btn-group divider"></div>
        											<div id="contacts-toolbar-trash" class="btn-group">
        												<a class="btn btn-default btn-sm" onclick="trashContacts();">
        													<i class="icomoon-trash "></i> <?php echo JText::_('TOOLBAR_TRASH'); ?>
        												</a>
        											</div>
        										</div>
        									</div>
        								</div>
        							</div>
        							<div class="row">
        								<div class="col-xs-12">
        									<table id="contactsList" class="table table-striped">
        										<thead>
        											<tr>
        												<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
        												<th class="left">Name</th>
        												<th class="nowrap center hidden-phone" style="width: 1%;">ID</th>
        											</tr>
        										</thead>
        										<tfoot>
        											<tr>
        												<td colspan="3"></td>
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
        		<button type="button" onclick="closeProjectModal();" class="btn btn-default"><span class="icomoon-close"></span> Close</button>
        		<button type="button" onclick="saveProject();" class="btn btn-primary" id="projectSaveBtn"><span class="icomoon-checkmark"></span> Save</button>
        	</div>
        </div>
    </div>
</div>
<div class="modal fade" id="contactFormModal" data-backdrop="static">
	<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
		      <button onclick="closeContactModal();" type="button" class="close" aria-hidden="true">&times;</button>
		      <h4 class="modal-title">Add Contact</h4>
            </div>
        	<div class="modal-body" style="height: 350px;">
        		<div class="alert-wrapper"></div>
        		<form action="/" method="post" name="contactForm" id="contactForm" class="form-horizontal form-validate" role="form">
                    <fieldset class="project">	
        				<div class="form-group">
        					<label for="jform_contact_first_name" class="col-sm-3 control-label required"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_first_name'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_last_name" class="col-sm-3 control-label required"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_last_name'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_title" class="col-sm-3 control-label"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_title'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_email" class="col-sm-3 control-label"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_email'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_phone" class="col-sm-3 control-label"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_phone'); ?>
                            </div>
        				</div>
        				<input type="hidden" id="jform_contactID" name="jform[contactID]" value="" />
        			</fieldset>
        		</form>
        	</div>
        	<div class="modal-footer">
        		<button type="button" onclick="closeContactModal();" class="btn btn-default"><span class="icomoon-close"></span> Close</button>
        		<button type="button" onclick="saveContact();" class="btn btn-primary" id="contactSaveBtn"><span class="icomoon-checkmark"></span> Save</button>
        	</div>
        </div>
    </div>
</div>