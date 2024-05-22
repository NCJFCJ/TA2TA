<?php
/**
 * Title: Orientations Post
 * Slug: twentytwentyfour/orientations-post
 * Categories: query
 * Keywords: Orientations
 * Block Types: core/template-part
 */
?>

<?php
    $orientations_id = get_orientations_post_id();
    if($orientations_id != 'NOID'){
        $fields = get_fields($orientations_id);

        if($fields['ppt_english']){
            $image_url = str_replace('.pdf','-pdf.jpg', $fields['ppt_english']['url']);
        }
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group orientations-container">
    <div class="o-tabs-container">
        <ul class="o-tabs-list" role="tablist">
            <li class="o-tab-list-item" role="none">
                <a href="#" class="o-link active" data-tab="0" role="tab" onclick="opentab(event, 'ngo-recording-<?php echo $orientations_id;?>')">
                    NGO Recordings
                </a>
            </li>
            <li class="o-tab-list-item" role="none">
                <a href="#resources" class="o-link" data-tab="1" role="tab" onclick="opentab(event, 'resources-<?php echo $orientations_id;?>')">
                    Resources
                </a>
            </li>
        </ul>

        <div id="ngo-recording-<?php echo $orientations_id;?>" class="o-tab">
            <div class="orientation">
                <h3 class="wp-block-heading has-text-align-center">NGO Recordings</h3>
                <div class="orientation-recording">
                    <div class="ngo-recording">
                        <div class="youtube-recording">
                            <?php echo $fields['ngo_recording'];?>
                        </div>
                        <?php if(! empty($fields['ppt_english'])): ?>
                        <div class="wp-block-buttons is-layout-flex wp-container-15 wp-block-buttons-is-layout-flex" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)">
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
                        <!-- wp:post-content /-->
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
            </div>
        </div>

        <div id="resources-<?php echo $orientations_id;?>" class="orientation o-tab" style="display:none">
            <h3 class="wp-block-heading has-text-align-center">Resources</h3>
            <hr class="wp-block-separator has-alpha-channel-opacity is-style-default" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)">
            <?php 
                $args = array(
                    'post_type' => 'resource', // the post type
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'orientations-categories', // the custom vocabulary
                            'field'    => 'term_id',          // term_id, slug or name  (Define by what you want to search the below term)    
                            'terms'    => $orientations_id,      // provide the term slugs
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
                        $image_r_url = str_replace('.pdf','-pdf.jpg', $fields['resource']['url']);
                        ?>
                        <!-- wp:group {"layout":{"type":"constrained"}} -->
                        <div class="wp-block-group">
                            <div class="resource-body">
                                <p class="resource-title"><?php the_title(); ?></p>
                                <a class="resource-link" href="<?php echo $fields['resource']['url']; ?>">
                                    <img decoding="async" class="image-document" src="<?php echo $image_r_url ?>" style="width:175px;height:300px;border-radius:3px;"><br>Get Resource
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
<?php 
}   else    {
    ?><!-- wp:post-content /--><?php
}