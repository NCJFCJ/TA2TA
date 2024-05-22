jQuery(document).ready(function($) {

        var grant_project_field = $( '#acf-field_639510b049fb0' );
        var award_field = $( '#acf-field_639bae8a346b4' );
        var grant_program_field = $( '#acf-field_65dcd84e0ad20' );

        var selected_grant_project,selected_award;
        if(grant_project_field.val() === 'None'){
            $('.acf-field.acf-field-select.acf-field-639bae8a346b4').hide();
            $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
        }
        if(award_field.val() === 'None'){
            grant_program_field.empty();
            $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
        }
        grant_project_field.change( function(){
            selected_grant_project = $(this).val();
            $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
            if(selected_grant_project == 'None'){
                award_field.empty();
                //grant_program_field.empty();
                $('.acf-field.acf-field-select.acf-field-639bae8a346b4').hide();
                $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
            } else {
                $('.acf-field.acf-field-select.acf-field-639bae8a346b4').show();
                awards = acf.data.ta2ta_org_data[selected_grant_project];
                award_field.html( $('<option></option>').val('None').html('Select Award').attr({ selected: 'selected'}) )
                award_field.append( $('<option></option>').val(awards).html(awards) )
                
            }
            console.log('GRant --- Project --- Act :' + selected_grant_project );
        });

        award_field.change(function(){
            //selected_grant_project = grant_project_field.text();
            selected_award = $(this).val();
            if(selected_award !== 'None'){
                $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').show();
                grant_program_field = acf.getField('field_65dcd84e0ad20');
                grant_programs_choices = acf.data.ta2ta_org_data[selected_award];
                console.log("AWARDS =================== :");
                //console.log(awards);
                console.log("Selected Award =================== : ");
                console.log(selected_award);
                grant_program_field.choices = grant_programs_choices;
                var html_loop = '';
                grant_programs_choices.forEach(element => {
                    html_loop = html_loop + '<li>'+
                        '<label>'+
                            '<input type="checkbox" id="acf-field-65dcd84e0ad20-' + element.replace(/ /g, "-")+ '" name="acf[field-65dcd84e0ad20][]" value="' + element+ '">'+ 
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
                console.log(grant_program_field.$el);
                grant_program_field.$el[0].innerHTML = html;

                console.log("Grant Programs =================== : ");
                console.log(grant_programs_choices);
                if ($("input[type=checkbox]").prop(
                    ":checked")) {
                    console.log("Check box in Checked");
                }
                
            } else {
                console.log(selected_award);
                $('.acf-field.acf-field-checkbox.acf-field-65dcd84e0ad20').hide();
            }
        });



        var myModel = new acf.Model({
        
            $el: $('#acf-field-65dcd84e0ad20'),
        
            initialize: function(){
        
                // find within $el
                // equivalent to $('.my-wrapper').find('.my-element')
                var search = this.$('.acf-input');
        
                // show element
                // equivalent to $('.my-wrapper').addClass('acf-hidden')
                this.show();
        
                // hide element
                // equivalent to $('.my-wrapper').removeClass('acf-hidden')
                this.hide();
                
                // add event
                // equivalent to $('.my-wrapper').on('click', 'a')
                this.on('click', 'a', function(){
                    console.log('Link clicked')
                });
        
            }
        
        });
});

