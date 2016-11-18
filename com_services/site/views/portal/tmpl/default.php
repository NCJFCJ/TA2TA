<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row" id="com_servicesPortal">
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
		<?php if($this->data->sub_title): ?>
		<h3><?php echo $this->data->sub_title; ?></h3>
		<?php endif; ?>
		<p><?php echo $this->data->description; ?></p>
		<hr>
		<form method="post" action="/" role="form" novalidate>
			<p>To join this webinar, please tell us a bit about yourself</p>
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
			<div class="form-group">
				<?php echo $this->form->getLabel('email'); ?>
				<?php echo $this->form->getInput('email'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('occupation'); ?>
				<?php echo $this->form->getInput('occupation'); ?>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-9">
						<?php echo $this->form->getLabel('num_viewers'); ?>
					</div>
					<div class="col-sm-3">
						<?php echo $this->form->getInput('num_viewers'); ?>
					</div>
				</div>
			</div>
			<br>
			<div class="form-actions">
				<div class="text-center">
					<?php echo JHtml::_('form.token'); ?>
					<input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					<input type="hidden" name="option" value="com_services" />
					<input type="hidden" name="task" value="portal.save" />
					<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
					<input type="hidden" name="adobe_link" value="<?php echo $this->data->adobe_link; ?>" />
			    <button type="submit" class="btn btn-lg btn-primary">Join Webinar</button>
		    </div>
			</div>

			<?php 
			// get the current time in PST
			$pst = new DateTimeZone('America/Los_Angeles');
			$current_time = new DateTime('now', $pst);

			$start_time = new DateTime($this->data->start, $pst);
			$open_time = clone $start_time;
			$open_time->sub(new DateInterval('PT10M'));

			$end_time = new DateTime($this->data->end, $pst);

			// tell the user that this webinar has past	
			if($current_time > $end_time):
			?>
			<div class="portal-overlay">
				<div class="overlay-content">
					<div class="alert alert-danger">
						<h3>You're too late!</h3>
						<p>Thank you for your interest, but this webinar has ended.</p>
					</div>
				</div>
			</div>
			<?php
			// only open this webinar up 10 minutes before it starts
			elseif($open_time > $current_time):
			?>
			<div class="portal-overlay">
				<div class="overlay-content">
					<div class="alert alert-info">
						<h3>You're too early!</h3>
						<p>This webinar is scheduled to start <?php echo $start_time->format('l F j, Y \a\t g:ia'); ?> PST. You will be able to join the webinar up to 10 minutes before it starts. Please return then.</p>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Time Zone</th>
									<th>Start Time</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Pacific</td>
									<td><?php echo $start_time->format('g:ia'); ?></td>
								</tr>
								<tr>
									<td>Mountain</td>
									<td>
										<?php
											$mt = new DateTimeZone('America/Denver');
											$start_time->setTimezone($mt);
											echo $start_time->format('g:ia');
										?>
									</td>
								</tr>
								<tr>
									<td>Central</td>
									<td>
										<?php
											$ct = new DateTimeZone('America/Chicago');
											$start_time->setTimezone($ct);
											echo $start_time->format('g:ia');
										?>
									</td>
								</tr>
								<tr>
									<td>Eastern</td>
									<td>
										<?php
											$et = new DateTimeZone('America/New_York');
											$start_time->setTimezone($et);
											echo $start_time->format('g:ia');
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</form>
		<br>
		<br>
		<p class="small">This webinar is hosted by <?php echo ($this->data->website ? '<a href="' . $this->data->website . '" target="_blank">' . $this->data->name . '</a>' : $this->data->name); ?> with support from the <a href="http://ta2ta.org" target="_blank">TA2TA Project</a> through the <a href="http://ncjfcj.org" target="_blank">National Council of Juvenile and Family Court Judges</a>.</p>
	</div>
</div>