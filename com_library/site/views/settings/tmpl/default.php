<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

// before continuing, we need to handle quotes in the PHP data
foreach($this->resources as &$resource){
	$resource->name = xmlentities($resource->name);
	$resource->created_by = xmlentities($resource->created_by);
}
// htmlentities is insufficient as it doesn't handle apostrophes
function xmlentities($string){
    return str_replace (array('&','"','’',"'",'<','>'),array('&amp;','&quot;','&apos;','&#039;','&lt;','&gt;'),$string);
}

// https?
$https = false;
if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"){
	$https = true;
}
?>
<script type="text/javascript">
	var resources = $.parseJSON('<?php echo str_replace('\n','',json_encode($this->resources)); ?>');
    
    jQuery(document).ready(function($){
    	// draw the table 
    	reloadResourceTable();

    	$('#formReset').click(function(event){
    		event.preventDefault();
    		location.reload();
    	});
    });
    
    /**
     * Function to reload the resource table
     * 
     * @param object List of resources
     * 
     * @return null
     */
    
    function reloadResourceTable(){
    	// get the table
    	var classInt = 0;
    	var firstDrawn = false;
    	var table = jQuery('#resourceList');
    	var rows = '<tr><td colspan="4" class="center no-records">There are no resources entered for <?php echo xmlentities($this->org->name); ?>. <a onclick="newResource();">Try adding one.</a></td></tr>';
    	
    	// check if we have resources to display
    	jQuery.each(resources, function(index,resource){
			if(!firstDrawn){
    			rows = '';
    			firstDrawn = true;
    		}
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + resource.id + '" type="checkbox" value="1" name="cid[]"></input></td><td>' + (resource.state == 0 ? '<span class="icomoon-remove"></span> ' : (resource.state == -1 ? '<span class="icomoon-clock"></span> ' : '')) + '<a onclick="window.location = \'/my-account/library/edit.html?id=' + resource.id + '\';">' + resource.name + '</a></td><td class="hidden-phone">' + resource.created_by + '</td><td>' + resource.id + '</td></tr>';
    		classInt = (classInt ? 0 : 1);
		});
		
		// show/hide applicable toolbar buttons
		if(firstDrawn){
			jQuery('#resourceToolbarEdit').css('visibility', 'visible');
		}else{
			jQuery('#resourceToolbarEdit').css('visibility', 'hidden');
		}
		
		if(resources == ''){
			jQuery('#resourceList thead').hide();
		}else{
			jQuery('#resourceList thead').show();
		}
		
		// clear the check all checkbox
		jQuery('#resourceList input[name="checkall-toggle"]').attr('checked', false);
    	
    	// update the table with this content
    	table.find('tbody').html(rows);
    }
    
    /**
     * Function to edit a single resource
     * 
     * @return null
     */
    function editResource(){
    	ta2ta.bootstrapHelper.removeAlert(jQuery('#resourceTableForm'));
    	var id = getSelectedIds('resourceList', true);
    	if(id != ''){
    		window.location = '/my-account/library/edit.html?id=' + id;
    	}else{
    		// the user did not select anything
    		ta2ta.bootstrapHelper.showAlert(jQuery('#resourceTableForm'), 'Please select a resource.', 'warning');
    	}
    }

    /**
	 * Function to obtain the ids of all records selected in the table, optionally limited to only a portion of those items
	 *
	 * @param 	string The name of the table
	 * @param	boolean Whether to return only the first value, or all
	 *
	 * @return	mixed string if single, array of strings otherwise, false on failure
	 */
    function getSelectedIds(table, single){
    	var ids = new Array();
    	jQuery('#' + table + ' input:checked').each(function(){
    		var id = jQuery(this).attr('id');
    		if(id){
	    		ids.push(id.replace('cb',''));
	    		if(single){
	    			return false;
	    		}
    		}
    	});

    	// check that the array contains data
    	if(ids.length > 0){
    		// if single, return only the first one
    		if(single){
    			return ids[0];
    		}else{
    			return ids;
    		}
    	}else{
    		return false;
    	}
    }

    /**
     * Loads the screen to enter a new resource
     *
     * @return null
     */
    function newResource(){
    	window.location = '/my-account/library/edit.html?id=0';
    }
	
    /**
     * Function to trash one or more resources
     * 
     * @return null
     */
    function trashResources(){
    	ta2ta.bootstrapHelper.removeAlert(jQuery('#resourceTableForm'));
			var ids = getSelectedIds('resourceList', false);
	    	if(ids.length){
				// send an AJAX request to the server
				var request = jQuery.ajax({
					data: {
						ids: ids
					},
					dataType: 'html',
					type: 'POST',
					url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_library&task=trash';?>',
				});
				
				// process the returned ajax
				request.done(function(response, textStatus, jqXHR){
					if(response.status == 'success'){
						ta2ta.bootstrapHelper.showAlert(jQuery('#resourceTableForm'), 'The items you selected have been trashed.', 'success', true, true);
					}else{
						ta2ta.bootstrapHelper.showAlert(jQuery('#resourceTableForm'), 'There was an error discarding the library items you selected. Please try again later and contact us if this issue persists.', 'error', true, true);
					}
				});

				// fire if the ajax request fails
				request.fail(function(jqXHR, textStatus){
					ta2ta.bootstrapHelper.showAlert(jQuery('#resourceTableForm'), 'There was an error discarding the library items you selected. Please try again later and contact us if this issue persists.', 'error', true, true);
				});
    	}else{
    		// the user did not select anything
    		ta2ta.bootstrapHelper.showAlert(jQuery('#resourceTableForm'), 'Please select at least one resource.', 'warning');
    	}
    }
</script>
<div class="ta-directory-settings-edit">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<p>This page allows you to manage the OVW approved resources that <?php echo $this->org->name; ?> has created. Each website user associated with <?php echo $this->org->name; ?> has the ability to modify this information. The information you enter here will be instantly displayed on the website, exactly as you enter it.</p>
	<h3>Resources</h3>
	<form action="/" method="post" enctype="multipart/form-data" name="resourceTableForm" id="resourceTableForm" class="form-validate form-horizontal">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-success" onclick="newResource();">
							<span class="icomoon-plus-circle"></span> <?php echo JText::_('TOOLBAR_NEW'); ?>
						</button>
					</div>
					<div id="resourceToolbarEdit" class="btn-group">
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-sm" onclick="editResource();">
								<span class="icomoon-edit"></span> <?php echo JText::_('TOOLBAR_EDIT'); ?>
							</button>
						</div>
						<div class="btn-group" style="display: inline-block; margin-left: 5px;">
							<button type="button" class="btn btn-default btn-sm" onclick="trashResources();">
								<span class="icomoon-remove"></span> <?php echo JText::_('TOOLBAR_TRASH'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table id="resourceList" class="table table-striped">
					<thead>
						<tr>
							<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
							<th class="left">Title</th>
							<th class="left" style="width: 20%;" class="hidden-phone">Created by</th>
							<th class="nowrap center hidden-phone" style="width: 1%;">ID</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4"></td>
						</tr>
					</tfoot>
					<tbody></tbody>
				</table>
				<p><small><span class="icomoon-clock"></span> = Pending Approval</small></p>
			</div>
		</div>
	</form>
</div>