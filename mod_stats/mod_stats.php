<?php
/**
* @package Stats
* @copyright (C) 2016 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

// determine the start and end dates of the nearest reporting period
$today = new DateTime();
$june30 = DateTime::createFromFormat('m-d-Y', '06-30-' . date('Y'));
if($today > $june30){
	// beginning of year reporting period
	$end = '06-30-' . date('Y');
	$start = '01-01-' . date('Y');
}else{
	// end of last year reporting period
	$end = '12-31-' . (date('Y') - 1);
	$start = '07-01-' . (date('Y') - 1);
}
$endObj = DateTime::createFromFormat('m-d-Y', $end);
$startObj = DateTime::createFromFormat('m-d-Y', $start);
$endObj->sub(new DateInterval('P1D'));
$startObj->add(new DateInterval('P1D'));

// Get the path to which the AJAX call will be made
$postURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/modules/' . pathinfo(__FILE__, PATHINFO_FILENAME) . '/ajax/get-stats.php';
?>
<script type="text/javascript">
	jQuery(function($){
		// remove required flags from everything so our javascript can run
		$('input,select').removeAttr('aria-required required');

		// validate on form submit
		$('#statsFiltersForm').submit(function(event){
			event.preventDefault();

			getStats();
		});

		/**
		 * Queries the server via AJAX to get the necessary statistics
		 */
		function getStats(){
			var inputs = $('#statsFiltersForm').find('input, button');
			var parent = $('#statsTable');
			var serialData = $('#statsFiltersForm').serialize();

			// remove any old alerts
			ta2ta.bootstrapHelper.removeAlert(parent);
			
			// disable all form elements to prevent double entry
	    inputs.prop('disabled', true);

			// post to AJAX
			var request = $.ajax({
        data: serialData,
        dataType: 'json',
        type: 'POST',
        url: '<?php echo $postURL; ?>'
	    });
		    
	    // fires when the AJAX call completes
			request.done(function(response, textStatus, jqXHR){
				// check if this has an error
				if(response.error){
					ta2ta.bootstrapHelper.showAlert(parent, response.error, 'warning', true, true);
      	}else{
      		$('#statsTable').html(response.html);
      		$('#end').val(response.end);
      		$('#start').val(response.start);
      	}				
			});
			
			// catch if the AJAX call fails completelly
	    request.fail(function(jqXHR, textStatus, errorThrown){
        // notify the user that an error occured
        ta2ta.bootstrapHelper.showAlert(parent, 'Server error. Please try again later.', 'error', true, true);
	    });
			
			// no matter what happens, enable the form again
			request.always(function(){
        inputs.prop('disabled', false);
	    });
		}

		// on load, get the stats
		getStats();

		// load the datepickers
		$('#end').datepicker({
			autoclose: true,
			endDate: '+0d',
			format: 'mm-dd-yyyy',
			startDate: '<?php echo $startObj->format('m-d-Y'); ?>'
		}).on('changeDate', function(selected){
      var maxDate = new Date(selected.date.valueOf());
			maxDate.setDate(maxDate.getDate() - 1);
      $('#start').datepicker('setEndDate', maxDate);
		});
		$('#start').datepicker({
			autoclose: true,
			endDate: '-1d',
			format: 'mm-dd-yyyy',
			endDate: '<?php echo $endObj->format('m-d-Y'); ?>'
		}).on('changeDate', function(selected){
      var minDate = new Date(selected.date.valueOf());
			minDate.setDate(minDate.getDate() + 1);
      $('#end').datepicker('setStartDate', minDate);
		});
	});
</script>
<div<?php echo (!empty($moduleclass) ? ' class="' . $moduleclass . '"' : ''); ?>>
	<form id="statsFiltersForm" class="form-inline" method="post" action="<?php echo $postURL; ?>">
		<div class="form-group">
			<label for="start">Reporting Period: </label>
	    <input type="text" class="date form-control" id="start" name="start" placeholder="mm-dd-yyyy" value="<?php echo $start; ?>">
	  </div>
	  <div class="form-group">
	    <input type="text" class="date form-control" id="end" name="end" placeholder="mm-dd-yyyy" value="<?php echo $end; ?>">
	  </div>
	  <button type="submit" class="btn btn-secondary">Filter</button>
	</form>
	<div id="statsTable">

	</div>
</div>