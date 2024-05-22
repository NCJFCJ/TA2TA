<?php
/**
 * Title: Orientations Single
 * Slug: twentytwentyfour/orientations-single
 * Categories: query
 * Keywords: Orientations
 * Block Types: core/template-single
 */
?>
<?php

    $permalink_explode = explode('/', "$_SERVER[REQUEST_URI]");
	$slug = $permalink_explode[2];
    // Get list of all taxonomy - orientation-categories terms
    $args = array(
        'taxonomy' => 'orientations-categories',
        'orderby' => 'name',
        'order'   => 'ASC'
    );
    $cats = get_categories($args);

?>

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group orientations-container">
    <div class="o-tabs-container">
        <ul class="o-tabs-list" role="tablist">
            <li class="o-tab-list-item" role="none">
                <a href="#" class="o-link active" data-tab="0" role="tab" onclick="opentab(event, 'ngo-recording')">
                    NGO Recordings
                </a>
            </li>
            <li class="o-tab-list-item" role="none">
                <a href="#resources" class="o-link" data-tab="1" role="tab" onclick="opentab(event, 'resources')">
                    Resources
                </a>
            </li>
        </ul>
    <div id="ngo-recording" class="o-tab">
    <?php
    
        // For every Terms of custom taxonomy get their posts by term_id
        foreach($cats as $cat) {
            if( $cat->slug == $slug ){
        ?>
        <a href="<?php echo get_category_link( $cat->term_id ) ?>">
            <?php echo $cat->name; ?> <br>
            <?php // echo $cat->term_id; ?> <br>
        </a>
        

            <?php
                // Query Arguments
                $args = array(
                    'post_type' => 'orientations', // the post type
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'orientations-categories', // the custom vocabulary
                            'field'    => 'term_id',          // term_id, slug or name  (Define by what you want to search the below term)    
                            'terms'    => $cat->term_id,      // provide the term slugs
                        ),
                    ),
                );

                // The query
                $the_query = new WP_Query( $args );

                // The Loop
                if ( $the_query->have_posts() ) {
                

                    echo '<ul>';
                    $html_list_items = '';
                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $html_list_items .= '<li>';
                        $html_list_items .= '<a href="' . get_permalink() . '">';
                        $html_list_items .= get_the_title();
                        $html_list_items .= '</a>';
                        $html_list_items .= '</li>';
                        //the_content();
                        $id = get_the_ID();
                        $fields = get_fields();
                        $image_url = str_replace('.pdf','-pdf.jpg', $fields['ppt_english']['url']);
                        ?>

                        
                <div class="orientation">
                    <div class="orientation-recording">
                        <div class="ngo-recording">
                            <!-- wp:group {"layout":{"type":"constrained"}} -->
                            <div class="wp-block-group">
                                <!-- wp:embed {"url":"<?php echo $fields['ngo_recording'];?>","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-4-3 wp-has-aspect-ratio"} -->
                                <figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-4-3 wp-has-aspect-ratio">
                                <div class="wp-block-embed__wrapper">
                                    <?php echo $fields['ngo_recording'];?>
                                </div>
                                <!-- /wp:embed -->
                            </div>
                            <!-- /wp:group -->
                            <?php if(! empty($fields['ppt_english'])): ?>
                            <div class="wp-block-buttons is-layout-flex wp-container-15 wp-block-buttons-is-layout-flex" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)">
                                <div class="wp-block-button has-custom-width wp-block-button__width-100">
                                    <a class="wp-block-button__link wp-element-button" href="<?php echo $fields['ppt_english']['url']; ?>">
                                    <img decoding="async" class="image-document" src="<?php //echo $image_url ?>" style="width:175px;height:300px;border-radius:3px;"><br>PPT English
                                    </a>
                                </div>
                                <?php if($fields['ppt_spanish_korean']): ?>
                                <div class="wp-block-button has-custom-width wp-block-button__width-100">
                                    <a class="wp-block-button__link wp-element-button" href="<?php echo $fields['ppt_spanish_korean']['url']; ?>">
                                        PTT Spanish &amp; Korean
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if($fields['ppt_other']): ?>
                                <div class="wp-block-button has-custom-width wp-block-button__width-100">
                                    <a class="wp-block-button__link wp-element-button" href="<?php echo $fields['ppt_other']['url']; ?>">
                                        Other
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="orientation-content">
                            <!-- wp:post-content /-->
                            <div class="orientations-date">
                                <?php echo $fields['orientation_date'];?>
                            </div>
                            <div class="translated-links">
                                <?php if($fields['ngo_recording_spanish'] != ''): ?>
                                <div class="spanish-recording">
                                    <a href="<?php echo $fields['ngo_recording_spanish'];?>"class="o-link-lan">
                                        View in Spanish
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if($fields['ngo_recording_korean'] != ''): ?>
                                <div class="other-recording">
                                    <a href="<?php echo $fields['ngo_recording_korean'];?>" class="o-link-lan">
                                        View in Korean
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>











                        <?php

                        
                    }
                    echo $html_list_items;
                    echo '</ul>';

                } else {
                    // no posts found
                }

                wp_reset_postdata(); // reset global $post;
            }
        }
        ?>
            <div id="resources" class="o-tab" style="display:none">

            <p>No resources available at this locations.</p>

            </div>
        </div>
        <script>
        function opentab(evt, tabName) {
            var i, x, tablinks;
            x = document.getElementsByClassName("o-tab");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";  
            }
            tablinks = document.getElementsByClassName("o-link");
            for (i = 0; i < x.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        </script>

    </div>
<!-- /wp:group -->