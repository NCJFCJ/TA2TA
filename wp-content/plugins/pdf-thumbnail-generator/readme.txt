=== PDF Thumbnail Generator ===
Contributors: kubiq
Donate link: https://www.paypal.me/jakubnovaksl
Tags: pdf, image, thumbnail, generator, creator
Requires at least: 3.0.1
Requires PHP: 5.6
Tested up to: 6.5
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generates thumbnail for PDF files


== Description ==

Generates thumbnail for PDF file automatically after file is uploaded to the Media library.

You can also generate thumbnails for old PDF files that are already in the Media library - you can generate missing thumbnails or regenerate all thumbnails.

<strong>Imagick library must be installed on your server, otherwise this plugin will not work</strong>

<ul>
	<li>automated test after plugin activation to make sure it will work on your server</li>
	<li>works with all types of WordPress installations: domain, subdomain, subdirectory, multisite/network</li>
	<li>works on Apache and NGiNX</li>
	<li>automatically generate thumbnail for new uploaded PDFs</li>
	<li>(re)generate thumbnails for existing PDFs in Media library</li>
	<li>set maximum width of PDF thumbnail</li>
	<li>set maximum height of PDF thumbnail</li>
	<li>set image quality of PDF thumbnail</li>
	<li>set image file type of PDF thumbnail</li>
</ul>

## Shortcodes

### pdf_thumbnail
Maybe you want to display PDF thumbnail by using a shortcode

`[pdf_thumbnail id="123"]`

### pdf_thumbnail_url
Maybe you want to display PDF thumbnail url by using a shortcode

`[pdf_thumbnail_url id="123"]`
 
## Functions

### get_pdf_thumbnail_url
If you want to return PDF thumbnail URL you can use

`get_pdf_thumbnail_url( $pdf_id )`

it works similar to `wp_get_attachment_url` and it will return something like

`https://site.com/wp-content/uploads/2022/01/example.pdf.png`

### get_pdf_thumbnail_path
If you want to return PDF thumbnail URL you can use

`get_pdf_thumbnail_path( $pdf_id )`

it works similar to `get_attached_file` and it will return something like

`/www/site.com/wp-content/uploads/2022/01/example.pdf.png`

### get_pdf_thumbnail_image_src
If you want to return PDF thumbnail url, width and height you can use

`get_pdf_thumbnail_image_src( $pdf_id )`

it works similar to `wp_get_attachment_image_src` and it will return something like

`[
	0 => 'https://site.com/wp-content/uploads/2022/01/example.pdf.png',
	1 => 600,
	2 => 800
]`

### get_pdf_thumbnail_image
If you want to return PDF thumbnail image tag you can use

`get_pdf_thumbnail_image( $pdf_id )`

it works similar to `wp_get_attachment_image` and it will return something like

`<img src="https://site.com/wp-content/uploads/2022/01/example.pdf.png" width="600" height="800" alt="example" loading="lazy">`
 
## Hooks

### pdf_thumbnail_max_width
Maybe you want to change global PDF thumbnail max_width for a specific PDF file

`add_filter( 'pdf_thumbnail_max_width', function( $max_width, $pdf_id ){
	if( $pdf_id == 123 ){
		return 1024;
	}
	return $max_width;
}, 10, 2 );`

### pdf_thumbnail_max_height
Maybe you want to change global PDF thumbnail max_width for a specific PDF file

`add_filter( 'pdf_thumbnail_max_height', function( $max_height, $pdf_id ){
	if( $pdf_id == 123 ){
		return 768;
	}
	return $max_height;
}, 10, 2 );`

### pdf_thumbnail_quality
Maybe you want to change global PDF thumbnail quality for a specific PDF file

`add_filter( 'pdf_thumbnail_quality', function( $quality, $pdf_id ){
	if( $pdf_id == 123 ){
		return 100;
	}
	return $quality;
}, 10, 2 );`

### pdf_thumbnail_type
Maybe you want to change global PDF thumbnail file type for a specific PDF file

`add_filter( 'pdf_thumbnail_type', function( $type, $pdf_id ){
	if( $pdf_id == 123 ){
		return 'png'; // or 'jpg'
	}
	return $type;
}, 10, 2 );`

### pdf_thumbnail_bgcolor
Maybe you want to change default PDF thumbnail background for a specific PDF file

`add_filter( 'pdf_thumbnail_bgcolor', function( $bgcolor, $pdf_id ){
	if( $pdf_id == 123 ){
		return 'black'; // default is 'white'
	}
	return $bgcolor;
}, 10, 2 );`

### pdf_thumbnail_page_number
Maybe you want to PDF thumbnail page number for a specific PDF file

`add_filter( 'pdf_thumbnail_page_number', function( $page, $pdf_id ){
	if( $pdf_id == 123 ){
		return 1; // default is 0
	}
	return $page;
}, 10, 2 );`

### pdf_thumbnail_filename
Maybe you want to PDF thumbnail filename for a specific PDF file

`add_filter( 'pdf_thumbnail_filename', function( $filename, $pdf_id ){
	if( $pdf_id == 123 ){
		return str_replace( '.pdf.png', '.png', $filename );
	}
	return $filename;
}, 10, 2 );`

### pdf_thumbnail_imagick
Maybe you want to add watermark to PDF thumbnail for a specific PDF file

`add_filter( 'pdf_thumbnail_imagick', function( $imagick, $pdf_id ){
	if( $pdf_id == 123 ){
		// add your watermark here
	}
	return $imagick;
}, 10, 2 );`

### get_pdf_thumbnail_image_attributes
Maybe you want to change attributes for image tag from `get_pdf_thumbnail_image` function

`add_filter( 'get_pdf_thumbnail_image_attributes', function( $attr, $pdf_id ){
	$attr['loading'] = 'eager';
	return $attr;
}, 10, 2 );`

### pdf_thumbnail_generated
Maybe you want to do something after the thumbnail is generated

`add_action( 'pdf_thumbnail_generated', function( $thumbnail_path, $pdf_id ){
	// do somthing with the local file $thumbnail_path
}, 10, 2 );`


== Installation ==

1. Upload `pdf-thumbnail-generator` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Plugin requirements =

PHP 5.6 or higher
Imagick extension

= PDF thumbnails stored location =

PDF thumbnails are generated in the same directory as original PDF file. Example:
pdf file: `/wp-content/uploads/2022/01/example.pdf`
thumbnail: `/wp-content/uploads/2022/01/example.pdf.png`


== Changelog ==

= 1.2 =
* tested on WP 6.5

= 1.1 =
* tested on WP 6.4

= 1.0 =
* First version