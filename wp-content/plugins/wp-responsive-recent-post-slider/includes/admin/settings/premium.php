<?php
/**
 * Plugin Premium Offer Page
 *
 * @package WP Responsive Recent Post Slider
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap">

	<h2><?php esc_html_e( 'WP Responsive Recent Post Slider', 'wp-responsive-recent-post-slider' ); ?></h2>

	<style>
		.wpos-new-feature{font-size: 10px; color: #fff; font-weight: bold; background-color: #03aa29; padding:1px 4px; font-style: normal;}
		.wpos-plugin-pricing-table thead th h2{font-weight: 400; font-size: 2.4em; line-height:normal; margin:0px; color: #2ECC71;}

		table.wpos-plugin-pricing-table{width:100%; text-align: left; border-spacing: 0; border-collapse: collapse; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;}
		.wpos-plugin-pricing-table th, .wpos-plugin-pricing-table td{font-size:14px; line-height:normal; color:#444; vertical-align:middle; padding:12px;}

		.wpos-plugin-pricing-table colgroup:nth-child(1) { width: 31%; border: 0 none; }
		.wpos-plugin-pricing-table colgroup:nth-child(2) { width: 22%; border: 1px solid #ccc; }
		.wpos-plugin-pricing-table colgroup:nth-child(3) { width: 25%; border: 10px solid #2ECC71; }

		/* Tablehead */
		.wpos-plugin-pricing-table thead th {background-color: #fff; background:linear-gradient(to bottom, #ffffff 0%, #ffffff 100%); text-align: center; position: relative; border-bottom: 1px solid #ccc; padding: 1em 0 1em; font-weight:400; color:#999;}
		.wpos-plugin-pricing-table thead th:nth-child(1) {background: transparent;}
		.wpos-plugin-pricing-table thead th:nth-child(3) p{color:#000;}
		
		/* Tablebody */
		.wpos-plugin-pricing-table tbody th{background: #fff; border-left: 1px solid #ccc; font-weight: 600;}
		.wpos-plugin-pricing-table tbody th span{font-weight: normal; font-size: 87.5%; color: #999; display: block;}

		.wpos-plugin-pricing-table tbody td{background: #fff; text-align: center;}
		.wpos-plugin-pricing-table tbody td .dashicons{height: auto; width: auto; font-size:30px;}
		.wpos-plugin-pricing-table tbody td .dashicons-no-alt{color: #ff2700;}
		.wpos-plugin-pricing-table tbody td .dashicons-yes{color: #2ECC71;}

		.wpos-plugin-pricing-table tbody tr:nth-child(even) th,
		.wpos-plugin-pricing-table tbody tr:nth-child(even) td { background: #f5f5f5; border: 1px solid #ccc; border-width: 1px 0 1px 1px; }
		.wpos-plugin-pricing-table tbody tr:last-child td {border-bottom: 0 none;}

		/* Table Footer */
		.wpos-plugin-pricing-table tfoot th, .wpos-plugin-pricing-table tfoot td{text-align: center; border-top: 1px solid #ccc;}
		.wpos-plugin-pricing-table tfoot a, .wpos-plugin-pricing-table thead a{font-weight: 600; color: #fff; text-decoration: none; text-transform: uppercase; display: inline-block; padding: 1em 2em; background: #ff2700; border-radius: .2em;}
		.wpos-epb{color:#ff2700 !important;}

		.h-blue{color:#0055fb;}
		.wpos-deal-heading{padding:0px 10px;}
		
		/* Table CSS */
		table, th, td {border: 1px solid #d1d1d1;}
		table.wpos-plugin-list{width:100%; text-align: left; border-spacing: 0; border-collapse: collapse; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; margin-bottom: 50px;}
		.wpos-plugin-list th {width: 16%; background: #2271b1; color: #fff; }
		.wpos-plugin-list td {vertical-align: top;}
		.wpos-plugin-type { text-align: left; color: #fff; font-weight: 700; padding: 0 10px; margin: 15px 0; }
		.wpos-slider-list { font-size: 14px; font-weight: 500; padding: 0 10px 0 25px; }
		.wpos-slider-list li {text-align: left; font-size: 13px; list-style: disc;}
		.wppsac-sf-blue { color: #6c63ff;  font-weight: bold;}
		.wppsac-sf-btn {display: inline-block;font-size: 18px;padding: 10px 25px;border-radius: 100px;background-color: #46b450;border-color: #46b450;color: #fff !important;font-weight: 600;text-decoration: none;}
.wppsac-sf-btn-orange {background-color: #FF1000;border-color: #FF1000;}
	</style>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
			
			<div style="text-align: center; background: #DCDCDC; margin: 30px 0; padding: 10px 30px 30px 30px;">
					<!-- <h1 style="font-size: 28px; font-weight: 700; letter-spacing: -1px; text-align: center; margin-top: 30px; margin-bottom: 5px;">Only <span class="wppsac-sf-blue">November 2022</span> Deal <span class="wppsac-sf-blue">40% OFF</span></h1>
					<h2>Build <span class="bg-highlight">better websites</span>, <span class="bg-highlight">landing pages</span> & <span class="bg-highlight">conversion flow</span></h2>
					<h2>With <span class="wppsac-sf-blue">35+ plugins</span>, <span class="wppsac-sf-blue">2000+ templates</span> & $600 saving in <span class="wppsac-sf-blue">Essential Plugin Bundle</span></h2>
					<h3><span style="text-decoration:line-through; color: #FF1000;">$299</span> <span class="wppsac-sf-blue" style="font-size:30px;">$179</span> Unlimited Site License</h3>
					<a href="<?php// echo esc_url(WPRPS_PLUGIN_LINK_UPGRADE); ?>" target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> View Essential Plugin Bundle Deal</a>
					<h4 style="font-weight: 700; font-size: 13px; color: #000;">(One time <span class="bg-highlight">Payment</span> & <span class="bg-highlight">Lifetime</span> update)</h4> -->

					<p style="font-weight: bold !important; font-size:20px !important;"><span style="color: #50c621;">Essential Plugin Bundle</span> + Any Leading Builders (Avada / Elementor / Divi / <br>VC-WPBakery / Site Origin / Beaver) = <span style="background: #50c621;color: #fff;padding: 2px 10px;">WordPress Magic</span></p>
					<h4 style="color: #333; font-size: 14px; font-weight: 700;">Over 15K+ Customers Using <span style="color: #50c621 !important;">Essential Plugin Bundle</span></h4>
					<a href="<?php echo esc_url(WPRPS_PLUGIN_LINK_UPGRADE); ?>" target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> View Essential Plugin Bundle</a>
				</div>
				
				<h2 style="font-size: 24px; text-align: center; color: #6c63ff;">Bundle Deal Details</h2>
	<table class="wpos-plugin-list">
		<thead>
			<tr>
				<th><h3 class="wpos-plugin-type">Image Slider</h3></th>
				<th><h3 class="wpos-plugin-type">Marketing</h3></th>
				<th><h3 class="wpos-plugin-type">Photo Album</h3></th>
				<th><h3 class="wpos-plugin-type">Publication</h3></th>
				<th><h3 class="wpos-plugin-type">Showcase</h3></th>
				<th><h3 class="wpos-plugin-type">WooCommerce</h3></th>
			</tr>
			<tr>
				<td>
					<ul class="wpos-slider-list">
						<li>Accordion and Accordion Slider</li>
						<li>WP Slick Slider and Image Carousel</li>
						<li>WP Responsive Recent Post Slider/Carousel</li>
						<li>WP Logo Showcase Responsive Slider and Carousel</li>
						<li>WP Featured Content and Slider</li>
						<li>Trending/Popular Post Slider and Widget</li>
						<li>Timeline and History slider</li>
						<li>Meta slider and carousel with lightbox</li>
						<li>Post Category Image With Grid and Slider</li>
					</ul>
				</td>
				<td>
					<ul class="wpos-slider-list">
						<li>Popup Anything - A Marketing Popup and Lead Generation Conversions</li>
						<li>Countdown Timer Ultimate</li>
					</ul>
				</td>
				<td>
					<ul class="wpos-slider-list">
						<li>Album and Image Gallery plus Lightbox</li>
						<li>Portfolio and Projects</li>
						<li>Video gallery and Player</li>
					</ul>
				</td>
				<td>
					<ul class="wpos-slider-list">
						<li><span style="color:#2271b1; font-weight: bold;">WP Responsive Recent Post Slider/Carousel</span></li>
						<li>WP News and Scrolling Widgets</li>
						<li>WP Blog and Widget</li>
						<li>Blog Designer - Post and Widget</li>
						<li>Trending/Popular Post Slider and Widget</li>
						<li>WP Featured Content and Slider</li>
						<li>Timeline and History slider</li>
						<li>Testimonial Grid and Testimonial Slider plus Carousel with Rotator Widget</li>
						<li>Post Ticker Ultimate</li>
						<li>Post grid and filter ultimate</li>
					</ul>
				</td>
				<td>
					<ul class="wpos-slider-list">
						<li>Testimonial Grid and Testimonial Slider plus Carousel with Rotator Widget</li>
						<li>Team Slider and Team Grid Showcase plus Team Carousel</li>
						<li>Hero Banner Ultimate</li>
						<li>WP Logo Showcase Responsive Slider and Carousel</li>
					</ul>
				</td>
				<td>
					<ul class="wpos-slider-list">
						<li>Product Slider and Carousel with Category for WooCommerce</li>
						<li>Product Categories Designs for WooCommerce</li>
						<li>Popup Anything - A Marketing Popup and Lead Generation Conversions</li>
						<li>Countdown Timer Ultimate</li>
					</ul>
				</td>
			</tr>
		</thead>
	</table>

	<div style="text-align: center; margin-bottom:30px">
		<h3 class="wppsac-sf-blue" style="margin:0; margin-bottom:10px; font-size:24px; font-weight:bold;">Use Essential Plugin Bundle with Post Slider</h3>
		<h1 style="font-size: 28px; font-weight: 700; letter-spacing: -1px; text-align: center; padding:0; margin-bottom: 5px;">With Your Favourite Page Builders</h1>
		<span style="font-size: 14px; color: #000;">and see how Essential Plugins can help you.</span>
	</div>

	<div style="text-align: center;">
		<img style="width: 100%; margin-bottom:30px;" src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/image-upgrade.png" alt="image-upgrade" title="image-upgrade" />
		<div style="font-size: 14px; margin-bottom:10px;"><span class="wppsac-sf-blue">Post Slider </span>Including in <span class="wppsac-sf-blue">Essential Plugin Bundle</span></div>
		<a href="<?php echo esc_url(WPRPS_PLUGIN_LINK_UPGRADE); ?>" target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> View Essential Plugin Bundle</a>
	</div>
	
	<!-- <h3 style="text-align:center"><?php //esc_html_e( 'Compare "WP Responsive Recent Post Slider/Carousel" Free VS Pro', 'wp-responsive-recent-post-slider' ); ?></h3>
	<table class="wpos-plugin-pricing-table">
		<colgroup></colgroup>
		<colgroup></colgroup>
		<colgroup></colgroup>
				<thead>
			<tr>
				<th></th>
				<th>
					<h2>Free</h2>
				</th>
				<th>
					<h2 class="wpos-epb">Premium</h2>
					<h3 class="wpos-deal-heading">Choose best pricing in <span class="h-blue"> Annual</span> or <span class="h-blue">Lifetime</span> deal</h3>
					<a href="<?php //echo esc_url(WPRPS_PLUGIN_LINK_UPGRADE); ?>" target="_blank">Buy Now</a>
				</th>
			</tr>
		</thead>

		 <tfoot>
			<tr>
				<th></th>
				<td></td>
				<td>
				<h3 class="wpos-deal-heading">Choose best pricing in <span class="h-blue"> Annual</span> or <span class="h-blue">Lifetime</span> deal</h3>
				<a href="<?php //echo esc_url(WPRPS_PLUGIN_LINK_UPGRADE); ?>" target="_blank">Buy Now</a></td>
			</tr>
		</tfoot>

		<tbody>
			<tr>
				<th>Designs <span>Designs that make your website better</span></th>
				<td>4</td>
				<td>60+</td>
			</tr>
			<tr>
				<th>Shortcodes <span>Shortcode provide output to the front-end side</span></th>
				<td>2 (Slider and Carousel)</td>
				<td>4 (Slider,  Carousel, Grid Box Slider )</td>
			</tr>
			<tr>
				<th>Shortcode Parameters <span>Add extra power to the shortcode</span></th>
				<td>16</td>
				<td>30+</td>
			</tr>
			<tr>
				<th>Shortcode Generator <span>Play with all shortcode parameters with preview panel. No documentation required!!</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>WP Templating Features <span class="subtext">You can modify plugin html/designs in your current theme.</span></th>
				<td><i class="dashicons dashicons-no-alt"> </i></td>
				<td><i class="dashicons dashicons-yes"> </i></td>
			</tr>
			<tr>
				<th>Widgets<span> WordPress Widgets to your sidebars.</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Custom Post Type Support<span>Supports any custom post type</span></th>
				<td>Limited</td>
				<td>Unlimited </td>
			</tr>
			<tr>
				<th>Drag & Drop Post Order Change <span>Arrange your desired post with your desired order and display</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Gutenberg Block Supports <span>Use this plugin with Gutenberg easily</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Elementor Page Builder Support <em class="wpos-new-feature">New</em> <span>Use this plugin with Elementor easily</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Bevear Builder Support <em class="wpos-new-feature">New</em> <span>Use this plugin with Bevear Builder easily</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>SiteOrigin Page Builder Support <em class="wpos-new-feature">New</em> <span>Use this plugin with SiteOrigin easily</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Divi Page Builder Native Support <em class="wpos-new-feature">New</em> <span>Use this plugin with Divi Builder easily</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Fusion Page Builder (Avada) native support <em class="wpos-new-feature">New</em> <span>Use this plugin with Fusion(Avada) Builder easily</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>WPBakery Page Builder Support <span>Use this plugin with Visual Composer easily</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Image Lazyload <span>Lazyload support for the image.</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Custom Read More link for Post <span>Redirect post to third party destination if any</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
				<tr>
				<th>Ignore Sticky Post <span>Ignore sticky post or not</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Display Desired Post <span>Display only the post you want</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Exclude Some Posts <span>Do not display the posts you want</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Display Post for Particular Categories <span>Display only the posts with particular category</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Exclude Some Categories <span>Do not display the posts for particular categories</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Post Order / Order By Parameters <span>Display post according to date, title and etc</span></th>
				<td><i class="dashicons dashicons-no-alt"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Multiple Slider Parameters <span>Slider parameters like autoplay, number of slide, sider dots and etc.</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Slider RTL Support <span>Slider supports for RTL website</span></th>
				<td><i class="dashicons dashicons-yes"></i></td>
				<td><i class="dashicons dashicons-yes"></i></td>
			</tr>
			<tr>
				<th>Automatic Update <span>Get automatic  plugin updates </span></th>
				<td>Lifetime</td>
				<td>Yearly OR Lifetime</td>
			</tr>
			<tr>
				<th>Support <span>Get support for plugin</span></th>
				<td>Limited</td>
				<td>1 Year</td>
			</tr>
		</tbody>
	</table> -->
		</div>
	 </div>
	</div>
</div>