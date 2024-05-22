<?php
/**
 * Title: All documents
 * Slug: twentytwentyfour/all-documents
 * Categories: query
 * Keywords: Documents
 * Block Types: core/template-single
 */


$args = [	
    'post_type' 		=> 'dlp_document',
    'posts_per_page'	=> -1,		
    'orderby' 			=> [
        'date'	=> 'DESC',
        'title'	=> 'ASC'
    ]
];

// The Query.
$the_query = new WP_Query( $args );

// The Loop.
if ( $the_query->have_posts() ) {

	while ( $the_query->have_posts() ) {
		$the_query->the_post();

		// echo esc_html( get_the_title() );

	}

} else {
	esc_html_e( 'Sorry, no posts matched your criteria.' );
}
// Restore original Post Data.
wp_reset_postdata();