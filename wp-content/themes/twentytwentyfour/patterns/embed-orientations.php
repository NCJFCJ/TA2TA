<?php
/**
 * Title: Orientations Single
 * Slug: twentytwentyfour/embed-orientations
 * Categories: query
 * Keywords: embed-orientations
 * Block Types: core/template-single
 */

 $permalink_explode = explode('/', "$_SERVER[REQUEST_URI]");
 $slug = $permalink_explode[2];

 // Get list of all taxonomy - orientation-categories terms
 $args = array(
     'taxonomy' => 'orientations-categories',
     'orderby' => 'name',
     'order'   => 'ASC'
 );
 $cats = get_categories($args);
 
        // For every Terms of custom taxonomy get their posts by term_id
        $cat_name = '';
        $term_oid = 0;
        foreach($cats as $cat) {
            if( $cat->slug == $slug ){
                $term_oid = $cat->term_id;
                $cat_name = $cat->name;
            }
        }
        ?>
    <div class="orientations_categories_page">
        <div class="orientations_tabs">
            <div class="o-tabs-container">
                <h1 class="orientation_categories_cat-title"><?php echo $cat_name; ?> Orientation</h1>
                <ul class="o-tabs-list" role="tablist">
                    <li class="o-tab-list-item" role="none">
                        <a href="#" class="o-link active" data-tab="0" role="tab" onclick="opentab(event, 'ngo-recording')">
                            NGO Recordings
                        </a>
                    </li>
                    <li class="o-tab-list-item" role="none">
                        <a href="#" class="o-link" data-tab="1" role="tab" onclick="opentab(event, 'resources')">
                            Resources
                        </a>
                    </li>
                    <li class="o-tab-list-item" role="none">
                        <a href="#" class="o-link" data-tab="2" role="tab" onclick="opentab(event, 'ovw_checklist')">
                            OVW Checklist
                        </a>
                    </li>
                </ul>
                <div id="ngo-recording" class="o-tab">
                    <div class="orientation">
                        <h3 class="wp-block-heading has-text-align-center">NGO Recordings</h3>
                        <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
                        <div class="orientation-recording-cat">
                            <?php
                            // Query Arguments
                            $args = array(
                                'post_type' => 'orientations', // the post type
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'orientations-categories', // the custom vocabulary
                                        'field'    => 'slug',          // term_id, slug or name  (Define by what you want to search the below term)    
                                        'terms'    => $slug,      // provide the term slugs
                                    ),
                                ),
                            );

                            // The query
                            $the_query = new WP_Query( $args );

                            // The Loop
                            if ( $the_query->have_posts() ) {
                                while ( $the_query->have_posts() ) {
                                    $the_query->the_post();
                                    $id = get_the_ID();
                                    $fields = get_fields();
                                    //echo '<pre>';
                                    //var_dump($fields);
                                    //echo '</pre>';
                                    if($fields['ppt_english']){
                                        $image_url = str_replace('.pdf','-pdf.jpg', $fields['ppt_english']['url']);
                                    }
                                    ?>
                                    <div class="orientations-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </div>
                                    <div class="orientation-recording">
                                        <div class="ngo-recording">
                                            <div class="youtube-recording">
                                                <?php echo $fields['ngo_recording'];?>
                                            </div>
                                            <?php if(! empty($fields['ppt_english'])): ?>
                                            <div class="wp-block-buttons is-layout-flex wp-container-15 wp-block-buttons-is-layout-flex o-buttons" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)">
                                                <div class="wp-block-button has-custom-width wp-block-button__width-100">
                                                    <a class="wp-block-button__link wp-element-button" href="<?php echo $fields['ppt_english']['url']; ?>">
                                                        <?php echo $fields['ppt_english_title']; ?>
                                                    </a>
                                                </div>
                                                <?php if($fields['ppt_spanish_korean']): ?>
                                                <div class="wp-block-button has-custom-width wp-block-button__width-100">
                                                    <a class="wp-block-button__link wp-element-button" href="<?php echo $fields['ppt_spanish_korean']['url']; ?>">
                                                        <?php echo $fields['ppt_spanish_or_korean_title'];?>
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                                <?php if($fields['ppt_other']): ?>
                                                <div class="wp-block-button has-custom-width wp-block-button__width-100">
                                                    <a class="wp-block-button__link wp-element-button" href="<?php echo $fields['ppt_other']['url']; ?>">
                                                        <?php echo $fields['ppt_other_title'];?>
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="orientation-content">
                                            <div class="orientations-content">
                                                <?php the_content(); ?>
                                            </div>
                                            
                                            <div class="orientations-date">
                                                Date : <?php echo $fields['orientation_date'];?>
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
                                    <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
                                    <?php
                                    }//end while
                                }   else    {
                                            // no posts found
                                    }
                                wp_reset_postdata(); // reset global $wp_query;
                            ?>
                        </div>
                    </div>
                </div>
                <div id="resources" class="orientation o-tab" style="display:none">
                    <h3 class="wp-block-heading has-text-align-center">Resources</h3>
                    <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
                    <?php 
                        $args = array(
                            'post_type' => 'resource', // the post type
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'orientations-categories', // the custom vocabulary
                                    'field'    => 'slug',          // term_id, slug or name  (Define by what you want to search the below term)    
                                    'terms'    => $slug,      // provide the term slugs
                                ),
                            ),
                        );

                        // The query
                        $the_query = new WP_Query( $args );
                        // The Loop
                        if ( $the_query->have_posts() ) {
                            while ( $the_query->have_posts() ) {
                                $the_query->the_post();
                                $id = get_the_ID();
                                $fields = get_fields();
                                if($fields['resource']['url']){
                                $image_r_url = str_replace('.pdf','-pdf.jpg', $fields['resource']['url']);
                                }
                                ?>
                                <!-- wp:group {"layout":{"type":"constrained"}} -->
                                <div class="wp-block-group">
                                    <div class="resource-body">
                                        <p class="resource-title"><?php the_title(); ?></p>
                                        <a class="resource-link" href="<?php echo $fields['resource']['url']; ?>">
                                            <img decoding="async" class="image-document" src="<?php echo $image_r_url ?>" style="width:175px;height:300px;border-radius:3px;"><br><?php echo $fields['resource_button_text']; ?>
                                        </a>
                                    </div>
                                </div>
                                <!-- /wp:group -->
                                <?php
                                the_content();
                                ?>
                                <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
                                <?php
                            }//end while
                        }   else    {
                            ?>
                                <p style="text-align: center">No Orientation resources available at this locations.</p>
                            <?php
                            }
                        wp_reset_postdata(); // reset global $wp_query;
                    ?>
                </div>
                <div id="ovw_checklist" class="orientation o-tab" style="display:none">
                    <h3 class="wp-block-heading has-text-align-center">OVW Checklist</h3>
                    <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
                    <?php 
                        $args = array(
                            'post_type' => 'ovw_checklist', // the post type
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'orientations-categories', // the custom vocabulary
                                    'field'    => 'slug',          // term_id, slug or name  (Define by what you want to search the below term)    
                                    'terms'    => $slug,      // provide the term slugs
                                ),
                            ),
                        );

                        // The query
                        $the_query = new WP_Query( $args );
                        // The Loop
                        if ( $the_query->have_posts() ) {
                            while ( $the_query->have_posts() ) {
                                $the_query->the_post();
                                $id = get_the_ID();
                                $fields = get_fields();
                                if($fields['resource']['url']){
                                $image_r_url = str_replace('.pdf','-pdf.jpg', $fields['resource']['url']);
                                }
                                ?>
                                <!-- wp:group {"layout":{"type":"constrained"}} -->
                                <div class="wp-block-group">
                                    <div class="resource-body">
                                        <p class="resource-title"><?php the_title(); ?></p>
                                        <a class="resource-link" href="<?php echo $fields['resource']['url']; ?>">
                                            <img decoding="async" class="image-document" src="<?php echo $image_r_url ?>" style="width:175px;height:300px;border-radius:3px;"><br><?php echo $fields['resource_button_text']; ?>
                                        </a>
                                    </div>
                                </div>
                                <!-- /wp:group -->
                                <?php
                                the_content();
                                ?>
                                <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
                                <?php
                            }//end while
                        }   else    {
                            ?>
                                <p style="text-align: center">No Orientation OVW Checklist available at this locations.</p>
                            <?php
                            }
                        wp_reset_postdata(); // reset global $wp_query;
                    ?>
                </div>
            </div>
        </div>

        <div class="wp-block-buttons is-vertical is-content-justification-center is-layout-flex wp-container-20 wp-block-buttons-is-layout-flex" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)">
            <div class="wp-block-button has-custom-width wp-block-button__width-50">
                <a class="wp-block-button__link wp-element-button" href="/directory">TA2TA TA Provider Directory</a>
            </div>
            <div class="wp-block-button has-custom-width wp-block-button__width-50">
                <a class="wp-block-button__link wp-element-button" href="https://www.justice.gov/ovw/grant-programs" target="_blank" rel="noreferrer noopener">OVW Website</a>
            </div>
        </div>
    </div>
        