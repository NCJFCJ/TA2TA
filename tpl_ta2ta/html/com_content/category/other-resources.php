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
	<p>Other resources for TA Providers include those TA projects that are designed for TA Providers, but may not necessarily be limited to TA Providers. Such projects include TA on developing curricula and distance learning programs, meeting and training accessibility, abuse in later life, and language access.</p>
	<br>
	<?php
	// articles
	$firstDrawn = false;
	foreach($this->items as $articleData):
		// get the image data
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
				<?php if($imgData && isset($imgData->image_intro)):
					if($linkData && isset($linkData->urla)): ?>
						<a href="<?php echo $linkData->urla; ?>" target="_blank"><img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo (isset($imgData->image_intro_alt) ? $imgData->image_intro_alt : ''); ?>"></a>
					<?php else: ?>
						<img src="<?php echo $imgData->image_intro; ?>" alt="<?php echo (isset($imgData->image_intro_alt) ? $imgData->image_intro_alt : ''); ?>">
					<?php endif;
				endif; ?>
			</div>
			<div class="col-sm-8 col-md-9">
				<?php if($linkData && isset($linkData->urla)): ?>
					<h4><a href="<?php echo $linkData->urla; ?>" target="_blank"><?php echo $articleData->title; ?></a></h4>
				<?php else: ?>
					<h4><?php echo $articleData->title; ?></h4>
				<?php endif;
				echo $articleData->introtext;
				if($linkData && isset($linkData->urla)): ?>
					<a href="<?php echo $linkData->urla; ?>" target="_blank" class="btn btn-dark">Visit This Resource</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach;?>
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