<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved. 
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// build an array of categories
$categories = array();
$lastCategory = 0;
foreach($this->items as $item){
	if($item->category_id != $lastCategory){
		$tmp = new stdClass();
		$tmp->id = $item->category_id;
		$tmp->name = $item->category_name;
		$categories[] = $tmp;
		$lastCategory = $item->category_id;
	}
}

// print the markup for each category
foreach($categories as $category):
?>
<div id="help-videos">
	<div class="help-video-category">
		<h3><span class="icomoon-movie"></span> <?php echo $category->name; ?></h3>
		<div class="row">
			<?php
			// print the markup for each video in a given category (max of 6)
			$vidCount = 0;
			foreach($this->items as $video):
				if($vidCount >= 6)
					break;
				if($video->category_id == $category->id):
					$link = JRoute::_('index.php?view=video&id=' . $video->video_slug . '&catid=' . $video->category_slug);
			?>
			<div class="help-video col-xs-6 col-sm-4 col-md-3 col-lg-2">
				<div class="thumbnail-wrapper">
					<a href="<?php echo $link; ?>">
						<img style="width: 100%;" src="http://img.youtube.com/vi/<?php echo $video->youtube_id; ?>/mqdefault.jpg" alt="Thumbnail">
					</a>
					<?php if(is_new($video->published)): ?>
					<a href="<?php echo $link; ?>" class="new-ribbon"><img src="/media/com_help_videos/new-ribbon.png" alt="New Video"></a>
					<?php endif; ?>
					<div class="duration"><?php echo gmdate('i:s', $video->duration); ?></div>
				</div>
				<h5><a href="<?php echo $link; ?>"><?php echo $video->title; ?></a></h5>
				<p class="published"><?php echo time_elapsed_string($video->published); ?></p>
			</div>
			<?php 
					$vidCount++;
					if($vidCount % 2 == 0):
						?> <div class="clearfix visible-xs"></div> <?php
					endif;
					if($vidCount % 3 == 0):
						?> <div class="clearfix visible-sm"></div> <?php
					endif;
					if($vidCount % 4 == 0):
						?> <div class="clearfix visible-md"></div> <?php
					endif;
					if($vidCount % 6 == 0):
						?> <div class="clearfix visible-lg"></div> <?php
					endif;
				endif;
			endforeach; ?>
		</div>
	</div>
</div>
<?php endforeach;
/**
 * Checks if this event is considered 'new'
 *
 * @param datetime A datetime object representing the published date of the video
 * @return boolean True if the item is new
 */
function is_new($datetime){
	// the number of days to still be considered new
	$newDays = 60;

	$now = new DateTime;
  $ago = new DateTime($datetime);
	$diff = $now->diff($ago)->format('%a');

  if($diff <= $newDays){
		return true;    	
  }
  return false;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>