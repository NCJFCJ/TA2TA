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
		$('#setPassword').submit(function(event){
			var errors = [];
			var parent = $('#setPassword').parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// password
			if(!ta2ta.validate.password($('#jform_password1'), 3)){
				errors.push('You must enter a password that is at least 5 characters long.');
			}

			// password match
			if(!ta2ta.validate.match($('#jform_password1'),$('#jform_password2'), 3)){
				errors.push('The passwords you entered do not match.');
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		});

		// check password on change
		$('#jform_password1').change(function(){
			ta2ta.validate.password($(this),3);
		});

		// check that both password fields match
		$('#jform_password2').change(function(){
			ta2ta.validate.match($('#jform_password1'),$(this),3);
		});
	});
</script>
<div class="reset-complete<?php echo $this->pageclass_sfx?>">
	<h1>Set Your New Password</h1>
	<p>To complete the password reset process, please enter your new password below.</p>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" id="setPassword" method="post" class="form-validate">
		<div class="row">
			<div class="col-sm-6">
				<fieldset>
					<div class="form-group">
						<div class="input-group">
							<span 
								class="input-group-addon has-tooltip icomoon-key"
								data-original-title="<?php echo JText::_('TPL_TA2TA_USERS_PASSWORD1_LABEL'); ?>"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<input 
								type="password" 
								name="jform[password1]"
								id="jform_password1"
								value=""
								placeholder="<?php echo JText::_('TPL_TA2TA_USERS_PASSWORD1_LABEL'); ?>"
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
								data-original-title="<?php echo JText::_('TPL_TA2TA_USERS_PASSWORD2_LABEL'); ?>"
								data-placement="top"
								data-toggle="tooltip">
							</span>
							<input 
								type="password" 
								name="jform[password2]"
								id="jform_password2"
								value=""
								placeholder="<?php echo JText::_('TPL_TA2TA_USERS_PASSWORD2_LABEL'); ?>"
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
