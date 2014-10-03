<?php
/**
 * @version                $Id: component.php $
 * @package                Joomla.Site
 * @subpackage        	   tpl_ta2ta
 * @copyright              Copyright (C) 2013 NCJFCJ. All rights reserved.
 */

defined( '_JEXEC' ) or die;
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
<div id="services">
	<?php
	// articles
	$count = 0;
	foreach($this->items as $articleData):
		// increment the count
		$count++;

		// if this is the fourth article, add the specialized services divider first
		if($count == 4){
			echo '<div class="row divider"><div class="col-xs-12"><div class="divider-text">Specialized Services</div></div></div>';
		}

		// get the image data
		$imgData = (isset($articleData->images) ? json_decode($articleData->images) : false);
		?>
		<div class="row well">
			<div class="hidden-xs col-sm-1">
				<?php if(!empty($imgData->image_intro)): ?>
					<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo (isset($imgData->image_intro_alt) ? $imgData->image_intro_alt : ''); ?>">
				<?php endif; ?>
			</div>
			<div class="col-xs-12 col-sm-11">
				<h3><?php echo $articleData->title; ?></h3>
				<?php echo $articleData->introtext; ?>
			</div>
		</div>
	<?php endforeach;?>
</div>