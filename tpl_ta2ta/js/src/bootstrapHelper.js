/* -------------------------------------- *
 * This file provides functionality to    *
 * make it easier to work with Bootstrap. *
 *                                        *
 * IMPORTANT: Include only after jQuery   *
 *                                        
 * Dependencies:                          *
 * - jQuery                               *
 * - Bootstrap 3                          *
 * -------------------------------------- */


ta2ta.bootstrapHelper = ta2ta.bootstrapHelper || {

	/* -------------- Settings ---------------*/

	body_scroll_offset: 90, // the number of pixels to offset the scrolling function above the alert
	joomla: true, // whether or not the base website is Joomla!
	parent_scroll_offset: 20, // the number of pixels to offset the scrolling function above the alert
	scroll_speed: 500, // the number of milliseconds it takes to scroll to an alert


	/* --------------- Alerts --------------- */

	/**
	 * Generates an error message from the given array. If there is only a single item in the array, simply returns that
	 *
	 * @param array Messages to be displayed in the error
	 * @return string HTML string containing all error messages
	 */
	constructErrorMessage: function(messages){
		// make sure we have messages to process
		if(!messages.length){
			return 'An unknown error occured.';
		}

		// if there is just one message, return it
		if(messages.length == 1){
			return messages[0];
		}

		// there is more than one message, combine them
		var combinedMessage = 'Your request could not be completed due to the following issues:<ul>';
		jQuery.each(messages, function(index, value){
			combinedMessage += '<li>' + value + '</li>';
		});
		combinedMessage += '</ul>';

		return combinedMessage;
	},

	/**
	 * Draws pagination buttons within the specified container
	 * 
	 * @param object A jQuery object for the parent in which the alert will appear
	 * @param int The current page number
	 * @param limit The max number of records to display
	 * @param numRecords The total number of records
	 */
	drawPagination: function(parent,page,limit,numRecords){
		if(!jQuery(parent).length){
			return;
		}

		// draw pagination
		var paginationOutput = '';
		if(numRecords > limit){
			var numPages = Math.ceil(numRecords / limit);
			
			// first, determine the starting number
			var startPage = page - 2;
			var endPage = page + 2;
			if(startPage < 1){
				startPage = 1;
				endPage = 5;
			}
			if(endPage > numPages){
				startPage = numPages - 5;
				endPage = numPages;
				if(startPage < 1){
					startPage = 1;
				}
			}
			
			for(var i = startPage; i <= endPage; i++){
				if(paginationOutput == '' && startPage > 1){
					// we are not starting with 1, but we need an opening number anyway
					paginationOutput += '<ul class="pagination"><li><a href="javascript:void(0);" data-page-number="1" onclick="changePage(this)">1</a></li>' + (startPage < 3 ? '' : '<li><a href="javascript:void(0);">...</a></li>'); 
				}else if(paginationOutput == ''){
					paginationOutput += '<ul class="pagination">';
				}
				paginationOutput += '<li' + (i == page ? ' class="active"' : '') + '><a href="javascript:void(0);" data-page-number="' + i + '" onclick="changePage(this)">' + i + '</a></li>'; 
				if(i == endPage && endPage < numPages){
					// the last item in the set has been printed, but an ending number is required
					paginationOutput += (i + 1 == numPages ? '' : '<li><a href="javascript:void(0);">...</a></li>') + '<li><a href="javascript:void(0);" data-page-number="' + numPages + '" onclick="changePage(this)">' + numPages + '</a></li></ul>'; 
				}	
			}
		}
		jQuery(parent).html(paginationOutput);
	},

	/**
	 * Displays an alert within the specified parent
	 * @param object A jQuery object for the parent in which the alert will appear
	 * @param string The HTML message to be displayed
	 * @param string The type of alert to render (error, warning, success, or info) (default warning)
	 * @param boolean Whether or not to display this message in block format (default false)
	 * @param boolean Whether or not the alert is dismissable (default false)
	 * @param int How long to display the alert in milliseconds before automatically closing (default disabled)
	 */
	showAlert: function(parent, message, type, block, dismissable, aliveTime){
		// make sure there is a message to display
		if(message === ''){
			return;
		}

		// make sure the type is valid
		if(type !== 'error'
		&& type !== 'warning'
		&& type !== 'success'
		&& type !== 'info'){
			type = 'warning';
		}

		class_name = type;

		// heading
		var heading = '';
		switch(type){
			case 'error':
				class_name = 'danger';
				heading = 'Error';
				break;
			case 'warning':
				heading = 'Warning';
				break;
			case 'success':
				heading = 'Well done';
				break;
			case 'info':
				heading = 'Notice';
				break;
		}

		// establish remaining defaults
		block = typeof block !== 'undefined' ? block : false;
		dismissable = typeof dismissable !== 'undefined' ? dismissable : false;
		aliveTime = typeof aliveTime !== 'undefined' ? aliveTime : 0;

		// creat the alert box
		var alrt = '<div class="alert alert-' + class_name + (dismissable ? ' alert-dismissable' : '') + '">';
		if(dismissable){
			alrt += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		}
		if(block){
			alrt += '<h4>' + heading + '!</h4>';
		}else{
			alrt += '<strong>' + heading + '!</strong> ';
		}
		alrt += message;
		alrt += '</div>';

		// check if the supplied element exists
		if(jQuery(parent).length){
			// print the alert to the screen
			jQuery(parent).prepend(alrt);

			// check if the parent scrolls, if not, scroll the document body
			var element_to_scroll = jQuery(parent);
			var scroll_offset = this.parent_scroll_offset;

			// HACK: Set element_to_scroll to have an overflow-y of auto (fixes collapsing divs)
			element_to_scroll.css({'overflow-y':'auto'});
			
			// determine if the parent element scrolls, if not scroll the body
			if(!(element_to_scroll[0].scrollHeight > element_to_scroll.innerHeight())){
				element_to_scroll = jQuery('html, body');
				scroll_offset = this.body_scroll_offset;
			}

			// scroll to this item relative to its parent
			element_to_scroll.animate({
		        scrollTop: jQuery('.alert').offset().top - element_to_scroll.offset().top - scroll_offset
		    }, this.scroll_speed);
		}else{
			// show a javascript alert insted (yuck!)
			alert(heading + ': ' + message.replace(/(<([^>]+)>)/ig, ' '));
		}

		// set the alert to automatically close
		if(aliveTime){
			window.setTimeout(function(){
				// TO DO: Fix this. By calling removeAlert in this manner, it breaks our ability
				// to use this code in other places without first editing this line. It is my intent
				// that only line 13 should require changes.
				ta2ta.bootstrapHelper.removeAlert(parent);
			}, aliveTime);
		}
	},

	/**
	 * Removes an alert
	 * 
	 * @param object A jQuery object for the parent in which the alert resides
	 */
	removeAlert: function(parent){
		jQuery(parent).children('.alert').remove();
		if(this.joomla){
			jQuery(parent).children('#system-message-container').html('');
		}
	},

	/* ----------- Form Validation ---------- */

	/**
	 * Shows the validation state of a specified form field. See Bootstrap
	 * documentation at http://getbootstrap.com/css/#forms-control-validation
	 *
	 * @param object A jQuery object for the form input
	 * @param string (optional) The state to show. Allowable values are 'error', 'warning', and 'success'. Default is warning.
	 * @param boolean (optional) Whether or not to show an icon with the valudation. Default is true.
	 */
	showValidationState: function(obj, state, icon){
		// make sure the input exists, and if not return without doing anything
		if(!obj.length){
			return;
		}

		// before continuing, reset this field
		this.hideValidationState(obj);

		// establish variable defaults
		if(state !== 'error'
		&& state !== 'warning'
		&& state !== 'success'){
			state = 'warning';
		}
		if(icon !== true
		&& icon !== false){
			icon = true;
		}

		// add the necessary classes to the form-group
		obj.closest('.form-group').addClass('has-' + state + ' has-feedback');

		// add the icon
		if(icon){
			// determine which icon to show
			var glyphiconName = '';
			switch(state){
				case 'error':
					glyphiconName = 'remove';
					break;
				case 'success':
					glyphiconName = 'ok';
					break;
				default:
					glyphiconName = 'warning-sign';
					break;
			}

			// add the necessary markup
			obj.parent().append('<span class="glyphicon glyphicon-' + glyphiconName + ' form-control-feedback"></span>');
		}
	},

	/**
	 * Removes all validation related markup
	 */
	hideAllValidationStates: function(){
		jQuery('.form-group').removeClass('has-error has-warning has-success has-feedback');
		jQuery('.form-control-feedback').remove();
	},

	/**
	 * Removes all validation related markup from the specified element
	 *
	 * @param object A jQuery object for the form element
	 */
	hideValidationState: function(obj){
		// make sure the input exists, and if not return without doing anything
		if(!obj.length){
			return;
		}

		// remove the class from the form-group
		obj.closest('.form-group').removeClass('has-error has-warning has-success has-feedback');

		// remove the icon
		obj.siblings('.form-control-feedback').remove();
	},

	/**
	 * Hides any validation errors on the specified form field
	 * 
	 * @param object A jQuery object for the form element
	 */
	hideFieldError: function(obj){
		obj.next('.help-inline').hide();
		obj.closest('.control-group').removeClass('warning');
	},

	/**
	 * Indicates a validation error on the specified form field
	 * 
	 * @param object A jQuery object for the form element
	 */
	showFieldError: function(obj){
		obj.next('.help-inline').show();
		obj.closest('.control-group').addClass('warning');		
	},
};