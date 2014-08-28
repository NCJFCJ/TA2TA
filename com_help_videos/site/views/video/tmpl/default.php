<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved. 
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;
?>
<div class="item-page">
	<div class="page-header">
		<h2><a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo $this->video->title ?></a></h2>
	</div>
	<div class="row">
		<div class="col-sm-8">
			<div class="row">
				<div class="col-xs-12">
					<div class="youtube-wrapper">
						<iframe width="100%" height="100%" src="//www.youtube.com/embed/<?php echo $this->video->youtube_id; ?>" frameborder="0" allowfullscreen></iframe>
					</div>
					<div class="row">
						<div class="col-xs-12">
							Posted: &nbsp;<?php echo time_elapsed_string($this->video->published); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<br>
					<p><?php echo $this->video->summary; ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<h4 class="similar-videos-heading"><?php echo $this->categories[$this->video->category]->name; ?> Help Videos</h4>
			<ul class="similar-videos">
				<?php foreach($this->videos as $video):
					if($video->category == $this->video->category): 
					$link = JRoute::_('index.php?view=video&id=' . $video->video_slug . '&catid=' . $video->category_slug);
				?>
					<li>
						<a href="<?php echo $link; ?>">
							<div class="thumbnail-wrapper">
									<img style="width: 100%;" src="http://img.youtube.com/vi/<?php echo $video->youtube_id; ?>/mqdefault.jpg" alt="Thumbnail">
								<?php if(is_new($video->published)): ?>
								<div class="new-ribbon"><img src="/media/com_help_videos/new-ribbon.png" alt="New Video"></div>
								<?php endif; ?>
								<div class="duration"><?php echo gmdate('i:s', $video->duration); ?></div>
							</div>
							<h5><?php echo $video->title; ?></h5>
							<p class="published"><?php echo time_elapsed_string($video->published); ?></p>
							<div class="clearfix"></div>
						</a>
					</li>
				<?php endif;
				endforeach; ?>
			</ul>
			<br>
			<h4>Other Help Videos</h4>
			<ul class="nav nav-pills nav-stacked">
				<?php foreach($this->categories as $category):
					if($category->id != $this->video->category):
						$link = '#';

						// get the link to the first video in this category
						foreach($this->videos as $video){
							if($video->category == $category->id){
								$link = JRoute::_('index.php?view=video&id=' . $video->video_slug . '&catid=' . $video->category_slug);
								break;
							}
						}
				?>
				<li><a href="<?php echo $link; ?>"><?php echo $category->name; ?></a></li>
				<?php endif;
				endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<?php
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