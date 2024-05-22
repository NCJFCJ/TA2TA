jQuery(document).ready(function($) {

    /**
     * @variable awards parameter to hold the awards choices, grant_programs parameter to hold the grant programs choices for fields
     */
    var award, grant_programs;
    // var grant_project = $('#acf-field_639510b049fb0').find(':selected').val();
    // if( grant_project == 'Select Grant Project'){
    //     $( '#acf-field_639510b049fb0 option:selected' ).each(function() {
    //         if($( this ).text() == 'Select Grant Project'){
    //             $( this ).html('Select Grant Project').attr({ selected: 'selected', disabled: 'disabled'});
    //             $('.acf-field.acf-field-select.acf-field-639bae8a346b4').hide();
    //             $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
                
    //         }
    //     });
    //     $( '#acf-field_65e7b896d210c option:selected' ).each(function() {
    //         if($( this ).text() == 'Select Award'){
    //             $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
    //         }
    //     });
    // } else {
    //     $('.acf-field.acf-field-select.acf-field-639bae8a346b4').show();
    //     $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').show()
    // }
    //console.log(matrice);

    var selected_grant_project = '';
    // Get selected value
    $( '#acf-field_639510b049fb0 option:selected' ).each(function() {
        selected_grant_project += $( this ).text();
    });
    console.log(selected_grant_project);
    if(selected_grant_project != 'Select Grant Project'){
        award = $( '#acf-field_639bae8a346b4 option:selected' ).text();
        $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').show();
        //console.log(grant_project_grant_programs[award]);
        console.log('================');
        console.log(selected_grant_project_grant_programs);
        console.log( grant_projects_organization[selected_grant_project][award]);
        //grant_programs = grant_project_grant_programs[award];
        grant_programs = grant_projects_organization[selected_grant_project][award];
        var field = acf.getField('field_65dcd84e0ad20');
        var html_loop = '';
        var selected = '';
        var checked = '';
        grant_programs.forEach( element => {
            if(selected_grant_project_grant_programs.includes(element)){
                selected = 'selected'; checked = "checked"; 
            } else { selected = ''; checked = ''; }
           console.log( 'Selected : ' + selected ); console.log( 'Checked : ' + selected );
            html_loop = html_loop + '<li>'+
            '<label class="'+ selected + '">'+
                '<input type="checkbox" id="acf-field-65dcd84e0ad20-' + element + '" name="acf[field-65dcd84e0ad20][]" value="' + element + '" ' + checked + '>'+ 
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
        console.log('ONE STEP');

    } else {
        //$('.acf-field.acf-field-select.acf-field-639bae8a346b4').hide();
        console.log(grant_projects_organization);
        $('.acf-field.acf-field-select.acf-field-639bae8a346b4').hide();
        $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
        var grant_project_field = $('#acf-field_639510b049fb0');
        var award_field = $('#acf-field_639bae8a346b4');
        var grant_program_field = $('#acf-field-65dcd84e0ad20');
        console.log("STEP TWO")
        //console.log(grant_project_field);

        // for (var x in grant_projects_organization) {
        //     grant_project_field.options[grant_project_field.options.length] = new Option(x, x);
        //   }
        grant_project_field.change( function() {
            if(this.value != 'Select Grant Project'){
                $('.acf-field.acf-field-select.acf-field-639bae8a346b4').show();
            }
            //empty Chapters- and Topics- dropdowns
            grant_program_field.length = 1;
            award_field.length = 1;
            //display correct values
            console.log(this.value);
            console.log(grant_projects_organization[this.value]);
            i = 0;
            for (var y in grant_projects_organization[this.value]) {
                console.log("JHFK========================jdj");
                console.log("length : " + award_field.length );
                console.log(y);
                //award_field.options[award_field.options.length] = new Option(y, y);
                console.log($('#acf-field_639bae8a346b4').options);
            }
        })
        
        award_field.change(function() {
            if(this.value != 'Select Award'){
                $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').show();
            }
            //empty Chapters dropdown
            grant_program_field.length = 1;
            award = grant_program_field
            //display correct values
            console.log(this.value);
            console.log(grant_program_field);
            var z = grant_projects_organization[grant_project_field.val()][this.value];
            console.log(z);
            for (var i = 0; i < z.length; i++) {
                grant_program_field.options[grant_program_field.options.length] = new Option(z[i], z[i]);
            }
            console.log(grant_program_field.options);
            z.forEach(element => {
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

                grant_program_field.$el[0].innerHTML = html; 
            });


        /**
         * Get Grant projects options
         */
        // $( '#acf-field_639510b049fb0' ).change(function () {
        //     // Get selected value
        //     $( '#acf-field_639510b049fb0 option:selected' ).each(function() {
        //         selected_grant_project = $( this ).text();
        //         console.log(selected_grant_project);
        //     });
        //     if( selected_grant_project != 'Select Grant Project' ) {
        //         var awards = grant_projects_organization.keys();
        //         console.log(awards);
        //     }

        //     $('.acf-field.acf-field-select.acf-field-639bae8a346b4').show();
        //     $( '#acf-field_639bae8a346b4' ).attr( 'disabled', 'disabled' );

        //     // If default is not selected get award for selected grant project
        //     if( selected_grant_project != 'Select Grant Project' ) {
        //         // Send AJAX request
        //         data = {
        //             action: 'awards_by_grant_project',
        //             pa_nonce: pa_vars.pa_nonce,
        //             grant_project: selected_grant_project,
        //         };
        //         // Get response and populate area select field
        //         $.post( ajaxurl, data, function(response) {
        //             if( response ){
        //                 // Disable 'Select Award' field until grant project is selected
        //                 $( '#acf-field_639bae8a346b4' ).html( $('<option></option>').val('0').html('Select Award').attr({ selected: 'selected', disabled: 'disabled'}) );
        //                 // Hide Grant programs field if it was shown
        //                 //============REMEMBER THIS condition in ACF Ui $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
        //                 // Add awards to select field options
        //                 //console.log(response);
        //                 awards = response.awards;
        //                 grant_programs = response.grant_programs;
        //                 $.each(awards, function(val, text) {
        //                     $( '#acf-field_639bae8a346b4' ).append( $('<option></option>').val(text).html(text) );
        //                 });
        //                 // Enable 'Select Award' field
        //                 $( '#acf-field_639bae8a346b4' ).removeAttr( 'disabled' );
        //             };
        //         });
        //     }
        //});

        // $( '#acf-field_639bae8a346b4' ).change(function () {

        //     //$( '.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20' ).on('show', function(){console.log('HERE');});

        //     $( '.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20' ).show();
        //     // Still want to find a way to use acf js to update field choices
        //     //acf.addAction('load_field/key=field-65dcd84e0ad20', function(){
        //         //console.log('Change the choices plus for grant programs');
        //         var field = acf.getField('field_65dcd84e0ad20');
        //         field.choices = grant_programs;   
        //         var html_loop = '';
        //         grant_programs.forEach(element => {
        //             html_loop = html_loop + '<li>'+
        //                 '<label>'+
        //                     '<input type="checkbox" id="acf-field-65dcd84e0ad20-' + element+ '" name="acf[field-65dcd84e0ad20][]" value="' + element+ '">'+ 
        //                     element + 
        //                 '</label>'+ 
        //             '</li>'
        //         });
        //         html = 
        //         '<div class="acf-label">'+
        //             '<label for="acf-field-65dcd84e0ad20">Grant Program(s)</label>'+
        //         '</div>'+
        //         '<div class="acf-input">'+
        //             '<input type="hidden" name="acf[field-65dcd84e0ad20]">'+
        //             '<ul class="acf-checkbox-list acf-bl">'+ 
        //                 html_loop+
        //             '</ul>'+
        //         '</div>';

        //         field.$el[0].innerHTML = html; 
        //     });
        //});
    }

});
//});
