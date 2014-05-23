<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

// Get the settings for registration
$usersConfig = JComponentHelper::getParams('com_users');
$allowReg = $usersConfig->get('allowUserRegistration');
?>
<script type="text/javascript">
	jQuery(function($){
		// remove required flags from everything so our javascript can run
		$('input,select').removeAttr('aria-required required');
		
		// validate on form submit
		$('#loginForm').submit(function(event){
			var errors = [];
			var parent = $('#login').parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// validate email
			if(!ta2ta.validate.email($('#username'), 1)){
				errors.push('The email address you entered is not valid.');
			}

			// validate password
			if(!ta2ta.validate.hasValue($('#password'), 1)){
				errors.push('You must enter a password.');
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		});

		// check the email on the fly
		$('#username').change(function(){
			ta2ta.validate.email($(this),3);
		});
	});
</script>
<div class="login <?php echo $this->pageclass_sfx?>" id="login">
	<?php if($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-sm-<?php echo ($allowReg ? '6' : '12'); ?>">
			<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
			<div class="login-description">
			<?php endif; ?>
		
				<?php if ($this->params->get('logindescription_show') == 1) : ?>
					<?php echo $this->params->get('login_description'); ?>
				<?php endif; ?>
		
				<?php if (($this->params->get('login_image') != '')) :?>
					<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
				<?php endif; ?>
		
			<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
			</div>
			<?php endif; ?>
			<div class="well">
				<h2><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
				<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="big-inputs" id="loginForm">
					<fieldset>
						<?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
							<?php if (!$field->hidden) : 
								$icon = null;
								$isUsername = false;
								$langString = '';
								switch($field->name){
									case 'password':
										$icon = 'key';
										$langString = 'JGLOBAL_PASSWORD';
										break;
									case 'username':
										$icon = 'envelop';
										$isUsername = true;
										$langString = 'TPL_TA2TA_USERS_EMAIL_ADDRESS';
										break;
								}
								?>
								<div class="form-group">
									<div class="input-group">
										<?php if ($icon) : ?>
											<span 
												class="input-group-addon has-tooltip<?php echo ($icon ? " icomoon-$icon" : ''); ?>"
												data-original-title="<?php echo JText::_($langString); ?>"
												data-placement="top"
												data-toggle="tooltip">
											</span>
										<?php endif; ?>
										<input 
											type="<?php echo $field->type; ?>" 
											name="<?php echo $field->name; ?>"
											id="<?php echo $field->id; ?>"
											value="<?php echo $field->value; ?>"
											placeholder="<?php echo JText::_($langString); ?>"
											class="form-control validate-<?php echo $field->name . ($field->required ? ' required' : ''); ?>"
											<?php if ( $field->required ) : ?>
											required="true"
											aria-required="true"
											<?php endif; ?> 
											/>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>		
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-lg"><span class="icomoon-enter"></span> <?php echo JText::_('TPL_TA2TA_USERS_LOGIN_BUTTON'); ?></button>
						</div>
						<p>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a><br>
						</p>
						<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</fieldset>
				</form>
			</div>
		</div>
		<?php if($allowReg): ?>
		<div class="col-sm-6">
			<div class="well" id="userRegistrationWell">
				<h2><?php echo JText::_('TPL_TA2TA_NEW_USER_REGISTRATION'); ?></h2>
				<p><?php echo JText::_('TPL_TA2TA_NEW_USER_REGISTRATION_COPY'); ?></p>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>" class="btn btn-primary btn-lg">
					<span class="icomoon-signup"></span> <?php echo JText::_('TPL_TA2TA_USERS_LOGIN_REGISTER_BUTTON'); ?>
				</a>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>