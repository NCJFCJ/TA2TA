=== LH Archived Post Status ===
Contributors:      shawfactor
Donate link: 	   https://lhero.org/portfolio/lh-archived-post-status/
Tags:              admin, posts, pages, status, workflow
Requires at least: 5.0
Tested up to:      6.3
Stable tag:         3.10
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Allows posts and pages to be archived so you can remove content from the main loop and feed without having to trash it.

== Description ==

This plugin allows you to archive your WordPress content similar to the way you archive your e-mail. Unlike other archiving solutions though this actually does it all and does it properly

* Makes a new post status available in the drop down called Archived
* Hides or removes your content without having to trash the content
* Content can either be hidden entirely from public view  or simply from the main loop and feed and pages, with other solutions you can only hide it from public view.
* Allows you to add a label to the title of those posts/pages etc that are archived
* Allows you to add a message to the top of the post/page etc that the content is no longer up too date
* Allows you to set an archiving date after which content is automatically changed to having an archived status
* Compatible with posts, pages and custom post types

This plugin is ideal for sites where certain kinds of content is not meant to be evergreen

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-archived-post-status/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-archived-post-status/).**


== Frequently Asked Questions ==

= Isn't this the same as using the Draft or Private statuses? =

Actually, no, they are not the same thing.

The Draft status is a "pre-published" status that is reserved for content that is still being worked on. You can still make changes to content marked as Draft, and you can preview your changes.

The Private status is a special kind of published status. It means the content is only available to certain logged in users.

The Archived post status, on the other hand, is meant to be a "post-published" status. Once a post has been set to Archived the content is either hidden entirely from non logged in viewers or removed from the front page and feed (but still available on singular pages). This behaviour is controlled in the settings screen.

= Doesn't this do the same thing as the other archiving plugin in the repository? =

Actually it does more! Unlike the other plugin content archived with this plugin can still be available to non logged in visitors (depends on plugin settings) and just  removed from the front page and xml feeds (with a custom message can also be added to flag to visitors that the content is no longer up too date). Alternately it can be hidden entirely (to non logged in viewers).

= Can't I just trash old content I don't want anymore? =

Yes, there is nothing wrong with trashing old content. However it will be hidden from non logged in viewers.

However, WordPress automatically purges trashed posts every 7 days (by default), so it will be gone.

This is what makes the Archived post status handy. You can unpublish content without having to delete it forever.

= How can I view a listing of my archived content on its own archive pagelisting all archived posts, pages etc?

This not not part of my plugin per se but it is easily done.

The easiest way would be to install a plugin that allows you to query by post_status eg: https://wordpress.org/plugins/display-posts-shortcode/

and input the shortcode with the post_status of archive:, eg [display-posts post_status=”archive”]

If you want to customise the display that shortcode has plenty of arguments. There are also other shortcodes tha can do this (just search the repository).

= My archived posts have disappeared when I deactivate the plugin, why is this?

The reason is that wordpress no longer recognises them, but they are still in the database. If you no longer need the plugin, just reactivate it, switch all the archived posts/pages/cpts to a native post status and THEN deactivate the plugin.

= What if something does not work?  =
	
LH Archived Post Status, and all [https://lhero.org](LocalHero) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones).
	
If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfifeen, twentysixteen etc.

If the problem persists please leave a post in the support forum: [https://wordpress.org/support/plugin/lh-archived-post-status/](https://wordpress.org/support/plugin/lh-archived-post-status/). I look there regularly and resolve most queries.
	
= What if I need a feature that is not in the plugin?  =
	
Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/)

= What if this plugin does not work with XXX plugin?  =
	
The issue is likely is likely with the other plugin, all my lugins follw standards but may do not. Feel free to add a support issue and if I identify a bug I will fix it. But if its not a bug and you need my plugin to be compatible with another then contact outside teh forum for an enahncement.

= Is there a template function to including archiving functionality appropriately on the front end?  =

Yes the plugin defines the function archive_post_link which acts almost identically to the wordpress native function edit_post_link. That is clicking it will archive the relevant post is the current user has the edit_post capability. You can add it to you theme. 

== Installation ==

1. Upload the entire `lh-archived-post-status` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to Settings->Reading and set the visibility and archiving message



== Changelog ==

**0.01 February 12, 2015**  
* Initial release

**0.02 February 17, 2015**
* Added public/private option

**1.00 March 26, 2015**
* Added icons

**1.1 April 24, 2015**
* Added nonces

**1.2 June 15, 2015**
* Added settings

**1.30 July 17, 2015**
* Code improvements

**1.31 August 17, 2015**
* Minor code improvements

**1.40 March 10, 2016**
* Simpler codebase

**2.00 October 20, 2016**
* Handle post expiry

**2.10 November 05, 2016**
* Handle bulk actions

**2.11 November 11, 2016**
* Wp editor for archive message

**2.12 November 21, 2016**
* Added check for empty option

**2.13 December 27, 2016**
* Added fix for php warnings

**2.14 January 04, 2017**
* Better documentation

**2.15 January 27, 2017**
* Sticky post bug fix

**2.16 July 25, 2017**
* Don't use on attachments

**2.17 October 04, 2017**
* Various improvements

**2.18 October 08, 2017**
* Bug Fix

**2.19 October 10, 2017**
* Changed uninstall method to static

**2.20 November 27, 2017**
* Added message filter and disquss fix

**2.30 April 25, 2018**
* WP status library

**3.00 May 01, 2022**
* Repository release and code cleanup, any breaking changes let me know in the furms and I will address immediately

**3.01 May 14, 2022**
* Minor change for Advanced Ads compatibility

**3.02 May 18, 2022**
* Take 2 at working with plugins that do funky things to metaboxes

**3.03 May 31, 2022**
* Better translations support and updated library

**3.04 July 30, 2022**
* Moved library to latest

**3.05 August 01, 2022**
* Removed useless code

**3.06 November 06, 2022**
* Removed useless codeBasic wp super cache functionality

**3.10 October 10, 2023**
* bumped to latest wp-statuses library, fixed archive link, and defined a template tag for use in the front end