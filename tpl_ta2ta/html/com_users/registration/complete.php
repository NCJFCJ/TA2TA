<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// TO DO: Make the template reference on line 26 variable
?>
<script type="text/javascript">
	$(function(){
		$('#system-message-container').hide();
	});
</script>
<div class="row registration-complete <?php echo $this->pageclass_sfx;?>">
	<div class="col-sm-offset-2 col-sm-8">
		<div class="row">
			<div class="col-sm-4">
				<img src="/templates/ta2ta/img/thumbs-up.jpg" style="width: 100%;" alt="">
			</div>
			<div class="col-sm-8">
				<h2>You Did It!</h2>
				<p>You will receive an email in the next couple of minutes prompting you to confirm your email address. Please click the link in that email within 24 hours. After your email address has been verified, our staff will review your request for an account within three days.</p>
				<p>Thank you, and welcome to the TA2TA community!</p>
			</div>
		</div>
	</div>
</div>
