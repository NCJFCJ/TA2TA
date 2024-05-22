<?php
/**
 * Title: Directory
 * Slug: twentytwentyfour/directory
 * Categories: query
 * Keywords: directory
 * Block Types: core/template-part/post-meta
 */


//$paged = (get_query_var('paged')) ? get_query_var('paged') : 0;

$postsPerPage = 5;
$postOffset = 0;

$args = array(

    'posts_per_page'  => 5,
    'offset'    => 0,
    'order'     => 'ASC'
);

$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
  
        /* grab the url for the full size featured image */
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
 
        /* link thumbnail to full size image for use with lightbox*/
        echo '<a href="'.esc_url($featured_img_url).'" rel="lightbox">'; 
            the_post_thumbnail('thumbnail');
        echo '</a>';

            echo '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
            

                the_content();
                
                //$fields = get_fields();

                if( have_rows('grant_projects_for_directory') ){

                     while( have_rows('grant_projects_for_directory' ) ) : the_row();

                        $project_archive = get_sub_field('archived');

                        if(!$project_archive) {

                            $project_title = get_sub_field('project_title');
                            $project_summary = get_sub_field('summary');
                            $grant_programs = get_sub_field('grant_programs');
                            $id = str_replace(' ', '_', $project_title);
                            
                            ?>
                                <div class="grant-project" id="<?php echo $id; ?>">
                                    <h5>Project Title</h5>

                                    <h3><?php echo esc_html_x($project_title, 'display the title of the project', 'twentytwentyfour');?></h3>

                                    <h5>
                                        Summary
                                    </h5>
                                    <p>
                                        <?php echo esc_html_x($project_summary, 'display the summary of the project', 'twentytwentyfour');?>
                                    </p>
                                    <h5>
                                        Contacts
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
                                                echo esc_html_x($phone_number, 'display the phone nnumber', 'twentytwentyfour');?><br />

                                                <?php }?>

                                                <?php if(!empty($email)){ ?>
                                                <a href="mailto:<?php echo esc_html_x($email, 'display the email', 'twentytwentyfour');?>"><?php echo esc_html_x($email, 'display the email', 'twentytwentyfour');?></a> <br />
                                                <?php }?>

                                            </li>
                                        <?php endwhile; ?>			
                                    </ul>
                                    
                                    <h5>
                                        Grant Programs Served
                                    </h5>
                                    <div class="columns-list-acf">
                                        <ul>
                                            <?php
                                            foreach($grant_programs as $value) { ?>
                                                <li>
                                                    <?php echo esc_html_x($value, 'display grant programs affilated with this project', "twentytwentyfour");?>
                                                </li>
                                                <?php
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php 
                        }
                        endwhile;
                    } else {
                        echo 'NONE FOUND';
                    }


        
    };
    echo paginate_links( array(
        'base' => '%_%',
        'format' => '?paged=%#%',
        'total' => $the_query->max_num_pages
        ) );
}

    else    {
        ?>
            <!-- wp:query-no-results -->
	        <!-- wp:pattern {"slug":"twentytwentyfour/hidden-no-results"} /-->
	        <!-- /wp:query-no-results -->
        <?php
    }
