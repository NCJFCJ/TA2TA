<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="form-horizontal service-form" role="form">
	<div id="alertWrapper"></div>
	<div id="formWrapper">
		<div class="row">
			<div class="col-sm-5">
				<fieldset>
					<legend>Contact Information</legend>
					<input type="hidden" id="serviceType" name="serviceType" value="webinar">
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
						<label class="control-label col-sm-4" for="dates">Date(s) Requested</label>
						<div class="col-sm-8">
							<input type="text" id="dates" name="dates">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="title">Title of Webinar</label>
						<div class="col-sm-8">
							<input type="text" id="title" name="title">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="startTime">Start Time</label>
						<div class="col-sm-8">
							<input type="text" id="startTime" name="startTime">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4" for="duration">Duration</label>
						<div class="col-sm-8">
							<input type="text" id="duration" name="duration">
						</div>
					</div>				
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-11">
				<fieldset>
					<legend>Webinar Specifics</legend>
					<p>We are equiped to facilitate webinars with up to 500 participants. If you require capacity for more than 500 participants, please contact us before completing this request.</p>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-5">
				<div class="form-group">
					<label class="control-label col-sm-4" for="numberOfAttendees">Est. # of Attendees</label>
					<div class="col-sm-8">
						<input type="text" id="numberOfAttendees" name="numberOfAttendees">
					</div>
				</div>	
			</div>
			<div class="col-sm-offset-1 col-sm-5">
				<div class="form-group">
					<label class="control-label col-sm-4" for="numberOfStaff"># of Staff</label>
					<div class="col-sm-8">
						<input type="text" id="numberOfStaff" name="numberOfStaff">
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-11">
				<fieldset>
					<div class="form-group">
						<label for="sendingInfo">Who will be responsible for sending webinar information to participants?</label>
						<select id="sendingInfo" name="sendingInfo" style="width: 175px;">
							<option value="Requesting Organization">My Organization</option>
							<option value="NCJFCJ">NCJFCJ</option>
						</select>
					</div>
					<div class="form-group">
						<label>For your webinar, which materials would you like uploaded?</small></label>
						<div class="row">
							<div class="col-sm-2">
								<label class="checkbox">
									<input type="checkbox" name="materials[]" value="Audio"> Audio
								</label>
								<label class="checkbox">
									<input type="checkbox" name="materials[]" value="Flash Content"> Flash
								</label>
								<label class="checkbox">
									<input type="checkbox" name="materials[]" value="Images"> Images
								</label>								
							</div>
							<div class="col-sm-offset-1 col-sm-8">								
								<label class="checkbox">
									<input type="checkbox" name="materials[]" value="PDFs"> PDFs
								</label>
								<label class="checkbox">
									<input type="checkbox" name="materials[]" value="PowerPoint Presentation"> PowerPoint Presentation
								</label>
								<label class="checkbox">
									<input type="checkbox" name="materials[]" value="Video"> Video
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>For your webinar, which features are you interested in using?</small></label>
						<div class="row">
							<div class="col-sm-2">
								<label class="checkbox">
									<input type="checkbox" name="features[]" value="Breakouts"> Breakouts
								</label>
								<label class="checkbox">
									<input type="checkbox" name="features[]" value="Polling"> Polling
								</label>								
							</div>
							<div class="col-sm-offset-1 col-sm-8">								
								<label class="checkbox">
									<input type="checkbox" name="features[]" value="Screen Sharing"> Screen Sharing
								</label>
								<label class="checkbox">
									<input type="checkbox" name="features[]" value="Question and Answer"> Question and Answer
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="closedCaptioning">Will you need closed captioning for your webinar?</label>
						<select id="closedCaptioning" name="closedCaptioning" style="width: 75px;">
							<option value="No">No</option>
							<option value="Yes">Yes</option>
						</select>
					</div>
					<div class="form-group">
						<label for="llNotes">Miscellaneous/Notes:</label>
						<textarea id="llNotes" name="llNotes"></textarea>
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
		$('#numberOfAttendees').change(function(){
			if(parseInt($(this).val()) > 500){
				alert('As your webinar will have in excess of 500 attendees, please contact the NCJFCJ before submitting this form. We cannot guarentee support above 500 participants.');
			}	
		});
		
		$('#sendingInfo').change(function(){
			if($(this).val() == 'NCJFCJ'){
				alert('In order for the NCJFCJ to market your webinar on your behalf, you must provide a list of potential participant email addresses at least TWO WEEKS prior to the date of your webinar.');
			}
		});
		
		$('#closedCaptioning').change(function(){
			if($(this).val() == 'Yes'){
				alert('Please note we require at least 48 hours notice prior to your webinar to cancel closed captioning.');
			}
		});
		
		$('.service-form').submit(function(event){
			// prevent default posting of form
    		event.preventDefault();
			
			// variables
			var fields = new Array('#serviceType', '#fullName', '#email', '#organization', '#phone', '#dates', '#title', '#startTime', '#duration', '#numberOfAttendees', '#numberOfStaff', '#sendingInfo', '#closedCaptioning', '#llNotes');
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
    			ta2ta.bootstrapHelper.showAlert('#alertWrapper', 'Please enter a valid telephone number.', 'warning');
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
					$('input:checkbox').removeAttr('checked');
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