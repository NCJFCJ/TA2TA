<?php

/**
 * Title: Newsletter Content
 * Slug: twentytwentyfour/newsletter-content
 * Keywords: newsletter
 * Categories: newsletter, newsletter content
 * Post Types: newsletter, wp_template
 */

 $slug = get_queried_object()->post_name;

//Get the Newsletter Object
$newsletter_c = get_page_by_path( $slug, OBJECT, 'newsletter' );
$id = 0;
$html ='';
if ( $newsletter_c ) {
    $id = $newsletter_c->ID;
}

//Get all fields of this newsletter

$newsletter_file = get_field('newsletter_file',$id);
$newsletter_link = get_field('newsletter_link',$id);
// The rendering the content of the newsletter
?>
<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"padding":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50)">

<?php 

if( $newsletter_link ){
    ?>
        <!-- wp:file {"id":<?php echo $newsletter_file['ID'];?>,"href":"<?php echo esc_url($newsletter_file['url'])?>","displayPreview":true} -->
        <div class="wp-block-file newsletter-content">
            <div class="newsletter-content-icon">
                <iframe height="1400" width="1200" style="border:none" src="<?php echo esc_url($newsletter_link)?>" title="<?php echo get_the_title(); ?>"></iframe>
            </div>
            <div class="buttons-newsletter-p">
                <a class="button" id="wp-block-file--media-<?php echo $newsletter_file['ID'];?>" href="<?php echo esc_url($newsletter_link)?>" target="_blank">
                    <?php echo get_the_title(); ;?>
                </a>
            </div>
        </div>
        <!-- /wp:file -->
    <?php
} else {
    ?>
    <!-- wp:file {"id":<?php echo $newsletter_file['ID'];?>,"href":"<?php echo esc_url($newsletter_file['url'])?>","displayPreview":true} -->
        <div class="wp-block-file newsletter-content">
            <div class="newsletter-content-pdf">
                <object class="wp-block-file__embed" data="<?php echo $newsletter_file['url']?>" type="application/pdf" style="width:100%;height:900px" aria-label="newsletter-content"></object>
            </div>
            <div class="buttons-newsletter-p">
                <a class="button" id="wp-block-file--media-<?php echo $newsletter_file['ID'];?>" href="<?php echo esc_url($newsletter_link)?>">
                    <?php echo $newsletter_file['title'] ;?>
                </a>
                <a href="<?php echo esc_url($newsletter_file['url'])?>" class="button" download aria-describedby="wp-block-file--media-<?php echo $newsletter_file['ID'];?>">
                    Download
                </a>
            </div>
        </div>
    <!-- /wp:file -->
    <?php
}

?>

</div>
<!-- /wp:group -->