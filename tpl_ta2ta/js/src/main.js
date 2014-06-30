/* ------ Establish Namespace ------- */

var ta2ta = {}

/* ----- Frameworks and Plugins ----- */

//@codekit-prepend "../../plugins/jquery/jquery-1.11.1.js"
//@codekit-append "../../plugins/modernizr/modernizr.js"
//@codekit-append "../../plugins/bootstrap/js/transition.js"
//@codekit-append "../../plugins/bootstrap/js/alert.js"
//@codekit-append "../../plugins/bootstrap/js/collapse.js"
//@codekit-append "../../plugins/bootstrap/js/dropdown.js"
//@codekit-append "../../plugins/bootstrap/js/modal.js"
//@codekit-append "../../plugins/bootstrap/js/tooltip.js"
//@codekit-append "../../plugins/bootstrap/js/popover.js"
//@codekit-append "../../plugins/jquery/imgareaselect/jquery.imgareaselect.js"
//@codekit-append "../../plugins/jquery/jqueryfileupload/vendor/jquery.ui.widget.js"
//@codekit-append "../../plugins/jquery/jqueryfileupload/jquery.fileupload.js"
//@codekit-append "../../plugins/jquery/jqueryfileupload/jquery.iframe-transport.js"
//@codekit-append "../../plugins/jquery/nivo/jquery.nivo.slider.js"
//@codekit-append "../../plugins/jquery/waypoints/waypoints.js"
//@codekit-append "../../plugins/jwplayer/jwplayer.js"
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

	// fix crazy disappearing tooltip parents
	$("[data-toggle='tooltip']").on('hide', function(e){
		e.stopPropagation();
	});

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

	/* --- JW PLayer --- */

	// construct the necessary URLs
	var baseURL = window.location.protocol + '//' + window.location.host;
	var jwPlayerURL = baseURL + '/templates/ta2ta/plugins/jwplayer/';

	// replace all link tags with the video player
	$('a.video-player').each(function(){
		// grab the video settings from the link
		var settingString = $(this).attr("href");

		// settings array [0] = video file, [1] = caption, [2] = high def, [3] = start
		var settings = settingString.split(':');

		// make sure we have at least a video before proceeding
		if(settings[0]){
			// replace the link with a DIV which will hold the video
			$(this).replaceWith('<div class="video-wrapper"><div id="video"></div></div>');

			// determine whether we are auto starting, default false
			var start = false;
			if(settings[3]){
				start = settings[3];
			}

			// configure JWPlayer
			var options = {
				autostart: start,
			  	file: baseURL + settings[0],
			  	flashplayer: jwPlayerURL + 'player.swf',
			  	height: '100%',
			  	plugins: {
			  		'gapro-2':{
					  	'trackstarts': true,
					  	'trackpercentage': true,
					  	'trackseconds': true		  	
			  		}
			  	},
			  	skin: jwPlayerURL + 'skins/ta2ta/ta2ta.xml',
			  	stretching: 'fill',
			  	width: '100%'
			};
			
			// append captions settings if present
			if(settings[1]){
				if(settings[1].toLowerCase() == 'embedded'){
					options.plugins['captions-2'] = {state: 'false', back: 'true'};
				}else{
					options.plugins['captions-2'] = {file: baseURL + settings[1], state: 'false', back: 'true'};
				}
			}
			
			// append high def settings if present
			if(settings[2]){
				options.plugins['hd-2'] = {file: baseURL + settings[2]};
			}
			
			// build the JWPlayer
			jwplayer('video').setup(options);
		}
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