/* ------ Establish Namespace ------- */
var ta2ta = {}

/* ----- Frameworks and Plugins ----- */

//@codekit-prepend "jquery-1.11.0.js"
//@codekit-append "../../plugins/modernizr/modernizr.js"
//@codekit-append "../../plugins/bootstrap/js/transition.js"
//@codekit-append "../../plugins/bootstrap/js/alert.js"
//@codekit-append "../../plugins/bootstrap/js/collapse.js"
//@codekit-append "../../plugins/bootstrap/js/dropdown.js"
//@codekit-append "../../plugins/bootstrap/js/modal.js"
//@codekit-append "../../plugins/bootstrap/js/tooltip.js"
//@codekit-append "../../plugins/bootstrap/js/popover.js"
//@codekit-append "../../plugins/jquery/imgareaselect/jquery.imgareaselect.js"
//@codekit-append "../../plugins/jquery/nivo/jquery.nivo.slider.js"
//@codekit-append "../../plugins/jquery/waypoints/waypoints.js"
//@codekit-append "../../plugins/bootstrap/plugins/datepicker/js/bootstrap-datepicker.js"
//@codekit-append "../../plugins/jquery/chosen/chosen.jquery.js"
//@codekit-append "bootstrapHelper.js"
//@codekit-append "validation.js"

/* ----- Section Specific Scripts ----- */

// run after the document is ready
jQuery(function($){
	// run ASAP
	$('#sliderNoJS').css('display', 'none');

	// Google Analytics
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-41570936-1', 'ta2ta.org');
	ga('send', 'pageview');	
	
	// Google Analytics Download Tracking
	var filetypes = /\.(zip|exe|dmg|pdf|doc.*|xls.*|ppt.*|mp3|txt|rar|wma|mov|avi|wmv|flv|wav)$/i;
    var baseHref = '';
    if ($('base').attr('href') != undefined) baseHref = $('base').attr('href');
 
    $(document.body).on('click', 'a', function(event) {
		var el = $(this);
		var track = true;
		var href = (typeof(el.attr('href')) != 'undefined' ) ? el.attr('href') :"";
		var isThisDomain = href.match(document.domain.split('.').reverse()[1] + '.' + document.domain.split('.').reverse()[0]);
		if (!href.match(/^javascript:/i)) {
			var elEv = []; elEv.value=0;
			if (href.match(/^mailto\:/i)) {
				elEv.category = "email";
				elEv.action = "click";
				elEv.label = href.replace(/^mailto\:/i, '');
				elEv.loc = href;
			}
			else if (href.match(filetypes)) {
				var extension = (/[.]/.exec(href)) ? /[^.]+$/.exec(href) : undefined;
				elEv.category = "download";
				elEv.action = "click-" + extension[0];
				elEv.label = href.replace(/ /g,"-");
				elEv.loc = baseHref + href;
			}
			else if (href.match(/^https?\:/i) && !isThisDomain) {
				elEv.category = "external";
				elEv.action = "click";
				elEv.label = href.replace(/^https?\:\/\//i, '');
				elEv.loc = href;
			}
			else if (href.match(/^tel\:/i)) {
				elEv.category = "telephone";
				elEv.action = "click";
				elEv.label = href.replace(/^tel\:/i, '');
				elEv.loc = href;
			}
			else track = false;
 
			if(track){
				ga('send', 'event', elEv.category.toLowerCase(), elEv.action.toLowerCase(), elEv.label.toLowerCase(), elEv.value);
				if ( el.attr('target') == undefined || el.attr('target').toLowerCase() != '_blank') {
					setTimeout(function() { location.href = elEv.loc; }, 400);
					return false;
				}
			}
		}
    });
	
	// cross-browser suppor for HTML5 Placeholder tag
	if(!Modernizr.input.placeholder){
		$('[placeholder]').focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		  }
		}).blur();
		$('[placeholder]').parents('form').submit(function() {
		  $(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
			  input.val('');
			}
		  })
		});
	}

	// use Chosen for all selects
	$('select').chosen({
		disable_search_threshold: 10
	});
	
	// nivo slider
	$('#slider').nivoSlider({
		effect: 'sliceUpDown',
		pauseTime: 10000
	});
	
	// top nav menu fading
	$('#topNavPills').waypoint(function(direction){
		if(Modernizr.mq("screen and (min-width: 980px)")){
			if(direction == 'down'){
				$('#topLogo').stop(true).show().fadeTo(500, 1);
				$('#topBar .navbar .nav > li > a:not(.top-only)').stop(true).show().fadeTo(500, 1);
			}else{
				$('#topLogo').stop(true).fadeTo(500, 0);
				$('#topBar .navbar .nav > li > a:not(.top-only)').stop(true).fadeTo(500, 0, function(){
					if($('#topBar .navbar .nav > li > a:not(.top-only)').is(':visible')){
						$('#topLogo').hide();
						$('#topBar .navbar .nav > li > a:not(.top-only)').hide();
					}
				});
			}
		}else{
			$('#topLogo').stop(true).show().fadeTo(0, 1);
			$('#topBar .navbar .nav > li > a:not(.top-only)').stop(true).show().fadeTo(0, 1);
		}
	},{offset: 10});
	
	// Search button collapse menu
	$('#siteSearch a').click(function(){
		$('#topBar .navbar .collapse').collapse('hide');
	});	
		
	// Tooltips
	$('.has-tooltip').tooltip();
	
	// Toggle Switches
	$('.radio.btn-group label').on('click', function(){
		// remove all classes
		$(this).parent().children('label').removeClass('btn-danger btn-success');
		
		// apply a class to the clicked button
		var id = $(this).attr('for');
		if($('#' + id).val() == '0'){
			$(this).addClass('btn-danger');
		}else if($('#' + id).val() == '1'){
			$(this).addClass('btn-success');
		}else{
			$(this).addClass('btn-primary');
		}
	});
	
	// set the default toggle
	$('.radio.btn-group').each(function(index){
		var input = $(this).children(':checked');
		var id = input.attr('id');
		if(input.val() == '0'){
			$('label[for="' + id + '"]').addClass('btn-danger');
		}else{
			$('label[for="' + id + '"]').addClass('btn-success');
		}
	});

	/**
	 * Checks all checkboxes within the same div on click
	 */
	$('.checkAll').click(function(){
		$(this).closest('div').find(':checkbox').prop('checked', true);
	});

	/**
	 * Unchecks all checkboxes within the same fieldset on click
	 */
	$('.uncheckAll').click(function(){
		$(this).closest('div').find(':checkbox').removeAttr('checked');
	});
});

/**
 * Function to remove formatting from a phone number
 * 
 * @return string The phone number, digits only
 */
function unformatPhoneNumber(phone){
	// if the phone number starts with a one, but is not followed by an 8 or 9, remove the 1
	if(phone){
		// remove everything but digits, and return the result
		phone = phone.replace(/[^\d]/g, '');
		
		// remove a leading 1 if it is not for an 800 or 900 series number
		if(phone.charAt(0) == '1'
		&& (phone.charAt(1) != '8' 
		&& phone.charAt(1) != '9')){
			phone = phone.substring(1);
		}
		
		// return the result    	
		return phone;
	}
	return '';
}