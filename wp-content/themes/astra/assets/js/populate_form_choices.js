jQuery(document).ready(function($) {
//     if ( typeof acf !== 'undefined' ) {
//         console.log( 'ACF is defined', acf );
//     }

    /**
     * @variable awards parameter to hold the awards choices, grant_programs parameter to hold the grant programs choices for fields
     */
    var awards, grant_programs;
    //acf.addAction('ready', function(){
        // Add a default desable option 
        //$( '#acf-field_639510b049fb0' ).prepend( $('<option></option>').val('0').html('Select Grant Project').attr({ selected: 'selected', disabled: 'disabled'}) );

        $( '#acf-field_639510b049fb0 option:selected' ).each(function() {
            if($( this ).text() == 'Select Grant Project'){
                $( this ).html('Select Grant Project').attr({ selected: 'selected', disabled: 'disabled'});
                $('.acf-field.acf-field-select.acf-field-639bae8a346b4').hide();
                $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
                
            }
        });
        $( '#acf-field_65e7b896d210c option:selected' ).each(function() {
            if($( this ).text() == 'Select Award'){
                $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
            }
        });
      //   var subjectObject = pa_vars.choices;
      //   console.log('bg');
      //   var subjectSel = $( '#acf-field_639510b049fb0' ); //document.getElementById("subject");
      //   console.log(subjectSel.choices = '');
      //   var topicSel = $( '#acf-field_65e7b896d210c' );//document.getElementById("topic");
      //   var chapterSel = $( '#acf-field_639bae8a346b4' );//document.getElementById("chapter");

      //   for (var x in subjectObject) {
      //     //subjectSel.options[subjectSel.options.length] = new Option(x, x);
      //     console.log(x)
      //     subjectSel.append( $('<option></option>').val(x).html(x) )
      //   }
      //   subjectSel.onchange = function() {
      //     //empty Chapters- and Topics- dropdowns
      //     chapterSel.length = 1;
      //     topicSel.length = 1;
      //     //display correct values
      //     for (var y in subjectObject[this.value]) {
      //       topicSel.append( $('<option></option>').val(y).html(y) );
      //       console.log(topicSel);
      //     }
      //   }
      //   topicSel.onchange = function() {
      //     //empty Chapters dropdown
      //     chapterSel.length = 1;
      //     //display correct values
      //     var z = subjectObject[subjectSel.value][this.value];
      //     for (var i = 0; i < z.length; i++) {
      //       chapterSel.append( $('<option></option>').val(z[i]).html(z[i]) );
      //       console.log(chapterSel);
      //     }
      //   }



    /**
     * Get Grant projects options
     */
    $( '#acf-field_639510b049fb0' ).change(function () {

        var selected_grant_project = ''; // Selected value

        // Get selected value
        $( '#acf-field_639510b049fb0 option:selected' ).each(function() {
            selected_grant_project += $( this ).text();
        });

        $('.acf-field.acf-field-select.acf-field-639bae8a346b4').show();
        $( '#acf-field_639bae8a346b4' ).attr( 'disabled', 'disabled' );

        // If default is not selected get award for selected grant project
        if( selected_grant_project != 'Select Grant Project' ) {
            // Send AJAX request
            data = {
                action: 'awards_by_grant_project',
                pa_nonce: pa_vars.pa_nonce,
                grant_project: selected_grant_project,
            };
            // Get response and populate area select field
            $.post( ajaxurl, data, function(response) {
                if( response ){
                    // Disable 'Select Award' field until grant project is selected
                    $( '#acf-field_639bae8a346b4' ).html( $('<option></option>').val('0').html('Select Award').attr({ selected: 'selected', disabled: 'disabled'}) );
                    // Hide Grant programs field if it was shown
                    $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
                    // Add awards to select field options
                    //console.log(response);
                    awards = response.awards;
                    grant_programs = response.grant_programs;
                    $.each(awards, function(val, text) {
                        $( '#acf-field_639bae8a346b4' ).append( $('<option></option>').val(text).html(text) );
                    });
                    // Enable 'Select Award' field
                    $( '#acf-field_639bae8a346b4' ).removeAttr( 'disabled' );
                };
            });
        }
    });

    $( '#acf-field_639bae8a346b4' ).change(function () {

        //$( '.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20' ).on('show', function(){console.log('HERE');});

        $( '.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20' ).show();
        // Still want to find a way to use acf js to update field choices
        //acf.addAction('load_field/key=field-65dcd84e0ad20', function(){
            //console.log('Change the choices plus for grant programs');
            var field = acf.getField('field_65dcd84e0ad20');
            field.choices = grant_programs;   
            var html_loop = '';
            grant_programs.forEach(element => {
                html_loop = html_loop + '<li>'+
                    '<label>'+
                        '<input type="checkbox" id="acf-field-65dcd84e0ad20-' + element+ '" name="acf[field-65dcd84e0ad20][]" value="' + element+ '">'+ 
                        element + 
                    '</label>'+ 
                '</li>'
            });
            html = 
            '<div class="acf-label">'+
                '<label for="acf-field-65dcd84e0ad20">Grant Program(s)</label>'+
            '</div>'+
            '<div class="acf-input">'+
                '<input type="hidden" name="acf[field-65dcd84e0ad20]">'+
                '<ul class="acf-checkbox-list acf-bl">'+ 
                    html_loop+
                '</ul>'+
            '</div>';

            field.$el[0].innerHTML = html; 
        });
    //});

});
//});

function reload(eltId){
    var container = document.getElementById(eltId);
    var content = container.innerHTML;
    container.innerHTML= content; 
    //this line is to watch the result in console , you can remove it later	
    //console.log("Refreshed"); 
}
