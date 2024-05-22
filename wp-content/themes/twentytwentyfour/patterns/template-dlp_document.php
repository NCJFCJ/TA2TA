<?php
/**
 * Title: Single document view template
 * Slug: twentytwentyfour/template-dlp_document
 * Template Types: Single document
 * Viewport width: 1248
 */

 /**
 * The template for displaying single documents.
 *
 * @package Twentytwentyfour ta2ta theme
 */

use Barn2\Plugin\Document_Library_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Frontend_Scripts;
?>



<div id="primary" class="content-area">

<!-- wp:group {"className": "site-main", "style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"0","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"},"tagName":"main"} -->
<main id="main" class="wp-block-group site-main" role="main" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">

		<?php
		while ( have_posts() ) :
			the_post();

			$document = dlp_get_document( get_the_ID() );

            $display_options = Options::get_document_display_fields();
            $options = Options::get_shortcode_options();

            ?>

            <div class="dlp-document-main">

                <?php
                the_title();
                the_post_thumbnail();
                the_content();
                ?>

            </div>

            <div class="dlp-document-info">
                <?php if ( $document->get_download_url() ) : ?>
                    <?php Frontend_Scripts::load_download_count_scripts(); ?>
                    <div class="dlp-document-info-buttons">
                        <?php echo $document->get_download_button( $options['link_text'], $options['link_style'], 'direct', $options['link_target'] ); ?>
                        <?php
                        if ( $document->is_allowed_preview_mime_type() && $options['preview'] ) :
                            Frontend_Scripts::load_preview_scripts();
                            ?>
                            <?php echo $document->get_preview_button( $options['preview_text'], $options['preview_style'], 'single' ); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div id="dlp-document-info-list">

                    <?php if ( $document->get_file_type() && in_array( 'file_type', $display_options, true ) ) : ?>
                        <div class="dlp-document-file-type">
                            <span class="dlp-document-info-title"><?php esc_html_e( 'File Type: ', 'document-library-pro' ); ?></span>
                            <?php echo $document->get_file_type(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $document->get_category_list() && in_array( 'doc_categories', $display_options, true ) ) : ?>
                        <div class="dlp-document-info-categories">
                            <span class="dlp-document-info-title"><?php esc_html_e( 'Categories: ', 'document-library-pro' ); ?></span>
                            <?php echo $document->get_category_list(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $document->get_tag_list() && in_array( 'doc_tags', $display_options, true ) ) : ?>
                        <div class="dlp-document-info-tags">
                            <span class="dlp-document-info-title"><?php esc_html_e( 'Tags: ', 'document-library-pro' ); ?></span>
                            <?php echo $document->get_tag_list(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $document->get_author_list() && in_array( 'doc_author', $display_options, true ) ) : ?>
                        <div class="dlp-document-info-author">
                            <span class="dlp-document-info-title"><?php esc_html_e( 'Author: ', 'document-library-pro' ); ?></span>
                            <?php echo $document->get_author_list(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $document->get_download_count() && in_array( 'download_count', $display_options, true ) ) : ?>
                        <div class="dlp-document-info-downloads">
                            <span class="dlp-document-info-title"><?php esc_html_e( 'Downloads: ', 'document-library-pro' ); ?></span>
                            <?php echo $document->get_download_count(); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

		<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

