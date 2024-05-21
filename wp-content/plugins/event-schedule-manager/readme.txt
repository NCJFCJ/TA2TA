=== Event Schedule Manager ===
Contributors: theeventscalendar, bordoni, brianjessee, roadwarriorwp, alh0319, stevejonesdev
Tags: event schedule, event, events, conference schedule, conference, schedule, block, blocks, gutenberg, sessions, speakers, sponsors, multi-track, shortcode, events calendar
Requires at least: 6.2.3
Tested up to: 6.4.2
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add a custom event or conference schedule (in responsive table or grid format) to your website with either a Gutenberg block or a shortcode.

== Description ==

= Features =

* Simple & Intuitive
* Gutenberg Ready
* Custom schedule block
* Custom schedule shortcode
* Works with any post type
* Speakers shortcode
* Sponsors shortcode
* Table and Grid layouts
* Light and Dark mode
* A11Y compliant
* Documentation

= Links =

* [Website](https://theeventscalendar.com)
* [Documentation](https://theeventscalendar.com/knowledgebase/guide/getting-started-with-event-schedule-manager/)
* [Demo](https://theeventscalendar.com/#demo)
* [Event Schedule Manager](https://theeventscalendar.com/products/event-schedule-manager/)

== Installation ==

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit "Choose File."
3. When the popup appears select the event-schedule-manager-x.x.zip file from your desktop. (The 'x.x' will change depending on the current version number).
4. Follow the on-screen instructions and wait as the upload completes.
5. When it's finished, activate the plugin via the prompt. A message will show confirming activation was successful.
6. For access to new updates, make sure you have added your valid License Key under Events --> Settings --> Licenses. You can find your license key on your theeventscalendar.com account at https://evnt.is/3u.

That's it! Just configure your settings as you see fit, and you're on your way to creating events in style.

== Frequently Asked Questions ==

= What kind of events is this plugin good for? =

This plugin is intended for both multi-track and single-track events that would like to display their event schedule in an easy to read, tabular format. It can be used by conferences, trade shows, conventions, festivals, company retreats, online events, and even wedding weekends...anything you want!

= Is the schedule mobile responsive? =

Yes, the schedule is mobile responsive. At smaller device sizes, the table will break into individual blocks for each timeframe in your schedule. Please see screenshots for an example.

= Does this plugin have a Gutenberg block? What if I'm using the Classic Editor plugin? =

This plugin was built as a "Gutenberg-first" plugin and includes a block with configurable options. This makes it super easy to add a conference (or other event) schedule to any page or post on your website.

If you're not yet using Gutenberg or need to add your schedule, speakers or sponsors anywhere, you can use our shortcodes. [Learn More About the Shortcodes](https://theeventscalendar.com/knowledgebase/shortcodes-custom-fields-and-setting-options/)

= What are the shortcodes? What options do they include? =

The base shortcode to display your schedule without using the included Gutenberg block is [tec_schedule], for speakers it is [tec_speakers], and for sponsors it is [tec_sponsors]. For full documentation and available shortcode attributes/options please [visit our shortcode documentation](https://theeventscalendar.com/knowledgebase/shortcodes-custom-fields-and-setting-options/).

= Can I add custom colors/font sizes to the schedule? =

The plugin includes coloring for both a light and dark schedule, as shown in the plugin [demo](https://theeventscalendar.com/#demo). You can easily toggle between these two color modes with both our Gutenberg block and shortcode.

We have included lots of helper classes in the schedule in rows and individual sessions, which will allow you to recolor the schedule as desired via CSS in your theme or the WordPress customizer. If you need help recoloring the schedule or adjusting font sizes, you can [get in touch with our support team](https://theeventscalendar.com/support/).

= Is this multi-site compatible? =

Yes! This plugin is built with multi-site in mind and is/has been tested in a multi-site environment.

= Do you provide support? =

We provide support for this plugin via the The Events Calendar Premium Support (https://theeventscalendar.com/support/). This plugin is central to our business and we're committed to providing quality support.

If you would like to request additional features, you can [submit a customization request here](https://app.loopedin.io/the-events-calendar-suite-roadmap#/ideas).

== Changelog ==

= 1.1.0 2024-01-24 =

* Tweak - Change Event Schedule block date picker to select multiple dates. [ESM-48]
* Tweak - Add no sessions message, remove styles, change block name to Event Schedule, add align and content options, add date label and helper text, add a block description, and update block icon for the Event Schedule block. [ESM-53]
* Fix - Prevent fatal when moving attendees in Event Tickets and Event Tickets Plus by updating the Uplink library to 1.3 [ESM-70]
* Fix - On single session editor show View Sessions in admin bar and under New dropdown. [ESM-58]
* Language - 42 new strings added, 27 updated, 4 fuzzied, and 5 obsoleted

= [1.0.0] 2023-11-13 =

* Feature - Initial release of Event Schedule Manager.
* Feature - add speaker and sponsor custom post types.
* Feature - add speaker groups and sponsor levels taxonomies.
* Feature - add [tec_speakers] and [tec_sponsors] shortcodes.
* Tweak - update settings options.
* Tweak - update styling for all shortcodes and single templates.
* Tweak - remove Freemius and use Stellar Telemetry.
* Tweak - update the admin menu display to combine all custom post types, taxonomies, and settings under one menu.
* Fix - block editor for schedule block and change to use native html5 datepicker.

= 0.1.12 =
* Fix cmb2 activation conflict

= 0.1.11 =
* Grid Height Parameter
* Pro Plugin Support
* Meta Field Updates
* Minor bug fixes
* Freemius Updated

= 0.1.10 =
* Freemius Updated, minor bug fixes

= 0.1.9 =
* Added a custom ID to schedules with the Grid layout and used that for the generated CSS to allow multiple Grid layout schedules on the same page
* Fixed an error in the Block Editor preview showing Invalid parameter(s): attributes. Block Editor previews now show properly

= 0.1.8 =
* Freemius Updated

= 0.1.7 =
* Minor styles changes
* Track taxonomy output as name
* Unlink track terms on session single

= 0.1.6 =
* Added settings page
* Added setting to show the WP Conference schedule link
* Added setting to define schedule page URL
* Added opt in analytics
* Added return to schedule link on single session
* Added grid layout
* Added grid layout select to block inspector controls

= 0.1.5 =
* Updated session link type anchor, to point to the current page rather than the single session page

= 0.1.4 =
* Added filter wpcs_session_content_footer

= 0.1.3 =
* Fixed location taxonomy slug

= 0.1.2 =
* Prevent PHP errors when shortcode doesn't have any attributes

= 0.1.1 =
* Added end time to sessions
* Added locations custom post type and output to sessions single template
* Added block alignment

= 0.1.0 =
* This is version 0.1.0 - everything is new and shiny!
