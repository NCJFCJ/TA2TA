<?php
/**
 * Title: Analytics Report Page
 * Slug: twentytwentyfour/analytics-report
 * Categories: Page
 * Keywords: reporting
 */


$user_id = get_current_user_id();
$total_download = 0;
$user_org = get_field('organization_for_user', 'user_' . $user_id);
// query will only pull everything from today and forward
// This is due to the post type, tribe_events
// This custom post type was made to behave like this.
$doc_query = new WP_Query( array(
        'posts_per_page'	=> -1,		
        'post_type' 		=> 'dlp_document',
        'orderby' 			=> [
            'date'	=> 'DESC',
            'title'	=> 'ASC',
        ],
    ) 
);
?>
 <table id="responsive-data-table" class="table table-bordered table-hover dt-responsive nowrap dataTable no-footer dtr-inline collapsed" style="width: 100%;" role="grid" aria-describedby="responsive-data-table_info">
 <thead style="text-align: center">
     <tr role="row">
         <th class="sorting_asc" tabindex="0" aria-controls="responsive-data-table" rowspan="1" colspan="1" style="width: 100%;" aria-sort="ascending" aria-label="Document Name: activate to sort column descending">
            Document
         </th>
         <th>Number Of Downloads</th>
     </tr>
 </thead>
 <tbody>
<?php

 //check
 if ( $doc_query->have_posts() ):

    while ($doc_query->have_posts()){
         
        $doc_query->the_post();
    
        $current_post_org = get_post_meta(get_the_id(), 'organization_for_library', true);
        
        $download_count = get_post_meta( get_the_id(), '_dlp_download_count', true ); //get_meta_data( '_dlp_download_count' )
        // if((int)$download_count != 0){
        //     $total_download = $total_download + (int)$download_count;
        // }
        if( $current_post_org == $user_org && $download_count >= 1 ) {
            ?>
            <tr role="row" class="">
                <td tabindex="0" class="sorting_1">
                    <div class="">
                        <a href="<?php the_permalink();?>"><?php echo get_the_title();?></a>
                    </div>
                </td>
                <td>
                    <div class="download-number">
                        <?php echo $download_count ;?>
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

<!-- <div class="">
    The total number of All Downloads is : 
    <span class="mb-2 mr-2 badge badge-dark"><?php //echo $total_download;?></span>
</div> -->