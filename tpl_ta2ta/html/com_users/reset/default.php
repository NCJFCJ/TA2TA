<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	jQuery(function($){
		// remove required flags from everything so our javascript can run
		$('input,select').removeAttr('aria-required required');

		// validate on form submit
		$('#user-registration').submit(function(event){
			var errors = [];
			var parent = $('#user-registration').parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// email
			if(!ta2ta.validate.email($('#jform_email'), 3)){
				errors.push('You must provide a valid email address.');
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		});

		// check the email on change and clone to the hidden username field
		$('#jform_email').change(function(){
			ta2ta.validate.email($(this),3);
		});
	});
</script>
<div class="reset <?php echo $this->pageclass_sfx?>">
	<div class="page-header">
		<h1>Password Reset</h1>
	</div>
	<p>To reset your password, please enter your email address below. Within a couple of minutes, you will recieve an email containing additional instructions.</p>
	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.request'); ?>" method="post" class="form-validate big-inputs">	
		<div class="row">
			<div class="col-sm-6">
				<fieldset>
					<div class="form-group">
						<div class="input-group">
							<span 
								class="input-group-addon has-tooltip icomoon-envelop"
								data-original-title="<?php echo JText::_('TPL_TA2TA_USERS_EMAIL_ADDRESS'); ?>"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<input 
								type="text" 
								name="jform[email]"
								id="jform_email"
								value=""
								placeholder="<?php echo JText::_('TPL_TA2TA_USERS_EMAIL_ADDRESS'); ?>"
								class="form-control required"
								aria-invalid="false"
								required="true"
								aria-required="true"
								/>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 form-actions">
				<button type="submit" class="btn btn-primary btn-lg validate"><span class="icomoon-checkmark"></span> <?php echo JText::_('JSUBMIT'); ?></button>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>