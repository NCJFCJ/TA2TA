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
	<?php endif;
	
	// content columns
	$current_article_key = 0;
	$num_columns = $this->params->get('num_columns');
	$span_value = 12 / $num_columns;
	for($i = 1; $i <= $num_columns; $i++): ?>
		<section class="col-sm-<?php echo $span_value . ' ' . $this->params->get('column_class'); ?>">
		<?php 
		if(array_key_exists($current_article_key, $this->items)): 
			$articleData = (isset($this->items[$current_article_key]) ? $this->items[$current_article_key] : false);
			$imgData = (isset($articleData->images) ? json_decode($articleData->images) : false);
			$linkData = (isset($articleData->urls) ? json_decode($articleData->urls) : false);
			if($imgData && isset($imgData->image_intro)): ?>
				<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo (isset($imgData->image_intro_alt) ? $imgData->image_intro_alt : ''); ?>">
			<?php endif; 
			if($linkData && isset($linkData->urla)): ?>	
			<h3><a href="<?php echo $linkData->urla; ?>"><?php echo $articleData->title; ?></a></h3>
			<?php else: ?>
			<h3><?php echo $articleData->title; ?></h3>
			<?php
			endif; 
			// article text
			echo $articleData->introtext;
			if($linkData && isset($linkData->urla)): ?>
			<div class="moreWrapper">
				<a href="<?php echo $linkData->urla; ?>" class="more"><?php echo (empty($linkData->urlatext) ? 'Learn More' : $linkData->urlatext); ?></a>
			</div>
		<?php endif;
		endif; ?>
		</section>	
	<?php
		$current_article_key++; 
	endfor;?>
</div>
	<?php
	// content rows
	if(array_key_exists($current_article_key, $this->items)):
		if($this->params->get('show_horizontal_dividers')): ?>
		<hr class="clr divider">
		<?php endif; ?>
		<div class="row <?php echo $this->params->get('row_class'); ?>">
			<?php 
			$articleData = (isset($this->items[$current_article_key]) ? $this->items[$current_article_key] : false);
			$imgData = (isset($articleData->images) ? json_decode($articleData->images) : false);
			if($imgData): ?>
			<div class="col-sm-3 hidden-xs">
				<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo $imgData->image_intro_alt; ?>" class="col-on-top-article-img">
			</div>
			<section class="col-sm-9">
			<?php else: ?>
			<section class="col-xs-12">
			<?php endif; ?>
				<h2><a href="<?php echo $articleData->alias; ?>.html"><?php echo $articleData->title; ?></a></h2>
				<?php echo $articleData->introtext; ?>
			</section>
		</div>
	<?php endif;?>