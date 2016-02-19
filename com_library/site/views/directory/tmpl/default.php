<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// before continuing, we need to handle special characters in the PHP data
if($this->items){
	foreach($this->items as &$item){
		$item->name = htmlentities($item->name, ENT_QUOTES);
		$item->org = htmlentities($item->org, ENT_QUOTES);
		$item->description = htmlentities($item->description, ENT_QUOTES);
		$item->base_file_name = htmlentities($item->base_file_name, ENT_QUOTES);
		$item->document_path = $item->document_path;
		$item->cover_path = $item->cover_path;
		$item->org_website = $item->org_website;
	}
}

// require the helper file
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
$permission_level = LibraryHelper::getPermissionLevel();
?>
<script type="text/javascript">
	var items = $.parseJSON('<?php echo str_replace(array('\n','\r','\t'),'',json_encode($this->items)); ?>');
	
	// document ready	
	$(function(){
		$('#filters input, #filters select').change(function(){
			loadLibrary();
		});

		$('.checkAll, .uncheckAll').click(function(){
			loadLibrary();
		});
		
		$('#filters').submit(function(event){
			event.preventDefault();
			loadLibrary();
		});
		
		// When the page first loads, load the library
		loadLibrary();
	});
	
	// reloads the library with new data
	function loadLibrary(){
		// make a local copy of the items for modification
		var temporaryDisplayItems = items;
		if(!temporaryDisplayItems){
			return;
		}
		
		// clear the old items
		displayItems = new Array;
		
		// get filters
		var targetAudiences = new Array;
		
		var searchString = $('#search').val();

		<?php if($permission_level == 2): ?>
		var states = new Array;
		$('input:checkbox[name="states[]"]:checked').each(function(){
			states.push($(this).val());
		});
		<?php endif; ?>

		$('input:checkbox[name="targetAudiences[]"]:checked').each(function(){
      targetAudiences.push($(this).val());
    });
				
		// Perform filtering first
		for(var i = 0; i < temporaryDisplayItems.length; i++){
			var matched = false;

			// check the archive status
			<?php if($permission_level == 2): ?>
			if($.inArray(temporaryDisplayItems[i].state, states) == -1){
				continue;
			}
			<?php endif; ?>

			// Loop through all target audiences, checking them first
			for(var j = 0; j < (temporaryDisplayItems[i].targetAudiences).length; j++){
				// See if there is a match, if not, skip this record
				if($.inArray(temporaryDisplayItems[i].targetAudiences[j],targetAudiences) >= 0){
					// There was a match, check for a search string, if none, this matches
					if(searchString == ''){
						// This project matches the target audience filter, and the search string was blank
						matched = true;
					}else{
						// Run text based search
						// sanitize the search string so it doesn't mess with the regex
						var searchExp = new RegExp(searchString.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&'), 'gi');

						// item name
						if((temporaryDisplayItems[i].name).match(searchExp)){
							temporaryDisplayItems[i].name = (temporaryDisplayItems[i].name).replace(searchExp, '<b>$&</b>');
							matched = true;
						}
						// item description
						if((temporaryDisplayItems[i].description).match(searchExp)){
							temporaryDisplayItems[i].description = (temporaryDisplayItems[i].description).replace(searchExp, '<b>$&</b>');
							matched = true;
						}
						// item org
						if((temporaryDisplayItems[i].org).match(searchExp)){
							temporaryDisplayItems[i].org = (temporaryDisplayItems[i].org).replace(searchExp, '<b>$&</b>');
							matched = true;
						}
					}
				}
			}

			// if there was a search match, use it
			if(matched){
				// Add this project to those to be displayed
				displayItems.push(temporaryDisplayItems[i]);
			}
		}
		
		// draw content
		drawContent(1);
	}
	
	/**
	 * Redraws the content to display results for the selected page
	 * 
	 * @param int The page number
	 */
	function drawContent(page){
		// determine the limit
		var limit = $('#pageLimit').val();
		if(limit == '*'){
			limit = displayItems.length;
		}
		
		// variables
		var htmlOutput = '';
		
		// figure out which records to display
		var firstRecord = (page - 1) * limit;
		var lastRecord = limit;
		if(isNaN(firstRecord)){
			firstRecord = 0;
		}else{
			lastRecord = page * limit;
		} 
						
		// start looping
		for(var i = firstRecord; i < displayItems.length && i < lastRecord; i++){
			// add a horizontal rule for all but the first entry
			if(htmlOutput != ''){
				htmlOutput += '<div class="row"><div class="col-xs-12"><hr class="clr divider"></div></div>';
			}
			htmlOutput += '<div class="ta-library-item row"><div class="col-sm-3 hidden-xs">';
			htmlOutput += '<a href="' + displayItems[i].document_path + '" target="_blank" class="cover"><img class="img-polaroid" src="' + displayItems[i].cover_path + '" alt="' + displayItems[i].name + ' Cover"></a>';
			if(displayItems[i].new){
				htmlOutput += '<a href="' + displayItems[i].document_path + '" target="_blank" class="new-ribbon"><img src="/media/com_library/new-ribbon.png" alt="Newly Uploaded"></a>';
			}
			<?php if($permission_level == 2): ?>
			if(displayItems[i].state == -1){
				htmlOutput += '<a href="' + displayItems[i].document_path + '" target="_blank" title="Resource is Pending Approval" class="pending-approval"><span class="icomoon-key"></span></a>';
			}
			if(displayItems[i].state == 2){
				htmlOutput += '<a href="' + displayItems[i].document_path + '" target="_blank" title="Resource is Archived" class="archived"><span class="icomoon-folder"></span></a>';
			}
			<?php endif; ?>
			htmlOutput += '</div><div class="col-sm-9">'
			htmlOutput += '<h3>' + displayItems[i].name + '</h3>';
			if(displayItems[i].org_website == ''){
				htmlOutput += '<h5><strong>' + displayItems[i].org + '</strong></h5>';
			}else{
				htmlOutput += '<h5><strong><a href="' + displayItems[i].org_website + '" target="_blank"><span class="icomoon-earth"></span> ' + displayItems[i].org + '</a></strong></h5>';
			}
			htmlOutput += '<p>' + displayItems[i].description + '</p>';
			htmlOutput += '<p><a class="btn btn-primary" href="' + displayItems[i].document_path + '" style="float:left;margin-right:15px;" target="_blank"><span class="icomoon-disk"></span> &nbsp;Download</a>';
			<?php if($permission_level == 2): ?>
				if(displayItems[i].state == -1){
					htmlOutput +=  '<a class="btn btn-danger" href="/my-account/library/approve.html?state=2&amp;id=' + displayItems[i].id + '" style="float:left;margin-right:15px;"><span class="icomoon-folder"></span> &nbsp;Archive</a><a class="btn btn-danger" href="/my-account/library/approve.html?state=1&amp;id=' + displayItems[i].id + '" style="float:left;margin-right:15px;"><span class="icomoon-checkmark"></span> &nbsp;Publish</a>';
				}
			<?php endif; ?>
			htmlOutput += '</p>';
			htmlOutput += '</div></div>';
		}
		
		// make sure we have content, and if not, add it
		if(htmlOutput == ''){
			htmlOutput = '<p>There are no resources that match the filters you selected.</p>';
		}
		
		// render the output
		$('#taLibraryItems').html(htmlOutput);
		
		// draw the pagination
		ta2ta.bootstrapHelper.drawPagination(jQuery('#paginationWrapper'),page,limit,displayItems.length);
	}
	
	/**
	 * Changes the current page which is displayed
	 * 
	 * @param obj The button that was clicked
	 */
	function changePage(btn){
		// only process a change if the clicked button is not already active
		if(!($(btn).closest('li').hasClass('active'))){					
			// draw the page content
			drawContent(parseInt($(btn).attr('data-page-number')));
			
			// scroll to top
			window.scrollTo(0,0);
		}
	}
</script>
<div class="item-page">
	<div class="page-header">
		<h2><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Technical Assistance Resource Library</a></h2>
	</div>
	<div class="alert alert-info alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<strong>Note:</strong> Products and resources are posted on this page pursuant to the special conditions of the Cooperative Agreements between OVW and the individual TA Providers. Please contact the individual TA Provider for permission to use or reprint these products and resources.
	</div>
	<div class="row">
		<form id="filters" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="col-sm-2 filters">
				<?php if($permission_level > 0): ?>
				<a class="btn btn-primary" style="width: 100%; padding-left: 0; padding-right: 0" href="/my-account/library/edit.html?id=0">
					<span class="icomoon-plus-circle"></span>&nbsp; <b>Add<span class="hidden-sm"> Resource</span></b>
				</a>
				<?php else: ?>
				<a class="btn btn-default" href="/login.html" style="width: 100%; padding-left: 0; padding-right: 0">
					<span class="icomoon-enter"></span> Sign In
				</a>
				<?php endif; ?>	
				<br>
				<br>	
				<div class="panel-group filter-list" id="filterList">
					<?php if($permission_level == 2): ?>
					<div class="panel panel-default">		
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseStatus">Status</a>
						</div>
						<div class="panel-collapse collapse" id="collapseStatus">
							<div class="panel-body">
								<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
								<label class="checkbox">
									<input type="checkbox" name="states[]" value="2"> Archived
								</label>
								<label class="checkbox">
									<input type="checkbox" name="states[]" value="1" checked> Published
								</label>
								<label class="checkbox">
									<input type="checkbox" name="states[]" value="-1"> Pending Approval
								</label>
							</div>
						</div>
					</div>
					<?php endif; ?>
					<div class="panel panel-default">			
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseTargetAudiences">Target Audiences</a>
						</div>
						<div class="panel-collapse expanding collapse in" id="collapseTargetAudiences">
							<div class="panel-body">
								<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
								<div class="row">
									<div class="col-sm-4">
									<?php 
										$records_in_column = ceil(count($this->targetAudiences) / 3);
										for($i = 0; $i < count($this->targetAudiences); $i++): ?>
										<label class="checkbox">
											<input type="checkbox" name="targetAudiences[]" value="<?php echo $this->targetAudiences[$i]->id; ?>" checked> <?php echo str_replace('&', '&amp;', $this->targetAudiences[$i]->name); ?>
										</label>
											<?php if($i == $records_in_column
													|| $i == $records_in_column * 2): ?>
											</div><div class="col-sm-4">
											<?php endif; ?>
										<?php endfor; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>		
			</div>
			<div class="col-sm-10">
				<div class="row filters">
					<div class="col-sm-7">
						<div class="form-group">
							<div class="input-group col-sm-6 col-lg-5">
								<input id="search" type="search" placeholder="Resource Search" class="form-control">
								<span class="input-group-btn">
									<button type="button" class="btn btn-default">
										<span class="icomoon-search"></span>
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group" id="controlsRight">
							<select id="pageLimit">
								<option value="5">5</option>
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="30">30</option>
								<option value="*">All</option>
							</select>
							<label>Items Per Page</label>
						</div>
					</div>
				</div>
				<div id="taLibraryItems"></div>
				<div id="paginationWrapper"></div>
			</div>
		</form>
	</div>
</div>