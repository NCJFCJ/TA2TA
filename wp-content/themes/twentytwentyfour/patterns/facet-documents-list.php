<?php 

/**
 * Title: Facet Documents List
 * Slug: twentytwentyfour/facet-documents-list
 * Categories: query
 * Keywords: documents, list
 * Block Types: core/template-part/post
 */
    $user_id = get_current_user_id();

	$user_org = get_field('organization_for_user', 'user_' . $user_id);
	// query will only pull everything from today and forward
	// This is due to the post type, tribe_events
	// This custom post type was made to behave like this.
	$query = new WP_Query( array(
			'posts_per_page'	=> -1,		
			'post_type' 		=> 'dlp_document',
			'orderby' 			=> [
				'date'	=> 'DESC',
				'title'	=> 'ASC',
			],
			'facewp'	=> true
		) 
	);
?>
	<table id="responsive-data-table" class="table table-bordered table-hover dt-responsive nowrap dataTable no-footer dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="responsive-data-table_info">
	<thead style="text-align: center">
		<tr role="row">
			<th class="sorting_asc" tabindex="0" aria-controls="responsive-data-table" rowspan="1" colspan="1" style="width: 100%;" aria-sort="ascending" aria-label="Document Name: activate to sort column descending">
				Document list ( Click the document title to edit)
			</th>
		</tr>
	</thead>
	<tbody>
<?php

	//check
	if ( $query->have_posts() ):

	    while ($query->have_posts()){
			
			$query->the_post();
			
			$current_post_org = get_post_meta(get_the_id(), 'organization_for_library', true);

			if( $current_post_org == $user_org) {
				?>
                <tr role="row" class="">
                    <td tabindex="0" class="sorting_1">
                        <div class="">
                            <a href="/edit-my-documents-directory/?post_id=<?php the_ID();?>"><?php echo get_the_title();?></a>
                            <?php
                            $status = wp_get_object_terms( get_the_id(), 'doc_categories' );
                            if ( ! empty( $status ) ) {
                                if ( ! is_wp_error( $status ) ) {
                
                                    foreach( $status as $term ) {
                                        echo 'Status: <em>' . $term->name . '</em>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </td>
                </tr>
			<?php	
				
			}

		}
    	
	endif;
		?>
        </tbody>
	</table>
<?php //wp_reset_postdata(); ?>

