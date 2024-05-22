<?php
/**
 * Title: Directory Post Meta
 * Slug: twentytwentyfour/directory-post-meta
 * Categories: query
 * Keywords: directory post meta
 * Block Types: core/template-part/post-meta
 */


// get or make permalink
$url = !empty(get_the_permalink()) ? get_the_permalink() : (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$permalink = strtok($url, '?');

// get post_id using url/permalink
$post_id = url_to_postid($url);

?>
<div class="wp-block-group fullWidth has-text-align-center">
    <a class="has-text-align-center" href="<?php echo esc_html_x( get_post_meta($post_id, 'external_link_for_directory', true), 'external link to organization', 'twentytwentyfour' ); ?>" target="_blank"><?php echo esc_html_x( get_post_meta($post_id, 'external_link_for_directory', true), 'external link to organization', 'twentytwentyfour' ); ?></a>

    <p>
        <?php echo esc_html_x( the_content($post_id), 'display excerpt of post', 'twentytwentyfour' ); ?>
    </p>
</div>
<?php
// Check rows existexists.
if( have_rows('grant_projects_for_directory', $post_id) ):
?>
<div class="carousel-container">

    <?php while( have_rows('grant_projects_for_directory', $post_id) ) : the_row();


    $project_archive = get_sub_field('archived');

    if(!$project_archive) {

    $project_title = get_sub_field('project_title');
    $project_summary = get_sub_field('summary');
    $grant_programs = get_sub_field('grant_programs');
    $id = str_replace(' ', '_', $project_title);
    ?>

    <div class="carousel-item fadeTransition wp-block-group carousel-block" id="<?php echo $id?>">
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

        	<? endwhile; ?>			
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

                }?>
            </ul>
        </div>
    </div>

    <?php endwhile;?>
    <?php
    else :
    // Do something...
        ?>
        <div class="wp-block-group fullWidth has-text-align-center">
            <div class="fill-height-gap">
                <div class="full-height-gap-inner">
                    <?php 
                    echo esc_html_x("No grant project available.", 'display empty message', 'twentytwentyfour');
                    ?>
                </div>
            </div>
        </div>
        <?php
    endif;

    ?>
</div>
<?php



