<?php
/**
 * Title: Orientations Categories page
 * Slug: twentytwentyfour/orientations-categories-page
 * Categories: query
 * Keywords: orientations-categories-page
 * Block Types: core/template-single
 */

 // Get list of all taxonomy - orientation-categories terms
 $args = array(
     'taxonomy' => 'orientations-categories',
     'orderby' => 'name',
     'order'   => 'ASC'
 );
 $cats = get_categories($args);
 ?>


    <div class="wp-block-uagb-container page-notice-block">
		<div class="notice-header">
			<div class="notice-item hidden-xs image-header-img">
				<span class="p-orientation-svg">
                    <svg id="a" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 35.72 35.34"><defs><clipPath id="b"><path d="M6.23-2v31.11h31.11V-2H6.23ZM25.38,24.78l-6.22-6.22,7.63-7.63,6.22,6.22-7.63,7.63Z" style="fill:none; stroke-width:0px;"/></clipPath><clipPath id="c"><path d="M-2,6.23v31.11h31.11V6.23H-2ZM8.92,24.78l-6.22-6.22,7.63-7.63,6.22,6.22-7.63,7.63Z" style="fill:none; stroke-width:0px;"/></clipPath></defs><rect x="7.01" y="9.16" width="29.54" height="8.79" transform="translate(27.61 38.54) rotate(-135)" style="fill:#f06724; stroke-width:0px;"/><rect x="-1.22" y="17.39" width="29.54" height="8.79" transform="translate(7.73 46.77) rotate(-135)" style="fill:#049898; stroke-width:0px;"/><rect x="-.84" y="9.16" width="29.54" height="8.79" transform="translate(-5.5 13.82) rotate(-45)" style="fill:#989898; stroke-width:0px;"/><rect x="7.39" y="17.39" width="29.54" height="8.79" transform="translate(-8.91 22.05) rotate(-45)" style="fill:#652e8e; stroke-width:0px;"/><g style="clip-path:url(#b);"><rect x="7.01" y="9.16" width="29.54" height="8.79" transform="translate(27.61 38.54) rotate(-135)" style="fill:#f06724; stroke-width:0px;"/></g><g style="clip-path:url(#c);"><rect x="-1.22" y="17.39" width="29.54" height="8.79" transform="translate(7.73 46.77) rotate(-135)" style="fill:#049898; stroke-width:0px;"/></g></svg>
				</span>
			</div>
			<div class="notice-item notice-text">
                The Office on Violence Against Women (OVW) administers grant programs authorized by the Violence Against Women Act (VAWA) of 1994 and subsequent legislation. These grant programs are designed to develop the nation's capacity to reduce domestic violence, dating violence, sexual assault, and stalking by strengthening services to victims and holding offenders accountable. Click on the desired grant program to find orientation materials and information.
			</div>
		</div>
	</div>

    <!-- wp:group {"layout":{"type":"constrained"}} -->
    <div class="wp-block-group">
        <div class="wp-block-buttons alignwide orientationBtns is-horizontal is-content-justification-center is-layout-flex wp-container-12 wp-block-buttons-is-layout-flex">
        <?php
            // For every Terms of custom taxonomy get their posts by term_id
            foreach($cats as $cat) {

                if($cat->name =='Formula Grant'){
                    ?>
                    <div class="wp-block-button">
                        <a class="wp-block-button__link wp-element-button orientations-buttons" href="/orientations/<?php echo $cat->slug;?>">
                        <?php echo $cat->name; ?>
                        </a>
                    </div>
                    <?php
                } elseif($cat->name == 'Administrators' || $cat->name == 'Coalition Directors') {
                        //DO NOTHING
                } else{
                ?>
                    <div class="wp-block-button">
                        <a class="wp-block-button__link wp-element-button orientations-buttons" href="/orientation_categories/<?php echo $cat->slug;?>">
                        <?php echo $cat->name; ?>
                        </a>
                    </div>
                <?php
                }
            }
        ?>
        </div>
    </div>
    <!-- /wp:group -->