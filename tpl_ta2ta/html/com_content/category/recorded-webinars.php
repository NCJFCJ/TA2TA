<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 */

defined( '_JEXEC' ) or die;

// Determines wether or not the user is logged in.
$logged_in = false;
	
// get the user groups for this user
$user_groups = JFactory::getUser()->getAuthorisedGroups();	

// check if the user is a member of 'guests'
if(!in_array(9, $user_groups)){
	$logged_in = true;
}
?>
<div class="blog<?php echo $this->pageclass_sfx;?> row">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
	<h2> <?php echo $this->escape($this->params->get('page_subheading')); ?>
		<?php if ($this->params->get('show_category_title')) : ?>
		<span class="subheading-category"><?php echo $this->category->title;?></span>
		<?php endif; ?>
	</h2>
	<?php endif; ?>

	<?php if ($this->params->get('show_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
		<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
		<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
	<?php endif; ?>

	<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
		<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		<?php endif; ?>
		<?php if ($this->params->get('show_description') && $this->category->description) : ?>
			<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
		<?php endif; ?>
		<div class="clr"></div>
	</div>
	<?php endif; ?>
</div>
<div id="recordedWebinars">
	<div class="well">
		<div class="row">
			<div class="hidden-xs col-sm-3 col-lg-2">
				<img src="/images/services-icons/webinars.png" alt="">
			</div>
			<div class="col-xs-12 col-sm-9 col-lg-10">
				<p>OVW and TA providers have hosted several educational webinars for OVW TA Providers, grantees, potential grantees, and subgrantees. These webinars provide an overview of grants financial management, grant fraud investigations, federal civil rights obligations, the grant management system, and rural TA providers and their projects.<p>
				<?php if(!$logged_in): ?>
				<p>For webinars specific to OVW TA providers <a class="btn btn-primary btn-lg" href="/login.html">Please Sign In To Your Account</a></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<br>
	<?php
		// sort the webinars for display
		function cmp($a, $b){
			if($a->access == $b->access){
	    	return strcmp($b->created, $a->created);
			}else{
	    	return strcmp($b->access, $a->access);
			}
		}
		usort($this->items, "cmp");
	
		// loop through and display each webinar
		foreach($this->items as $webinar):
		$urls = json_decode($webinar->urls); ?>
		<div class="row">
			<div class="col-sm-4 col-lg-3<?php echo ($webinar->access > 1 ? ' ta-provider-only' : ''); ?>">
				<div class="video-container">
					<?php if(!empty($urls->urla)): ?>
					<iframe width="263" height="148" src="<?php	echo $urls->urla; ?>" frameborder="0" allowfullscreen></iframe>
					<?php endif;?>
				</div>
				<?php if(!empty($urls->urlb)): ?>
				<a class="btn btn-orange" href="<?php	echo $urls->urlb; ?>" target="_blank"><?php	echo (empty($urls->urlbtext) ? 'Download' : $urls->urlbtext); ?></a>
				<?php endif;
				if(!empty($urls->urlc)): ?>
				<a class="btn btn-orange" href="<?php	echo $urls->urlc; ?>" target="_blank"><?php	echo (empty($urls->urlctext) ? 'Download' : $urls->urlctext); ?></a>
				<?php endif; ?>
			</div>
			<div class="col-sm-8 col-lg-9">
				<h3><?php echo $webinar->title; ?></h3>
				<b><?php echo date('F j, Y', strtotime($webinar->created)); ?></b>
				<?php echo $webinar->introtext; ?>
			</div>
		</div>
		<?php if($webinar !== end($this->items)): ?>
		<hr>
		<?php endif;
	endforeach; ?>
</div>