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
<div id="otherResources">
	<p>Other resources for TA Providers include those TA projects that are designed for TA Providers, but may not necessarily be limited to TA Providers. Such projects include TA on developing curricula and distance learning programs, meeting and training accessibility, abuse in later life, language access, and progress reports.</p>
	<br>
	<?php
	// articles
	$firstDrawn = false;
	foreach($this->items as $articleData):
		// get the image and link data
		$imgData = (isset($articleData->images) ? json_decode($articleData->images) : false);
		$linkData = (isset($articleData->urls) ? json_decode($articleData->urls) : false);

		// see if we need a rule
		if($firstDrawn){
			echo '<hr>';
		}else{
			$firstDrawn = true;
		}

		?>
		<div class="row">
			<div class="col-sm-4 col-md-3">
				<?php if(!empty($imgData->image_intro)):
					if(!empty($linkData->urla)): ?>
						<a href="<?php echo $linkData->urla; ?>" target="_blank"><img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo $imgData->image_intro_alt; ?>"></a>
					<?php else: ?>
						<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo $imgData->image_intro_alt; ?>">
					<?php endif;
				endif; ?>
			</div>
			<div class="col-sm-8 col-md-9">
				<?php if(!empty($linkData->urla)): ?>
					<h4><a href="<?php echo $linkData->urla; ?>" target="_blank"><?php echo $articleData->title; ?></a></h4>
				<?php else: ?>
					<h4><?php echo $articleData->title; ?></h4>
				<?php endif;
				echo $articleData->introtext;
				if(!empty($linkData->urla)): ?>
					<a href="<?php echo $linkData->urla; ?>" target="_blank" class="btn btn-dark">Visit This Resource</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach;?>
</div>