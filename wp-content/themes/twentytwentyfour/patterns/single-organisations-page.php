<?php
/**
 * Title: Single Organization
 * Slug: twentytwentyfour/single-organisations-page
 * Categories: query
 * Keywords: organisation
 * Block Types: core/template-single
 */

// Get the Requested Organization slug

//if ( is_page() ){
    $slug = get_queried_object()->post_name;
//}

//Get the Organization Object
$organization_object = get_page_by_path( $slug, OBJECT, 'organizations' );

if ( $organization_object ) {
    $id = $organization_object->ID;
}

//Get all fields of this organization

$fields = get_fields($id);

$html = '';

if($fields){

    // var_dump($fields);

    $logo = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
    $link = get_post_meta( $id, 'external_link_for_directory', true );

    $html .='<div class="organization-logo">';

    /* link thumbnail to full size image for use with lightbox*/
    if ( $logo ) : 
        $html = $html . '<a class="text-center" href="'. $link .'" target="_blank" rel="lightbox">'; 
        $html = $html . '<img src="' . $logo . '" alt="Organization logo" />';
        $html = $html . '</a>';
       
    else:
        /* default link */
        $html = $html . '<span class="gr-bl-svg"><a href="'. $link .'" target="_blank" rel="lightbox">'; 
        $html = $html . '<?xml version="1.0" encoding="UTF-8"?><svg id="a" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 35.72 35.34"><defs><clipPath id="b"><path d="M6.23-2v31.11h31.11V-2H6.23ZM25.38,24.78l-6.22-6.22,7.63-7.63,6.22,6.22-7.63,7.63Z" style="fill:none; stroke-width:0px;"/></clipPath><clipPath id="c"><path d="M-2,6.23v31.11h31.11V6.23H-2ZM8.92,24.78l-6.22-6.22,7.63-7.63,6.22,6.22-7.63,7.63Z" style="fill:none; stroke-width:0px;"/></clipPath></defs><rect x="7.01" y="9.16" width="29.54" height="8.79" transform="translate(27.61 38.54) rotate(-135)" style="fill:#f06724; stroke-width:0px;"/><rect x="-1.22" y="17.39" width="29.54" height="8.79" transform="translate(7.73 46.77) rotate(-135)" style="fill:#049898; stroke-width:0px;"/><rect x="-.84" y="9.16" width="29.54" height="8.79" transform="translate(-5.5 13.82) rotate(-45)" style="fill:#989898; stroke-width:0px;"/><rect x="7.39" y="17.39" width="29.54" height="8.79" transform="translate(-8.91 22.05) rotate(-45)" style="fill:#652e8e; stroke-width:0px;"/><g style="clip-path:url(#b);"><rect x="7.01" y="9.16" width="29.54" height="8.79" transform="translate(27.61 38.54) rotate(-135)" style="fill:#f06724; stroke-width:0px;"/></g><g style="clip-path:url(#c);"><rect x="-1.22" y="17.39" width="29.54" height="8.79" transform="translate(7.73 46.77) rotate(-135)" style="fill:#049898; stroke-width:0px;"/></g></svg>';
        $html = $html . '</a></span>';
    endif;

    $html = $html . '</div>';

    $html_grant_projects = '<h5 class="title-gp"> Grant Projects </h5>';

    // Get the custom fields 
    if( $fields['grant_projects_for_directory'] ){



        foreach ($fields['grant_projects_for_directory'] as $field){

            if(!$field['archived']){

                $project_title = $field['project_title'];
                $project_summary = $field['summary'];
                $grant_programs = $field['grant_programs'];
                $id = str_replace(' ', '_', $project_title);

                $html_contacts='';

                if($field["contacts"]){
                    foreach($field["contacts"] as $contact){
                        $full_name = $contact['full_name'];
                        $job_title = $contact['title'];
                        $phone_number = $contact['phone_number'];
                        $email = $contact['email'];
                        $html_contacts = $html_contacts;
                            if(!empty($full_name)){
                                $html_contacts = $html_contacts .  esc_html_x($full_name, 'display the name', 'twentytwentyfour') . '<br />';
                                }

                            if(!empty($job_title)){
                                $html_contacts = $html_contacts .  esc_html_x($job_title, 'display the job title', 'twentytwentyfour') . '<br />';
                            }

                            if(!empty($phone_number)){
                                $html_contacts = $html_contacts .  esc_html_x($phone_number, 'display the phone nnumber', 'twentytwentyfour') . '<br />';
                            }

                            if(!empty($email)){
                                $html_contacts = $html_contacts .  '<a href="mailto:' . esc_html_x($email, 'display the email', 'twentytwentyfour'). '">' . esc_html_x($email, 'display the email', 'twentytwentyfour') . '</a> <br />';
                            }
                        $html_contacts = $html_contacts; 
                    }
                } else {
                    $html_contacts = $html_contacts . " No Contact Available";
                }
                $html_grant_programs='';
                if($grant_programs){
                    $html_grant_programs = '<ul>';
                    foreach($grant_programs as $grant_program){
                        $html_grant_programs = $html_grant_programs . '<li>' . $grant_program . '</li>';
                    }
                    $html_grant_programs = $html_grant_programs . '</ul>';
                }
                
                $html_awards = '';
                if($field["list_of_award_numbers"]){
                    foreach($field["list_of_award_numbers"] as $award){
                        $award_number = $award['award_number'];
                        $html_awards = $html_awards . '<br><ul><li>' . $award_number . '</ul><br><br>';
                    }
                } else {
                    $html_awards = $html_awards . '<br><ul><li>' . 'No Award Available' . '</li></ul><br>';
                }

                $html_grant_projects = $html_grant_projects . '
                    <div class="grant-project-s">
                        <div class="accordion-project" onclick="accordion_project_toggle_me(this)">
                            <strong>' . esc_html_x($project_title, "display the title of the project", "twentytwentyfour") . '</strong>
                            <span class="gr-p-svg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg>
                            </span>
                        </div>
                        <div class="panel" style="display: none;">
                        <h5>
                            <strong>Summary</strong>
                        </h5>
                        <p>
                            ' . esc_html_x($project_summary, "display the summary of the project" , "twentytwentyfour") . '
                        </p>
                        <h5>
                            <strong>Contacts</strong>
                        </h5>
                        
                        <ul class="contact-list"> ' . $html_contacts . '</ul>
                        <h5>
                            <strong>Awards</strong>
                            ' . $html_awards . '
                        </h5>
                        <h5>
                            <strong>Grant Programs Served</strong>
                        </h5>
                        <div class="columns-list-acf">
                            ' . $html_grant_programs . '
                        </div>
                    </div>';

            }
        }

    }
    echo $html . $html_grant_projects;
} else {
    echo '<div class="organization-nobody"> No Details available for this orgization at this time. <br><br> Contact TA2TA </div>';
}

