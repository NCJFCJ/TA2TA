<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="form-horizontal service-form" role="form">
	<div id="alertWrapper"></div>
	<div id="formWrapper">
		<div class="row">
			<div class="col-sm-5">
				<fieldset>
					<legend>Contact Information</legend>
					<input type="hidden" id="serviceType" name="serviceType" value="roundtable">
					<div class="form-group">
						<label class="control-label col-sm-4" for="fullName">Full Name</label>
						<div class="col-sm-8">
							<input type="text" id="fullName" name="fullName">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="email">Email Address</label>
						<div class="col-sm-8">
							<input type="email" id="email" name="email">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="organization">Organization</label>
						<div class="col-sm-8">
							<input type="text" id="organization" name="organization">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="phone">Phone Number</label>
						<div class="col-sm-8">
							<input type="text" id="phone" name="phone">
						</div>
					</div>
				</fieldset>
			</div>
			<div class="col-sm-offset-1 col-sm-5">
				<fieldset>
					<legend>Event Details</legend>
					<div class="form-group">
						<label class="control-label col-sm-4" for="proposedTopic">Proposed Topic</label>
						<div class="col-sm-8">
							<input type="text" id="proposedTopic" name="proposedTopic">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="proposedLocation">Proposed Location(s)</label>
						<div class="col-sm-8">
							<input type="text" id="proposedLocation" name="proposedLocation">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="suggestedDates">Suggested Dates</label>
						<div class="col-sm-8">
							<input type="text" id="suggestedDates" name="suggestedDates">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="suggestedLength">Suggested Length</label>
						<div class="col-sm-8">
							<input type="text" id="suggestedLength" name="suggestedLength">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="estParticipants">Est. # of Participants</label>
						<div class="col-sm-8">
							<input type="text" id="estParticipants" name="estParticipants">
						</div>
					</div>					
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-11">
				<fieldset>
					<legend>Roundtable Specifics</legend>
					<div class="form-group">
						<label for="llActiveProjectsAndGrants">List your organization's active TA projects and grant numbers:</label>
						<textarea id="llActiveProjectsAndGrants" name="llActiveProjectsAndGrants"></textarea>
					</div>
					<div class="form-group">
						<label for="llOtherProjects">Are you a partner on any other TA project that is focused on a similar topic as the proposed roundtable topic?<br><small>If yes, please list the organization’s name and provide a brief description of the project.</small></label>
						<textarea id="llOtherProjects" name="llOtherProjects"></textarea>
					</div>
					<div class="form-group">
						<label for="llBenefit">How does the proposed roundtable benefit the domestic violence, dating violence, sexual assault and stalking field?</label>
						<textarea id="llBenefit" name="llBenefit"></textarea>
					</div>
					<div class="form-group">
						<label for="llAdvanceMission">How does the proposed roundtable advance the mission of OVW?</label>
						<textarea id="llAdvanceMission" name="llAdvanceMission"></textarea>
					</div>
					<div class="form-group">
						<label for="llGoals">What are the tentative goals and outcomes for the proposed roundtable?</label>
						<textarea id="llGoals" name="llGoals"></textarea>
					</div>
					<div class="form-group">
						<label for="llResources">What resources will your organization provide?</label>
						<textarea id="llResources" name="llResources"></textarea>
					</div>
					<div class="form-group">
						<label for="llNCJFCJResources">What resources are needed from the NCJFCJ?</label>
						<textarea id="llNCJFCJResources" name="llNCJFCJResources"></textarea>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="form-actions col-sm-11" style="padding-left: 10px;">
				<div class="pull-left">
					<small>*All fields are required.</small>
				</div>
				<div class="pull-right">
				    <button type="submit" class="btn btn-lg btn-primary">Complete Request</button>
				    <button type="reset" class="btn btn-default btn-lg">Clear Form</button>
			    </div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(function($){
		$('.service-form').submit(function(event){
			// prevent default posting of form
    		event.preventDefault();
			
			// variables
			var fields = new Array('#serviceType', '#fullName', '#email', '#organization', '#phone', '#proposedTopic', '#proposedLocation', '#suggestedDates', '#suggestedLength', '#estParticipants', '#llActiveProjectsAndGrants', '#llOtherProjects', '#llBenefit', '#llAdvanceMission', '#llGoals', '#llResources', '#llNCJFCJResources');
			var inputs = $(this).find('input, textarea, button');
			var regExp;
			var validationErr = false;
			
			// serialize the data in the form
	    	var serialData = $(this).serialize();
			
			// reset all error indicators
			ta2ta.bootstrapHelper.removeAlert($('#alertWrapper'));
			
			// check that all fields have values and clear any errors they may have
			$.each(fields, function(index, selector){
				//hideFieldError(selector);
				if(!$(selector).val()){
					validationErr = true;
					//showFieldError(selector);
				}
			});
			
			// if a field is empty, show the error
			if(validationErr){
				ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'All fields are required, please enter a value for each.', 'warning');
				scrollToTop();
				return;
			}
			
			// vaidate other fields
			
			// email
			regExp = /(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*:(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)(?:,\s*(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*))*)?;\s*)/;
    		if(!regExp.test($('#email').val())){
    			ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'Please enter a valid email address.', 'warning');
    			scrollToTop();
				return;
    		}
			
			// phone
			var phone = $('#phone').val();
    		// regex
    		regExp = /[\d() -ext\.]/;
    		if(!regExp.test(phone)){
    			ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'Please enter a valid telephone number.', 'warning');
    			scrollToTop();
				return;
    		}
    		// strip formatting
    		phone = unformatPhoneNumber(phone);
    		// min length
    		if(phone.length < 10){
    			ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'The phone number you entered is invalid, please remember to include your area code.', 'warning');
    			scrollToTop();
				return;
    		}
    		// max length
    		if(phone.length > 15){
    			ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'Please enter a valid telephone number.', 'warning');
    			scrollToTop();
				return;
    		}
	    	
			// disable all form elements to prevent double entry
	     	inputs.prop('disabled', true);			
			
			// make an AJAX call to the server side script to send this as an email
			var request = $.ajax({
		        data: serialData,
		        dataType: "json",
		        type: 'POST',
		        url: '/forms/email-form.php'
		    });
		    
		    // fires when the AJAX call completes
			request.done(function(response, textStatus, jqXHR){
				// check if this has an error
				if(response.error){
					ta2ta.bootstrapHelper.showAlert('#alertWrapper', response.error, 'warning');
		      		scrollToTop();
				}else{
		      		// clear the form
					$.each(fields, function(index, selector){
						$(selector).val('');
					});
					// show the confirmation page
					window.location.replace('<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . '/service-request-recieved.html'; ?>');
		      	}				
			});
			
			// catch if the AJAX call fails completelly
		    request.fail(function(jqXHR, textStatus, errorThrown){
		        // notify the user that an error occured
		        ta2ta.bootstrapHelper.showAlert('#alertWrapper','Server error. Please try again later.','error')
		    	scrollToTop();
			});
			
			// no matter what happens, enable the form again
			request.always(function (){
		        inputs.prop('disabled', false);
		    });			
		});
		
		function scrollToTop(){
			$('body,html').animate({
				scrollTop: 0
			}, 800);
		}
	});
</script>