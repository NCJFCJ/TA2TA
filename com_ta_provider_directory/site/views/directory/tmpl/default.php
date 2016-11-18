<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// before continuing, we need to handle quotes in the PHP data
foreach($this->providers as &$provider){
	$provider->name = xmlentities($provider->name);
	foreach($provider->projects as &$project){
		$project->title = xmlentities($project->title);
		$project->summary = xmlentities($project->summary);
		foreach($project->contacts as &$contact){
			$contact->last_name = xmlentities($contact->last_name);
			$contact->title = xmlentities($contact->title);
		}
	}
}
// htmlentities is insufficient as it doesn't handle apostrophes
function xmlentities($string){
    return str_replace (array('"','’',"'",'<','>'),array('&quot;','&apos;','&#039;','&lt;','&gt;'),$string);
}

// require the helper file
require_once(JPATH_COMPONENT . '/helpers/ta_provider_directory.php');

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
$permission_level = Ta_provider_directoryHelper::getPermissionLevel();
?>
<script type="text/javascript">
	var displayProviders = new Array;
	var grantPrograms = $.parseJSON('<?php echo json_encode($this->grantPrograms); ?>');
	var numGrantPrograms = <?php echo count($this->grantPrograms); ?>;
	var providers = $.parseJSON('<?php echo str_replace('\n','',json_encode($this->providers)); ?>');
			
	// document ready	
	jQuery(function($){
		$('#filters input, #filters select').change(function(){
			loadDirectory();
		});

		$('.checkAll, .uncheckAll').click(function(){
			loadDirectory();
		});
		
		$('#filters').submit(function(event){
			event.preventDefault();
			loadDirectory();
		});
		
		// When the page first loads, load the directory
		loadDirectory();
	});
	
	/**
	 * Changes the current page which is displayed
	 * 
	 * @param obj The button that was clicked
	 */
	function changePage(btn){
		// only process a change if the clicked button is not already active
		if(!(jQuery(btn).closest('li').hasClass('active'))){					
			// draw the page content
			drawContent(parseInt(jQuery(btn).attr('data-page-number')));
			
			// scroll to top
			window.scrollTo(0,0);
		}
	}
	
	/**
	 * Redraws the content to display results for the selected page
	 * 
	 * @param int The page number
	 */
	function drawContent(page){
		// determine the limit
		var limit = jQuery('#pageLimit').val();
		if(limit == '*'){
			limit = displayProviders.length;
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
		for(var i = firstRecord; i < displayProviders.length && i < lastRecord; i++){
			// add a horizontal rule for all but the first entry
			if(htmlOutput != ''){
				htmlOutput += '<div class="row"><div class="col-xs-12"><hr class="clr divider"></div></div>';
			}
			htmlOutput += '<div class="ta-directory-provider row"><div class="col-xs-12">';
			htmlOutput += '<h3 style="margin: 10px 0 0 0;">' + displayProviders[i].name + '</h3>';
			if(displayProviders[i].website != ''){
				htmlOutput += '<p style="margin: 0 0 20px;"><a href="' + displayProviders[i].website + '" target="_blank"><span class="icomoon-earth"></span> ' + displayProviders[i].website + '</a></p>';
			}
			for(var j = 0; j < (displayProviders[i].projects).length; j++){
				htmlOutput += '<div class="project">';
				htmlOutput += '<h4>' + displayProviders[i].projects[j].title + '</h4>';
				if(displayProviders[i].projects[j].summary != null){
					htmlOutput += '<p>' + displayProviders[i].projects[j].summary + '</p>';
				}else{
					htmlOutput += '<p>There is currently no summary available for this project.</p>';
				}
				// project contacts
				if(displayProviders[i].projects[j].contacts.length > 0){
					htmlOutput += '<div class="contacts row">';
					var openRow = false;
					for(var k = 0; k < displayProviders[i].projects[j].contacts.length; k++){
						if(!openRow){
							htmlOutput += '<div style="margin-left: 0">';
							openRow = true;
						}
						htmlOutput += '<div class="vcard col-sm-6">';
						
						htmlOutput += '<p><b>' + displayProviders[i].projects[j].contacts[k].first_name + ' ' + displayProviders[i].projects[j].contacts[k].last_name + '</b>';
						if(displayProviders[i].projects[j].contacts[k].title != ''){
							htmlOutput += '<br>' + displayProviders[i].projects[j].contacts[k].title;
						}				
						if(displayProviders[i].projects[j].contacts[k].phone != ''){
							htmlOutput += '<br>' + formatPhoneNumber(displayProviders[i].projects[j].contacts[k].phone);
						}
						if(displayProviders[i].projects[j].contacts[k].email != ''){
							htmlOutput += '<br><a href="mailto:' + displayProviders[i].projects[j].contacts[k].email + '">' + displayProviders[i].projects[j].contacts[k].email + '</a>'; 
						}
						htmlOutput += '</div>';
						if((k % 2) == 1){
							htmlOutput += '</div>';
							openRow = false;
						}
					}
					if(openRow){
						htmlOutput += '</div>';
					}
					htmlOutput += '</div>';
				}
				// grant programs
				if(displayProviders[i].projects[j].grantPrograms.length > 0){
					htmlOutput += '<b>Grant Programs:</b>';
					var grantProgramNames = new Array;
					for(var k = 0; k < displayProviders[i].projects[j].grantPrograms.length; k++){
						for(var l = 0; l < grantPrograms.length; l++){
							if(grantPrograms[l].id == displayProviders[i].projects[j].grantPrograms[k]){
								grantProgramNames.push(grantPrograms[l].name);
								break;
							}
						}
					}
					// sort the names so they are alphabetical
					grantProgramNames.sort();
					
					// single column
					htmlOutput += '<div class="grantPrograms row"><div class="col-sm-6"><ul>';
					// draw the grant programs
					for(var l = 0; l < grantProgramNames.length; l++){
						if(grantProgramNames.length > 3 && l == Math.ceil(grantProgramNames.length/2)){
							htmlOutput += '</ul></div><div class="col-sm-6"><ul>';
						}
						htmlOutput += '<li>' + grantProgramNames[l] + '</li>';
					}
					htmlOutput += '</ul></div></div>';
				}
				htmlOutput += '</div>'
			}
			htmlOutput += '</div></div>';
		}
		
		// make sure we have content, and if not, add it
		if(htmlOutput == ''){
			htmlOutput = '<div class="alert alert-block alert-error"><h4>No Records To Display</h4>There are no Technical Assistance Providers who match the filters you selected. Please change your search term or select different grant programs.</div></p>';
		}
		
		// render the output
		jQuery('#providers').html(htmlOutput);
		
		// draw the pagination
		ta2ta.bootstrapHelper.drawPagination(jQuery('#paginationWrapper'),page,limit,displayProviders.length);
	}	
	
	/**
     * Function to format a phone number
     * 
     * @return string The phone number, digits only
     */
    function formatPhoneNumber(phone){
    	if(phone){
    		var formattedNumber = '';
    		if(phone.charAt(0) == '1'){
    			// this is a 1-800 or similar type number, format accordingly
    			formattedNumber = phone.substr(0,1) + '-' + phone.substr(1,3) + '-' + phone.substr(4,3) + '-' + phone.substr(7,4);
    			// if there is an extension, add it
    			if(phone.length > 11){
    				formattedNumber += ' ext. ' + phone.substring(11);
    			}
    		}else{
    			// this is a normal number
    			formattedNumber = '(' + phone.substr(0,3) + ') ' + phone.substr(3,3) + '-' + phone.substr(6,4);
    			// if there is an extension, add it
    			if(phone.length > 10){
    				formattedNumber += ' ext. ' + phone.substring(10);
    			}
    		}
    		return formattedNumber;
    	}
    	return '';
    }
		
	// reloads the TA provider directory with new data
	function loadDirectory(){
		// if we have no provider data, do not continue
		if(!providers){
			return;
		}
		
		// use a local copy of the provider data so the original data does not get changed
		var tmpProviders = JSON.parse(JSON.stringify(providers));
		
		// clear the old providers
		displayProviders = [];
		
		// get the grant program filters
		var grantPrograms = [];
		jQuery('#filters input:checked').each(function() {
            grantPrograms.push(jQuery(this).val());
        });

		// get the search string
		var searchString = jQuery('#search').val();
		
		// sanitize the search string so it doesn't mess with the regex
		if(searchString !== ''){
			var searchExp = new RegExp(searchString.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&"), 'gi');
		}

		// check each provider for a match
		jQuery.each(tmpProviders, function(index, provider){
			// projects of this provider
			var forceMatch = false;
			var matchedProjects = new Array();
			var nameMatch = false;
			
			// check the organization name
			if(searchString !== ''){
				if((provider.name).match(searchExp)){
					// there was a match, bold it
					provider.name = (provider.name).replace(searchExp, '<b>$&</b>');
					nameMatch = true;	
					// if the search string is empty, all grant programs are selected, and this organization has no projects, display it
					if(numGrantPrograms == grantPrograms.length
						&& provider.projects.length == 0){
						forceMatch = true;
					}				
				}
			}else{
				// if the search string is empty, all grant programs are selected, and this organization has no projects, display it
				if(numGrantPrograms == grantPrograms.length
					&& provider.projects.length == 0){
					forceMatch = true;
				}
			}

			// loop through each project
			jQuery.each(provider.projects, function(index2, project){
				// check if there is a grant program match
				var grantProgramMatch = false;
				$.each(project.grantPrograms, function(index3, grantProgram){
					if($.inArray(grantProgram, grantPrograms) >= 0){
						grantProgramMatch = true;
					}
				});

				// if the grant program didn't match, there is no need to continue
				if(!grantProgramMatch){
					return true;
				}
				
				// if the search string is blank, add this project, there is no need to continue
				if(searchString === ''){
					matchedProjects.push(project);
					return true;
				}

				// process the string based search
				var searchStringMatch = false;
				
				// project title
				if(project.title
				&& (project.title).match(searchExp)){
					project.title = (project.title).replace(searchExp, '<b>$&</b>');
					searchStringMatch = true;
				}
				
				// project summary
				if(project.summary
				&& (project.summary).match(searchExp)){
					project.summary = (project.summary).replace(searchExp, '<b>$&</b>');
					searchStringMatch = true;
				}

				// contacts
				jQuery.each(project.contacts, function( index3, contact ){
					// first name
					if(contact.first_name
					&& (contact.first_name).match(searchExp)){
						contact.first_name = (contact.first_name).replace(searchExp, '<b>$&</b>');
						searchStringMatch = true;
					}

					// last name
					if(contact.last_name
					&& (contact.last_name).match(searchExp)){
						contact.last_name = (contact.last_name).replace(searchExp, '<b>$&</b>');
						searchStringMatch = true;
					}

					// title
					if(contact.title
					&& (contact.title).match(searchExp)){
						contact.title = (contact.title).replace(searchExp, '<b>$&</b>');
						searchStringMatch = true;
					}
				});
				
				// check for a match
				if(searchStringMatch || nameMatch){
					// add this project to the list
					matchedProjects.push(project);
				}			
			});
			
			// if there was a search match, use it
			if(matchedProjects.length || forceMatch){	
				var providerObj = new Object();
				providerObj.name = provider.name;
				providerObj.website = provider.website;
				providerObj.projects = matchedProjects;
				
				// Add this project to those to be displayed
				displayProviders.push(providerObj);
			}
		});
				
		// draw content
		drawContent(1);
	}
</script>
<div class="item-page">
	<div class="row">
		<form id="filters" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div class="col-sm-2 filters">
				<?php if($permission_level > 0): ?>
				<a class="btn btn-primary" style="width: 100%; padding-left: 0; padding-right: 0" href="my-account/directory.html?edit=0">
					<span class="icomoon-plus-circle"></span>&nbsp; <b>Add<span class="hidden-sm"> Project</span></b>
				</a>
				<?php else: ?>
				<a class="btn btn-default" href="/login.html" style="width: 100%; padding-left: 0; padding-right: 0">
					<span class="icomoon-enter"></span> Sign In
				</a>
				<?php endif; ?>	
				<br>
				<br>	
				<div class="panel-group filter-list">
					<div class="panel panel-default">			
						<div class="panel-heading">
							<h4 class="panel-title">Grant Programs</h4>
						</div>
						<div class="panel-collapse expanding collapse in">
							<div class="panel-body">
								<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
								<div class="row">
									<div class="col-sm-6">
									<?php
										$midPoint = ceil(count($this->grantPrograms) / 2);
										for($i = 0; $i < count($this->grantPrograms); $i++): 
											if($i == $midPoint){
												echo '</div><div class="col-sm-6">';
											}
										?>
										<label class="checkbox">
											<input type="checkbox" name="grantPrograms[]" id="cb<?php echo $this->grantPrograms[$i]->id; ?>" value="<?php echo $this->grantPrograms[$i]->id; ?>" checked> <?php echo $this->grantPrograms[$i]->name; ?>
										</label>
									<?php endfor; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<p><small><span class="glyphicon glyphicon-info-sign"></span> Note: TA Providers who have not yet entered projects will only display if all grant programs are selected.</small></p>
			</div>
			<div class="col-sm-10">
				<div class="row filters">
					<div class="col-sm-7">
						<div class="form-group">
							<div class="input-group col-sm-6 col-lg-5">
								<input id="search" type="search" placeholder="Project Search" class="form-control">
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
							<label>Providers Per Page</label>
						</div>
					</div>
				</div>
				<div id="providers"></div>
				<div id="paginationWrapper"></div>
			</div>
		</form>
	</div>
</div>
