<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// no direct access
defined('_JEXEC') or die;
$pst = new DateTimeZone('America/Los_Angeles');
$current_time = new DateTime('now', $pst);

$service_type = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], 's') - 1);

// if there is only one webinar in the series, treat this like a single webinar
if($service_type == 'webinar' && $this->data->series && count($this->data->webinars) == 1){
	$this->data->series = false;

	// override the data with that of the single series webinar
	$this->data->end = $this->data->webinars[0]->end;
	$this->data->start = $this->data->webinars[0]->start;
	$this->data->sub_title = $this->data->webinars[0]->sub_title;
}
?>
<div class="row" id="com_servicesPortal">
	<?php if($this->data->registration): ?>
	<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3">
		<?php if($this->data->logo): ?>
			<div class="logo">
				<?php if($this->data->website): ?>
				<a href="<?php echo $this->data->website; ?>" target="_blank">
				<?php endif; ?>
				<img alt="<?php echo $this->data->name; ?>" src="/media/com_ta_providers/logos/<?php echo $this->data->logo; ?>">
				<?php if($this->data->website): ?>
				</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<h1><?php echo $this->data->title; ?></h1>
		<?php if(!$this->data->series && isset($this->data->sub_title) && $this->data->sub_title): ?>
		<h3><?php echo $this->data->sub_title; ?></h3>
		<?php endif; ?>
		<?php if($service_type == 'webinar' && !$this->data->series): ?>
			<div>
				<?php $start_time = new DateTime($this->data->start, $pst);
							echo $start_time->format('F j, Y'); ?> - 
				<?php echo $start_time->format('g:ia'); ?> PT, 
				<?php $mt = new DateTimeZone('America/Denver');
							$start_time->setTimezone($mt);
							echo $start_time->format('g:ia');?> MT, 
				<?php
							$ct = new DateTimeZone('America/Chicago');
							$start_time->setTimezone($ct);
							echo $start_time->format('g:ia'); ?> CT, 
				<?php
							$et = new DateTimeZone('America/New_York');
							$start_time->setTimezone($et);
							echo $start_time->format('g:ia'); ?> ET
			</div>
		<?php endif; ?>

		<p><?php echo $this->data->description; ?></p>
		<hr>
		<form method="post" action="/" role="form" novalidate>
			<p>Thank you for your interest in attending this <?php echo $service_type; ?>. To register, please complete the form below:</p>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<?php echo $this->form->getLabel('fname'); ?>
						<?php echo $this->form->getInput('fname'); ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?php echo $this->form->getLabel('lname'); ?>
						<?php echo $this->form->getInput('lname'); ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-9">
					<div class="form-group">
						<?php echo $this->form->getLabel('email'); ?>
						<?php echo $this->form->getInput('email'); ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<?php echo $this->form->getLabel('zip'); ?>
						<?php echo $this->form->getInput('zip'); ?>
					</div>
				</div>
			</div>
			<?php if($service_type != 'webinar'): ?>
			<div class="form-group">
				<?php echo $this->form->getLabel('address'); ?>
				<?php echo $this->form->getInput('address'); ?><br>
				<?php echo $this->form->getInput('address2'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('phone'); ?>
				<?php echo $this->form->getInput('phone'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('fax'); ?>
				<?php echo $this->form->getInput('fax'); ?>
			</div>
			<?php endif; ?>
			<div class="form-group">
				<?php echo $this->form->getLabel('organization'); ?>
				<?php echo $this->form->getInput('organization'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('occupation'); ?>
				<?php echo $this->form->getInput('occupation'); ?>
			</div>
			<?php 
			/** CUSTOM QUESTIONS **/

			/**
			 * Renders the HTML to display a custom question
			 *
			 * @param int The number corresponding to this question
			 * @param string The question to be asked
			 * @param int The type of question
			 * @return null Draws HTML to the screen
			 */
			function drawQuestion($question_num, $question, $type){
				echo '<div class="form-group">';
				echo '<label for="jform_q' . $question_num . '_answer" class="required">' . $question . '<span class="star">&nbsp;*</span></label>';			
				if($type == 0){
					// fill-in
					echo '<input name="jform[q' . $question_num . '_answer]" id="jform_q' . $question_num . '_answer" value="" class="form-control required" maxlength="255" required="" aria-required="true" type="text">';
				}else{
					// yes-no
					echo '&nbsp;&nbsp;&nbsp;&nbsp;<fieldset id="jform_q' . $question_num . '_answer" class="btn-group required radio" required="" aria-required="true">';
					echo '<input id="jform_q' . $question_num . '_answer0" name="jform[q' . $question_num . '_answer]" value="0" checked="checked" required="" aria-required="true" type="radio">';
					echo '<label for="jform_q' . $question_num . '_answer0" class="btn btn-default btn-danger">No</label>';
					echo '<input id="jform_q' . $question_num . '_answer1" name="jform[q' . $question_num . '_answer]" value="1" required="" aria-required="true" type="radio">';
					echo '<label for="jform_q' . $question_num . '_answer1" class="btn btn-default">Yes</label>';
					echo '</fieldset>';
				}
				echo '</div>';
			}

			// display the questions
			for($i = 1; $i <= 3; $i++){
				if(!empty($this->data->{'registration_q' . $i})){
					drawQuestion($i, $this->data->{'registration_q' . $i}, $this->data->{'registration_q' . $i . '_type'});
				}
			}
			/** END CUSTOM QUESTIONS **/ ?>
			<?php if($this->data->registration_adv_accessibility): ?>
			<div class="form-group">
				<?php echo $this->form->getLabel('accessibility_interpreter'); ?>
				<?php echo $this->form->getInput('accessibility_interpreter'); ?>
			</div>
			<div id="interpreterDetails">
				<div class="form-group">
					<?php echo $this->form->getLabel('accessibility_interpreter_lang'); ?>
					<?php echo $this->form->getInput('accessibility_interpreter_lang'); ?>
				</div>
				<div class="form-group">
					<?php echo $this->form->getLabel('accessibility_simultaneous_interpretation'); ?>
					<?php echo $this->form->getInput('accessibility_simultaneous_interpretation'); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('accessibility_braille'); ?>
				<?php echo $this->form->getInput('accessibility_braille'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('accessibility_large_print'); ?>
				<?php echo $this->form->getInput('accessibility_large_print'); ?>
			</div>
			<div class="form-group">
				<label id="jform_accessibility-lbl" for="jform_accessibility">
					Other Accessibility Needs
				</label>
				<?php echo $this->form->getInput('accessibility'); ?>
			</div>
			<?php else: ?>
			<div class="form-group">
				<label id="jform_accessibility-lbl" for="jform_accessibility">
					Accessibility Needs
				</label>
				<?php echo $this->form->getInput('accessibility'); ?>
			</div>
			<?php endif;
			if($service_type == 'webinar' && $this->data->series): ?>
				<strong>Which webinars in this series do you wish to attend?</strong>
				<table id="webinarRegSeries">
				<?php foreach($this->data->webinars as $webinar):
					$start_time = new DateTime($webinar->start, $pst);
				?>
					<tr>
						<td><input name="services[]" type="checkbox" value="<?php echo $webinar->id; ?>"></td>
						<td><?php echo $start_time->format('F j, Y'); ?></td>
						<td><?php echo $start_time->format('g:ia'); ?> PT<br>
							 	<?php $mt = new DateTimeZone('America/Denver');
											$start_time->setTimezone($mt);
											echo $start_time->format('g:ia');?> MT<br>
								<?php
											$ct = new DateTimeZone('America/Chicago');
											$start_time->setTimezone($ct);
											echo $start_time->format('g:ia'); ?> CT<br>
								<?php
											$et = new DateTimeZone('America/New_York');
											$start_time->setTimezone($et);
											echo $start_time->format('g:ia'); ?> ET
						</td>
						<td><?php echo $webinar->sub_title; ?></td>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php else: ?>
				<input name="services[]" type="hidden" value="<?php echo $this->data->id; ?>" />
			<?php endif; ?>
			<br>
			<div class="form-actions">
				<div class="text-center">
					<?php echo JHtml::_('form.token'); ?>
					<input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					<input type="hidden" name="option" value="com_services" />
					<input type="hidden" name="task" value="registration.save" />
					<input type="hidden" name="jform[service_type]" value="<?php echo $service_type; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
			    <button type="submit" class="btn btn-lg btn-primary">Register For <?php echo ucwords($service_type); ?></button>
		    </div>
			</div>

			<?php 
			// get the current time in PST
			if($service_type == 'webinar'){
				if($this->data->series){
					$end_time = new DateTime($this->data->webinars[count($this->data->webinars) - 1]->end);
				}else{
					$end_time = new DateTime($this->data->end, $pst);
				}
			}else{
				$end_time = new DateTime($this->data->registration_cutoff . ' 23:59:59', $pst);
			}

			// tell the user that this webinar has past	
			if($current_time > $end_time):
			?>
			<div class="portal-overlay">
				<div class="overlay-content">
					<div class="alert alert-danger">
						<h3>You're too late!</h3>
						<p>Thank you for your interest, but this <?php echo $service_type; ?> has passed.</p>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</form>
		<br>
		<br>
		<p class="small">This <?php echo $service_type; ?> is hosted by <?php echo ($this->data->website ? '<a href="' . $this->data->website . '" target="_blank">' . $this->data->name . '</a>' : $this->data->name); ?> with support from the <a href="http://ta2ta.org" target="_blank">TA2TA Project</a> through the <a href="http://ncjfcj.org" target="_blank">National Council of Juvenile and Family Court Judges</a>.</p>
	</div>
	<?php else: ?>
		<br>
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3 alert alert-warning">
			<h2><?php echo ucwords($service_type); ?> Registration Closed</h2>
			<p>This <?php echo $service_type; ?> is not accepting registrants at this time. <br class="hidden-xs">We appologize for any inconvienence.</p>
		</div>
	<?php endif; ?>
</div>
<script type="text/javascript">
	jQuery(function($){
		$("input[name='jform[accessibility_interpreter]']").change(function(){
			toggleInterpreterDetails();
		});
		function toggleInterpreterDetails(){
			if($("input[name='jform[accessibility_interpreter]']:checked").val() == 1){
				$('#interpreterDetails').show();
			}else{
				$('#interpreterDetails').hide();
			}
		}
		toggleInterpreterDetails();
	});
</script>