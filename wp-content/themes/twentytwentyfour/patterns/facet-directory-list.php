<?php

/**
 * Title: Facet Directory List
 * Slug: twentytwentyfour/facet-directory-list
 * Categories: query
 * Keywords: Organizations, list
 * Block Types: core/template-part/post
 */

echo '<div class="grid-container">';

if ( have_posts() ) : 
    while ( have_posts() ): the_post();
        echo '<div class="grid-item" id="org_details_'.get_the_ID().'">'; // grid item start
        echo '<div class="close-show-button" onclick="hide_org_details('.get_the_ID().')"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></div>';
        echo '<h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
        /* grab the url for the full size featured image */
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'medium'); 
 
        /* link thumbnail to full size image for use with lightbox*/
        if ( has_post_thumbnail() ) : 
            echo '<a href="'. esc_html_x( get_post_meta(get_the_ID(), 'external_link_for_directory', true), 'external link to organization', 'twentytwentyfour' ).'" target="_blank" rel="lightbox">'; 
                the_post_thumbnail('full');
            echo '</a>';
        else:
            /* default link */
            echo '<span class="gr-bl-svg"><a href="'. esc_html_x( get_post_meta(get_the_ID(), 'external_link_for_directory', true), 'external link to organization', 'twentytwentyfour' ).'" target="_blank" rel="lightbox">'; 
                echo '<?xml version="1.0" encoding="UTF-8"?><svg id="a" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 35.72 35.34"><defs><clipPath id="b"><path d="M6.23-2v31.11h31.11V-2H6.23ZM25.38,24.78l-6.22-6.22,7.63-7.63,6.22,6.22-7.63,7.63Z" style="fill:none; stroke-width:0px;"/></clipPath><clipPath id="c"><path d="M-2,6.23v31.11h31.11V6.23H-2ZM8.92,24.78l-6.22-6.22,7.63-7.63,6.22,6.22-7.63,7.63Z" style="fill:none; stroke-width:0px;"/></clipPath></defs><rect x="7.01" y="9.16" width="29.54" height="8.79" transform="translate(27.61 38.54) rotate(-135)" style="fill:#f06724; stroke-width:0px;"/><rect x="-1.22" y="17.39" width="29.54" height="8.79" transform="translate(7.73 46.77) rotate(-135)" style="fill:#049898; stroke-width:0px;"/><rect x="-.84" y="9.16" width="29.54" height="8.79" transform="translate(-5.5 13.82) rotate(-45)" style="fill:#989898; stroke-width:0px;"/><rect x="7.39" y="17.39" width="29.54" height="8.79" transform="translate(-8.91 22.05) rotate(-45)" style="fill:#652e8e; stroke-width:0px;"/><g style="clip-path:url(#b);"><rect x="7.01" y="9.16" width="29.54" height="8.79" transform="translate(27.61 38.54) rotate(-135)" style="fill:#f06724; stroke-width:0px;"/></g><g style="clip-path:url(#c);"><rect x="-1.22" y="17.39" width="29.54" height="8.79" transform="translate(7.73 46.77) rotate(-135)" style="fill:#049898; stroke-width:0px;"/></g></svg>';
            echo '</a></span>';
        endif;
       /* the content of projects organization get a new background over the overlay class="gr-project-content" */
        echo '<div class="gr-project-content"><div class= "hide-excerpt" >';
            the_excerpt();
        echo '</div>';

        echo '<div class= "hide-content">';
            the_content();
            echo'<h3 class="text-center">Projects</h3>';
        echo '</div>';
       
       // Get the custom fields 
       if( have_rows('grant_projects_for_directory') ){

           while( have_rows('grant_projects_for_directory' ) ) : the_row();

           $project_archive = get_sub_field('archived');

           if(!$project_archive) {

               $project_title = get_sub_field('project_title');
               $project_summary = get_sub_field('summary');
               $grant_programs_directory = get_sub_field('grant_programs');
               $id = str_replace(' ', '_', $project_title);
               
               ?>
                   <div class="grant-project">
                        <div class="accordion-project" onclick=" accordion_project_toggle_me(this)">
                            <strong><?php echo esc_html_x($project_title, 'display the title of the project', 'twentytwentyfour');?></strong>
                            <span class="gr-p-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></span>
                        </div>
                        <div class="panel">
                        <h5>
                            <strong>Summary</strong>
                        </h5>
                        <p>
                            <?php echo esc_html_x($project_summary, 'display the summary of the project', 'twentytwentyfour');?>
                        </p>
                        <h5>
                            <strong>Contacts</strong>
                        </h5>
                        
                        <ul class="contact-list">			
                            <?php while( have_rows('contacts') ) : the_row();
                                // Load sub field value.
                                $full_name = get_sub_field('full_name');
                                $job_title = get_sub_field('title');
                                $phone_number = get_sub_field('phone_number');
                                $email = get_sub_field('email');
                                ?>
                                <li>
                                    <?php if(!empty($full_name)){
                                    echo esc_html_x($full_name, 'display the name', 'twentytwentyfour');?><br />
                                    <?php }?>

                                    <?php if(!empty($job_title)){
                                    echo esc_html_x($job_title, 'display the job title', 'twentytwentyfour');?><br />
                                    <?php }?>

                                    <?php if(!empty($phone_number)){
                                    echo esc_html_x($phone_number, 'display the phone number', 'twentytwentyfour');?><br />

                                    <?php }?>

                                    <?php if(!empty($email)){ ?>
                                    <a href="mailto:<?php echo esc_html_x($email, 'display the email', 'twentytwentyfour');?>"><?php echo esc_html_x($email, 'display the email', 'twentytwentyfour');?></a> <br />
                                    <?php }?>

                                </li>
                            <?php endwhile; ?>			
                        </ul>
                        
                        <h5>
                            <strong>Grant Programs Served</strong>
                        </h5>
                        <div class="columns-list-acf">
                            <ul>
                                <?php
                                foreach($grant_programs_directory as $value) { ?>
                                    <li>
                                        <?php echo esc_html_x($value, 'display grant programs affilated with this project', "twentytwentyfour");?>
                                    </li>
                                    <?php
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                    
            </div>
                    
               <?php 
           }
           endwhile;
       } else {
           ?>
               <div class="grant-project">
                   <h3>
                       No Details found for this organization
                   </h3>
               </div>
           <?php
       }




        echo '
                <div class="buttons" onclick="show_org_details('.get_the_ID().')">
                    <a href="javascript:void(0)" class="button">
                        Preview
                    </a>
                </div>
            </div>
            </div>
                
                
                '; //end grid item
    endwhile;
else : 
    echo "<p> Sorry, no posts matched your criteria.</p>";
endif;

wp_reset_postdata();

echo '</div>';