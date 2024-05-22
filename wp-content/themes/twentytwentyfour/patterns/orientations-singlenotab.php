<?php
/**
 * Title: Orientations Post
 * Slug: twentytwentyfour/orientations-singleotab
 * Categories: query
 * Keywords: Orientations
 * Block Types: core/template-part
 */
?>



<?php
    $orientations_id = get_orientations_post_id();

    $fields = get_fields($orientations_id);

    $image_url = str_replace('.pdf','-pdf.jpg', $fields['ppt_english']['url']);
?>

        <div id="ngo-recording" class="o-tab">
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
                                <img decoding="async" class="image-document" src="<?php echo $image_url; ?>" style="width:175px;height:300px;border-radius:3px;"><br>PPT English
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
        </div>

        <div id="resources" class="o-tab" style="display:none">

            <p>No resources available at this locations.</p>

        </div>
    