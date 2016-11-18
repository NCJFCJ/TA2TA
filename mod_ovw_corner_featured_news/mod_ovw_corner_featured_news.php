<?php
/**
* @package  mod_ovw_corner_featured_news
* @copyright (C) 2016 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

// get the featured OVW news items
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->quoteName(array(
	'id',
  'title',
  'alias',
  'introtext'
)));
$query->from($db->quoteName('#__content'));
$query->where($db->quoteName('featured') . ' = ' . $db->quote('1'));
$query->where($db->quoteName('catid') . ' = ' . $db->quote('16'));
$query->where($db->quoteName('state') . ' = ' . $db->quote('1'));
$query->where($db->quoteName('publish_up') . ' < NOW()');
$query->where('(' . $db->quoteName('publish_down') . ' = ' . $db->quote('0000-00-00 00:00:00') . ' OR ' . $db->quoteName('publish_down') . ' > NOW())');
$query->order('publish_up DESC');
$db->setQuery($query);
$articles = $db->loadObjectList();
?>

<div class="ovw-featured-news">

<?php
// loop through and display the articles
foreach($articles as $article): ?>
	<a class="row" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id='.$article->id); ?>">
		<div class="col-xs-12">
			<div class="well">
				<div class="row">
					<div class="col-sm-4 col-lg-3 news-title">
						<h3><?php echo $article->title; ?></h3>
					</div>
					<div class="col-sm-8 col-lg-9 news-content">
						<?php
						// get the excerpt and limit its length
				    $excerpt = explode(' ', strip_tags($article->introtext), 70);
				    if(count($excerpt) >= 70){
				      array_pop($excerpt);
				      $excerpt[count($excerpt) - 1] .= '...';
				    }
				    $excerpt = implode(' ', preg_replace('`\[[^\]]*\]`','', $excerpt));
				    echo $excerpt;
						?>
						<div class="row">
							<div class="col-xs-12 text-right">
								<div class="btn btn-orange">Read More</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</a>
<?php endforeach; ?>
</div>