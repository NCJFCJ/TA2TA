<?php
/**
* @package Contact Form
* @copyright (C) 2013 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

// Get the path to which the AJAX call will be made
$postURL = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . '/modules/' . pathinfo(__FILE__, PATHINFO_FILENAME) . '/ajax/send-email.php';

// grab parameters
$afterFormText 	= $params->get('afterFormText');
$beforeFormText	= $params->get('beforeFormText');
$btnClass 		= $params->get('btnClass');
$btnIcon		= $params->get('btnIcon');
$moduleclass	= $params->get('moduleclass');

// print before form text, if it exists
if(!empty($beforeFormText)){
	echo $beforeFormText;
}
?>
<script type="text/javascript">
	jQuery(function($){
		// remove required flags from everything so our javascript can run
		$('input,select').removeAttr('aria-required required');

		// validate on form submit
		$('#contactForm').submit(function(event){
			// variables
			var errors = [];
			var inputs = $(this).find('input, textarea, button');
			var parent = $('#contactForm').parent().parent();
			var serialData = $(this).serialize();

			// prevent the form from submitting
			event.preventDefault();
			
			// disable all form elements to prevent double entry
	    inputs.prop('disabled', true);

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// name
			if(!ta2ta.validate.hasValue($('#inputName'), 3)){
				errors.push('You must provide your name.');
			}

			// email
			if(!ta2ta.validate.email($('#inputEmail'), 3)){
				errors.push('You must provide a valid email address.');
			}

			// message
			if(!ta2ta.validate.hasValue($('#inputMessage'), 3)){
				errors.push('You must enter a message.');
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();

				// re-enable all form elements to allow the user to fix the issues
	     		inputs.prop('disabled', false);
			}else{
				// post to AJAX
				var request = $.ajax({
			        data: serialData,
			        dataType: 'json',
			        type: 'POST',
			        url: '<?php echo $postURL; ?>'
			    });
			    
			    // fires when the AJAX call completes
				request.done(function(response, textStatus, jqXHR){
					// check if this has an error
					if(response.error){
						ta2ta.bootstrapHelper.showAlert(parent, response.error, 'warning', true, true);
			      	}else{
			      		ta2ta.bootstrapHelper.showAlert(parent, 'Your message has been sent. We will get back to you shortly!', 'success', true, true);
			      		// clear the form
						inputs.val('');
						ta2ta.bootstrapHelper.hideAllValidationStates();
			      	}				
				});
				
				// catch if the AJAX call fails completelly
			    request.fail(function(jqXHR, textStatus, errorThrown){
			        // notify the user that an error occured
			        ta2ta.bootstrapHelper.showAlert(parent, 'Server error. Please try again later.', 'error', true, true);
			    });
				
				// no matter what happens, enable the form again
				request.always(function (){
			        inputs.prop('disabled', false);
			    });
			}
		});

		// check name on change
		$('#inputName').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});

		// check the email on change
		$('#inputEmail').change(function(){
			ta2ta.validate.email($(this),3);
		});

		// check message on change
		$('#inputMessage').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});
	});
</script>
<div<?php echo (!empty($moduleclass) ? ' class="' . $moduleclass . '"' : ''); ?>>
	<form id="contactForm" class="form-validate big-inputs" method="post" action="<?php echo $postURL; ?>">
		<div class="row">
			<div class="col-sm-6">
				<fieldset>
					<div class="form-group">
						<div class="input-group">
							<span 
								class="input-group-addon has-tooltip icomoon-user"
								data-original-title="Name"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<input
								aria-invalid="false"
								class="form-control"
								id="firstName"
								name="firstName"
								style="display: none;"
								type="hidden"
								value=""
							/>	
							<input 
								type="text" 
								name="inputName"
								id="inputName"
								value=""
								placeholder="Name"
								class="form-control required"
								aria-invalid="false"
								required="true"
								aria-required="true"
								/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span 
								class="input-group-addon has-tooltip icomoon-envelop"
								data-original-title="Email Address"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<input 
								type="email" 
								name="inputEmail"
								id="inputEmail"
								value=""
								placeholder="Email Address"
								class="form-control required"
								aria-invalid="false"
								required="true"
								aria-required="true"
								/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span 
								class="input-group-addon has-tooltip icomoon-edit"
								data-original-title="Message"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<textarea 
								name="inputMessage"
								id="inputMessage"
								placeholder="Message"
								class="form-control required"
								rows="4"
								aria-invalid="false"
								required="true"
								aria-required="true"></textarea>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 form-actions">
				<button type="submit"<?php echo (empty($btnClass) ? '' : ' class="' . $btnClass . '"'); ?>><?php echo (empty($btnIcon) ? '' : '<span class="' . $btnIcon . '"></span> '); ?> Send</button>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>
<?php
if(!empty($afterFormText)){
	echo $afterFormText;
}
?>