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
		$('#resetPassword').submit(function(event){
			var errors = [];
			var parent = $('#resetPassword').parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// email
			if(!ta2ta.validate.email($('#jform_username'), 3)){
				errors.push('You must provide a valid email address.');
			}

			// verification code
			if(!ta2ta.validate.hasValue($('#jform_token'), 3)){
				errors.push('You must enter the verification code sent to your email address.');
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		});

		// check the email on change and clone to the hidden username field
		$('#jform_username').change(function(){
			ta2ta.validate.email($(this),3);
		});

		// check grant number on change
		$('#jform_token').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});
	});
</script>
<div class="reset-confirm<?php echo $this->pageclass_sfx?>">
	<h1>Password Reset Confirmation</h1>
	<p>Please check your email. Within a couple of minutes you should receive a verification code. Please enter it along with your email address below.</p>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.confirm'); ?>" id="resetPassword" method="post" class="form-validate big-inputs">
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
								name="jform[username]"
								id="jform_username"
								value=""
								placeholder="<?php echo JText::_('TPL_TA2TA_USERS_EMAIL_ADDRESS'); ?>"
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
								class="input-group-addon has-tooltip icomoon-key"
								data-original-title="<?php echo JText::_('TPL_TA2TA_USERS_VERIFICATION_CODE'); ?>"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<input 
								type="text" 
								name="jform[token]"
								id="jform_token"
								value=""
								placeholder="<?php echo JText::_('TPL_TA2TA_USERS_VERIFICATION_CODE'); ?>"
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