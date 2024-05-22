<?php
/**
 * Title: Test newsletter template
 * Slug: twentytwentyfour/test-build-content
 * Template Types: index, home 
 * Viewport width: 1600
 * Inserter: no
 */
?>

<?php 
echo '<br> ==== Start Test ==== <br>';

$user_id = get_current_user_id();
	$org = get_field("organization_for_user", "user_{$user_id}");

    echo '<br>' . $org. '<br>';


echo '<pre>';

//39552

$user_grants_projects = organization_user_grant_projects();
$user_grants_projects = organization_user_grant_project_grant_programs(39552, '2017-TA-AX-K043','grant_programs');
//$grant_programs_lister = organization_user_grant_project_grant_programs( $post_id, $selected_award, 'grant_programs' );

print_r($user_grants_projects);


//$mts = get_metadata( 39522 , 'award_number_for_library', false );
$mts = get_field('award_number_for_library', 39552, false );
$selected_award = get_post_meta( 39522 , 'award_number_for_library', false );
//print_r($mts);

//print_r( $selected_award );

echo '</pre>';


$d = 'newsletter-placeholder-2';

$pod = ta2ta_get_post_id_by_slug($d);

$thumbId = get_post_thumbnail_id();

$att = get_attached_media('post', $d);
echo '============= : <br>';
echo 'Post ID : <br>';
print_r($pod);
echo '============= : <br>';
echo 'Post THUMB : <br>';
print_r($thumbId);
echo '============= : <br>';
echo 'Post ATTACH : <br>';
print_r($att);

$ev = 'tribe_get_events()';


$array_obj_of_event_meta = array();
	$args = [
		'post_type'	=>	'tribe_events',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby'	=> 'post_title',
		'order'	=> 'ASC'
	];
	$org_query = new WP_Query($args);
	if ( $org_query->have_posts() ) : 
		while ( $org_query->have_posts() ): $org_query->the_post();
			$array_obj_of_event_meta[get_the_ID()]= get_post_meta(get_the_ID(),'_tribe_virtual_events_type');
		endwhile;
		wp_reset_postdata();
	endif;
	wp_reset_query();



echo '<pre>';
var_dump($array_obj_of_event_meta);
echo '</pre>';

echo  'THE EVENTS : ' + count($array_obj_of_event_meta);