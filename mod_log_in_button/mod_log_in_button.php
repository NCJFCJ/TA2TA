<?php
/**
* @package  mod_log_in_button
* @copyright (C) 2014 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

// Determines wether or not the user is logged in.
$logged_in = false;
	
// get the user groups for this user
$user_groups = JFactory::getUser()->getAuthorisedGroups();	

// check if the user is a member of 'guests'
if(!in_array(9, $user_groups)){
	$logged_in = true;
}



?>
<?php if($logged_in): ?>
<a class="btn <?php echo $params->get('btn_class', 'btn-orange btn-lg'); ?>" href="<?php echo JRoute::_('index.php?Itemid='.$params->get('menu_item')); ?>">
	<?php 
	$btn_icon = $params->get('btn_icon', '');
	if(!empty($btn_icon)):
	?>
	<span class="<?php echo $btn_icon; ?>"></span>&nbsp;
	<?php endif; ?>
	<?php echo $params->get('btn_text'); ?>
</a>
<?php else: ?>
<a class="btn <?php echo $params->get('nli_btn_class', 'btn-default btn-lg'); ?>" href="<?php echo JRoute::_('index.php?Itemid='.$params->get('nli_menu_item')); ?>">
	<?php 
	$btn_icon = $params->get('nli_btn_icon', '');
	if(!empty($btn_icon)):
	?>
	<span class="<?php echo $btn_icon; ?>"></span>&nbsp;
	<?php endif; ?>
	<?php echo $params->get('nli_btn_text', 'Sign In'); ?>
</a>
<?php endif; ?>