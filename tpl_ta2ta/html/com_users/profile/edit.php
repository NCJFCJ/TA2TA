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

// variables
$newsletter = 0;
?>
<script type="text/javascript">
	jQuery(function($){
		// remove required flags from everything so our javascript can run
		$('input,select').removeAttr('aria-required required');

		// validate on form submit
		$('#memberProfile').submit(function(event){
			var errors = [];
			var parent = $('#memberProfile').parent().parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// validate name
			if(!ta2ta.validate.hasValue($('#jform_name'), 1)){
				errors.push('You must provide your name.');
			}

			// validate passwords only if there is a value
			if($('#jform_password1').val() || $('#jform_password2').val()){
				// password
				if(!ta2ta.validate.password($('#jform_password1'), 1)){
					errors.push('You must enter a password that is at least 5 characters long.');
				}

				// password match
				if(!ta2ta.validate.match($('#jform_password1'),$('#jform_password2'), 1)){
					errors.push('The passwords you entered do not match.');
				}
			}

			// email
			if(!ta2ta.validate.email($('#jform_email1'), 1)){
				errors.push('You must provide a valid email address.');
			}

			// email match
			if(!ta2ta.validate.match($('#jform_email1'),$('#jform_email2'), 1)){
				errors.push('The email addresses you entered do not match.');
			}

			// city
			if(!ta2ta.validate.hasValue($('#jform_profile_city'), 1)){
				errors.push('You must enter your city.');
			}

			// region
			if(!ta2ta.validate.hasValue($('#jform_profile_region'), 1)){
				errors.push('You must select your state or territory.');
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		});

		// check name on change
		$('#jform_name').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});

		// check password on change
		$('#jform_password1').change(function(){
			ta2ta.validate.password($(this),2);
		});

		// check that both password fields match
		$('#jform_password2').change(function(){
			ta2ta.validate.match($('#jform_password1'),$(this),2);
		});

		// check the email on change and clone to the hidden username field
		$('#jform_email1').change(function(){
			ta2ta.validate.email($(this),3);
			$('#jform_username').val($(this).val());
		});

		// check that both email fields match
		$('#jform_email2').change(function(){
			ta2ta.validate.match($('#jform_email1'),$(this),3);
		});

		// check city on change
		$('#jform_profile_city').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});

		// check region on change
		$("#jform_profile_region").chosen().change(function(){
		    ta2ta.validate.hasValue($(this),3);
		});

		// check phone on change
		$('#jform_profile_phone').change(function(){
			ta2ta.validate.hasValue($(this),2);
		});

		// fix crazy disappearing input-group-addon
		$('.input-group-addon').mouseout(function(){
			$(this).show();
		});
	});
</script>

<div class="profile-edit <?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>
	<form id="memberProfile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate big-inputs" enctype="multipart/form-data">
	<div class="row">
	<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
		<?php $fields = $this->form->getFieldset($fieldset->name);
		if (count($fields)): ?>
			<div class="col-sm-6">
				<fieldset>
				<?php foreach ($fields as $field) : // Iterate through the fields in the set and display them.
					$icon = null;
					$langString = '';
					switch($field->name){
						case 'jform[profile][city]':
							$icon = 'office';
							$langString = 'PLG_USER_PROFILE5_FIELD_CITY_LABEL';
							break;
						case 'jform[email1]':
							$icon = 'envelop';
							$langString = 'TPL_TA2TA_USERS_EMAIL_ADDRESS';
							break;
						case 'jform[email2]':
							$icon = 'envelop';
							$langString = 'TPL_TA2TA_USERS_EMAIL2_LABEL';
							break;
						case 'jform[profile][region]':
							$icon = 'grid';
							$langString = 'PLG_USER_PROFILE5_FIELD_REGION_LABEL';
							break;
						case 'jform[name]':
							$icon = 'user';
							$langString = 'TPL_TA2TA_USERS_FULL_NAME_LABEL';
							break;
						case 'jform[password1]':
							$icon = 'key';
							$langString = 'TPL_TA2TA_USERS_PASSWORD1_LABEL';
							break;
						case 'jform[password2]':
							$icon = 'key';
							$langString = 'TPL_TA2TA_USERS_PASSWORD2_LABEL';
							break;
						case 'jform[profile][phone]':
							$icon = 'phone';
							$langString = 'PLG_USER_PROFILE5_FIELD_PHONE_LABEL';
							break;
					}
					if($field->name == 'jform[username]'): ?>
						<div style="height: 0; overflow: hidden; width: 0;">
							<?php echo $field->input;?>
						</div>
					<?php
				    elseif($field->name == 'jform[profile][newsletter]'):
						$newsletter = $field->value;
					elseif($field->type == 'Spacer' || $field->name == 'jform[params][editor]' || $field->name == 'jform[params][language]' || $field->name == 'jform[profile][grant]'):
						// do nothing!
					elseif ($field->hidden || $field->name == 'jform[captcha]'):// If the field is hidden, just display the input.
						echo $field->input;
					else: ?>
						<div class="form-group">
							<div class="input-group">
								<?php if ($icon) : ?>
									<span 
										class="input-group-addon has-tooltip<?php echo ($icon ? " icomoon-$icon" : ''); ?>"
										data-original-title="<?php echo JText::_($langString); ?>"
										data-placement="top"
										data-toggle="tooltip">
									</span>
								<?php endif;
								if($field->name == 'jform[profile][region]') :
									echo $field->input;
								else: ?>
								<input 
									type="<?php echo $field->type; ?>" 
									name="<?php echo $field->name; ?>"
									id="<?php echo $field->id; ?>"
									value="<?php echo $field->value; ?>"
									placeholder="<?php echo JText::_($langString) . (!$field->required ? ' ' . JText::_('COM_USERS_OPTIONAL') : ''); ?>"
									class="form-control<?php echo ($field->name == 'jform[password1]' || $field->name == 'jform[password2]' ? ' validate-password' : '') . ($field->required ? ' required' : ''); ?>"
									size="25"
									aria-invalid="false"
									<?php if ( $field->required ) : ?>
									required="true"
									aria-required="true"
									<?php endif; ?>
									/>
								<?php endif; ?>
							</div>
						</div>
					<?php endif;?>
				<?php endforeach;?>
				</fieldset>
			</div>		
		<?php endif;?>
	<?php endforeach;?>
		</div>
		<div class="row">
			<div class="col-xs-12" id="newsletter-controls">
				<label><?php echo JText::_('PLG_USER_PROFILE5_FRONT_FIELD_NEWSLETTER_LABEL'); ?></label>
				<div class="controls">
					<fieldset id="jform_profile_newsletter" class="radio btn-group">
						<input id="jform_profile_newsletter0" type="radio" value="0" name="jform[profile][newsletter]" <?php echo (!$newsletter ? 'checked ' : ''); ?>/>
						<label class="btn btn-default" for="jform_profile_newsletter0">No</label>
						<input id="jform_profile_newsletter1" type="radio" value="1" name="jform[profile][newsletter]" <?php echo ($newsletter ? 'checked ' : ''); ?>/>
						<label class="btn btn-default" for="jform_profile_newsletter1">Yes</label>
					</fieldset>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 form-actions">
				<button type="submit" class="btn btn-primary btn-lg validate"><span class="icomoon-checkmark"></span> <?php echo JText::_('JSUBMIT');?></button>
				<button type="reset" class="btn btn-lg"><span class="icomoon-close"></span> Reset</button>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="profile.save" />
				<?php echo JHtml::_('form.token');?>
				<br>
				<br>
				<small><?php echo JText::_('TPL_TA2TA_FORM_ALL_REQUIRED'); ?></small>
			</div>
		</div>
	</form>
</div>