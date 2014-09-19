<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// calDate
// calTimezone

// we need to get all events that fall within
// 1) the specified date range
// 2) the filters selected by the user

?>
<div class="calendar-view" id="cv-list">
	<!-- start foreach here-->
	<!-- remember edit permissions!! -->
	<div class="row-fluid list-event webinar">
		<div class="list-date-tab">
			<table>
				<tr>
					<td><small>Monday</small></td>
				</tr>
				<tr>
					<td><strong style="font-size: 185%">Mar</strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 185%">11</strong></td>
				</tr>
				<tr>
					<td><small>2013</small></td>
				</tr>
			</table>
		</div>
		<div class="list-event-detail">
			<h3><a onclick="openDialog('webinar-detials');" style="color: #333; cursor: pointer; text-decoration: none">An Ordinarily Verbose Webinar Name Which was Entered by the User Who Setup This Event</a></h3>
			<p class="hostOrg">Host Organization: <a href="javascript:void(0);">NCJFCJ <i class="icon-share"></i></a></p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam gravida molestie diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus scelerisque arcu sed quam mattis quis ultrices tortor convallis. Nulla elementum massa diam, ac aliquam augue.</p>	
			<a href="#infoPopup" data-toggle="modal" class="btn mini-button btn-info"><i class="icon-th-list icon-white"></i> View Details</a>
		</div>
	</div>
	<!-- end foreach here -->
	<div class="row-fluid list-event conference">
		<div class="list-date-tab">
			<table>
				<tr>
					<td><small>Tuesday</small></td>
				</tr>
				<tr>
					<td><strong>Mar</strong></td>
				</tr>
				<tr>
					<td><strong>12</strong></td>
				</tr>
				<tr>
					<td><small>2013</small></td>
				</tr>
			</table>
		</div>
		<div class="list-event-detail">
			<h3>An Ordinarilly Verbose Conference Name Which was Entered by the User Who Setup This Event</h3>
			<p>March 12, 2013 - San Antonio, TX</p>
			<p class="hostOrg">Host Organization: <a href="javascript:void(0);">NCJFCJ <i class="icon-share"></i></a></p>
			<div class="alert alert-error">
				This conference is pending approval.
			</div>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam gravida molestie diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus scelerisque arcu sed quam mattis quis ultrices tortor convallis. Nulla elementum massa diam, ac aliquam augue.</p>	
			<a href="#infoPopup" data-toggle="modal" class="btn mini-button btn-info"><i class="icon-th-list icon-white"></i> View Details</a>
		</div>
	</div>
	<div class="row-fluid list-event webinar">
		<div class="list-date-tab">
			<table>
				<tr>
					<td><small>Wednesday</small></td>
				</tr>
				<tr>
					<td><strong style="font-size: 185%">Mar</strong></td>
				</tr>
				<tr>
					<td><strong style="font-size: 185%">13</strong></td>
				</tr>
				<tr>
					<td><small>2013</small></td>
				</tr>
			</table>
		</div>
		<div class="list-event-detail">
			<h3><a onclick="openDialog('webinar-detials');" style="color: #333; cursor: pointer; text-decoration: none">An Ordinarilly Verbose Webinar Name Which was Entered by the User Who Setup This Event</a></h3>
			<p class="hostOrg">Host Organization: <a href="javascript:void(0);">NCJFCJ <i class="icon-share"></i></a></p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam gravida molestie diam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus scelerisque arcu sed quam mattis quis ultrices tortor convallis. Nulla elementum massa diam, ac aliquam augue.</p>	
			<a href="#infoPopup" data-toggle="modal" class="btn mini-button btn-info"><i class="icon-th-list icon-white"></i> View Details</a>
		</div>
	</div>
</div>