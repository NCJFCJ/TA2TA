<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// require the helper file
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');

$action = '';
$color = '';
$description = '';
switch($this->state){
	case 1:
		$action = 'Published';
		$color = 'green';
		$description = 'The following library resource has been immediatelly made available on the TA2TA webiste.';
		break;
	case 2:
		$action = 'Archived';
		$color = 'red';
		$description = 'The following library resource has been added to the library archive. Only NCJFCJ, OVW, and the posting organization will have access to it.';
		break;
	default:
		break;
}

if($this->error){
	$color = 'red';
	$description = 'An error occured. Please contact the NCJFCJ and let them know that this library resource could not be ' . $action;
	$heading = 'Resource Could Not Be ' . $action;
}else{
	$heading = 'Library Resource ' . $action;
}

// figure paths
$this->item->document_path = '/media/com_library/resources/' . $this->item->id . '-' . $this->item->base_file_name . '.pdf';
?>
<div class="item-page">
	<div class="row">
		<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
			<div class="row">
				<div class="col-xs-12" style="text-align: center;">
					<h1 style="color: <?php echo $color; ?>; padding: 30px 0 10px 0;"><?php echo $heading; ?></h1>
					<p style="padding: 0 0 20px 0;"><?php echo $description; ?></p>
				</div>
			</div>
			<div class="ta-library-item row">
				<div class="col-sm-3 hidden-xs">
					<a href="/media/com_library/resources/<?php echo $this->item->id; ?>-<?php echo $this->item->base_file_name; ?>.pdf" target="_blank" class="cover"><img class="img-polaroid" src="/media/com_library/covers/<?php echo $this->item->id; ?>-<?php echo $this->item->base_file_name; ?>.png" alt="<?php echo $this->item->name; ?> Cover"></a>
					<?php if($this->state == 2): ?>
						<a href="/media/com_library/resources/<?php echo $this->item->id; ?>-<?php echo $this->item->base_file_name; ?>.pdf" target="_blank" title="Resource is Archived" class="archived"><span class="icomoon-folder"></span></a>
					<?php endif; ?>
				</div>
				<div class="col-sm-9">
					<h3><?php echo $this->item->name; ?></h3>
					<?php if($this->item->org_website): ?>
					<h5><strong><a href="<?php echo $this->item->org_website; ?>" target="_blank"><span class="icomoon-earth"></span> <?php echo $this->item->org_name; ?></a></strong></h5>
					<?php else: ?>
					<h5><strong><?php echo $this->item->org_name; ?></strong></h5>
					<?php endif; ?>
					<p><?php echo $this->item->description; ?></p>
					<p><a class="btn btn-primary" href="/media/com_library/resources/<?php echo $this->item->id; ?>-<?php echo $this->item->base_file_name; ?>.pdf" target="_blank"><span class="icomoon-disk"></span> &nbsp;Download</a></p>
				</div>
			</div>
		</div>
	</div>
</div>