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
<div id="latestNews">
	<?php
	// articles
	$firstPage = (filter_has_var(INPUT_GET, 'start') ? false : true);
	$summary_length = $this->params->get('summary_length');
	for($i = 0; $i < count($this->items); $i++):
		// get the data for the next article
		$articleData = $this->items[$i];
		$imgData = (isset($articleData->images) ? json_decode($articleData->images) : false);

		// determine the link to the article
		$articleURL = '/ta-updates/' . $articleData->id . '-' . $articleData->alias . '.html';
		
		// draw the top side-by-side items
		if($i < 2 && count($this->items) > 1 && $firstPage):
			if($i == 0):
				// start the row ?>
				<div class="row top-articles <?php echo ($this->params->get('show_dividers') && count($this->items) > 1 ? 'show-dividers ' : ''); ?><?php echo $this->params->get('row_class'); ?>">
			<?php endif; 
			// draw the article code for a top column
			?>
			<div class="col-sm-6">
				<div class="row">
					<div class="col-xs-12 intro-image">
						<?php if(!empty($imgData->image_intro)): ?>
							<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo $imgData->image_intro_alt; ?>">
						<?php endif; ?>
					</div>
				</div>
				<div class="row">
					<article class="col-xs-12">
						<header>
							<h3><a href="<?php echo $articleURL; ?>"><?php echo $articleData->title; ?></a></h3>
						</header>
						<?php 
						$article_text = $articleData->introtext;
						if($summary_length > 0){
							// shorten the article to the specified length
							$article_text = truncateHTML($article_text, $summary_length);
							echo $article_text;
						}else{
							// display the entire article
							echo $article_text;
						} ?>
						<div class="row">
							<div class="col-xs-6 post-date">
								Posted: <?php echo date('F j, Y',strtotime($articleData->publish_up)); ?>
							</div>
							<div class="col-xs-6">
								<?php if(strlen($article_text) < strlen($articleData->introtext)): ?>
								<div class="more-btn">
									<a href="<?php echo $articleURL; ?>" class="more"><span class="icomoon-arrow-right"></span> <?php echo $this->params->get('read_more_text'); ?></a>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</article>
				</div>
			</div>
			<?php if($i == 1):
				// end the row ?>
				</div>
			<?php endif;
		else: // draw the full-width articles
			if(($i > 1 || ($i >= 1 && !$firstpage)) && $this->params->get('show_dividers')):?>
				<hr class="clr divider">
			<?php endif; ?>
			<div class="row <?php echo $this->params->get('row_class'); ?>">
				<div class="col-sm-3 col-lg-2 intro-image">
					<?php if(!empty($imgData->image_intro)): ?>
						<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo $imgData->image_intro_alt; ?>">
					<?php endif; ?>
				</div>
				<article class="col-sm-9 col-lg-10">
					<header>
						<div class="post-date">
							Posted: <?php echo date('F j, Y',strtotime($articleData->publish_up)); ?>
						</div>
						<h3><a href="<?php echo $articleURL; ?>"><?php echo $articleData->title; ?></a></h3>
					</header>
					<div class="row">
						<div class="col-xs-12">
					<?php 
					$article_text = $articleData->introtext;
					if($summary_length > 0){
						// shorten the article to the specified length
						$article_text = truncateHTML($article_text, $summary_length);
						echo $article_text;
					}else{
						// display the entire article
						echo $article_text;
					} ?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<?php if(strlen($article_text) < strlen($articleData->introtext)): ?>
							<div class="more-btn">
								<a href="<?php echo $articleURL; ?>" class="more"><span class="icomoon-arrow-right"></span> <?php echo $this->params->get('read_more_text'); ?></a>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</article>		
			</div>		
	<?php endif;
	endfor;
	if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
		<?php endif; ?>
		<div class="text-center">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>
<?php
/**
 * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
 *
 * @param string $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param string $ending Ending to be appended to the trimmed string.
 * @param boolean $exact If false, $text will not be cut mid-word
 * @param boolean $considerHtml If true, HTML tags would be handled correctly
 *
 * @return string Trimmed string.
 */
function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true){
	if($considerHtml){
		// if the plain text is shorter than the maximum length, return the whole text
		if(strlen(preg_replace('/<.*?>/', '', $text)) <= $length){
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach($lines as $line_matchings){
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if(!empty($line_matchings[1])){
				// if it's an "empty element" with or without xhtml-conform closing slash
				if(preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])){
					// do nothing
				// if tag is a closing tag
				}else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)){
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if($pos !== false){
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				}else if(preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)){
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if($total_length+$content_length> $length){
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if(preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)){
					// calculate the real length of all entities in the legal range
					foreach($entities[0] as $entity){
						if($entity[1]+1-$entities_length <= $left){
							$left--;
							$entities_length += strlen($entity[0]);
						}else{
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			}else{
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length){
				break;
			}
		}
	}else{
		if(strlen($text) <= $length){
			return $text;
		}else{
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if(!$exact){
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if(isset($spacepos)){
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml){
		// close all unclosed html-tags
		foreach($open_tags as $tag){
			$truncate .= '</' . $tag . '>';
		}
	}
	return $truncate;
}
?>