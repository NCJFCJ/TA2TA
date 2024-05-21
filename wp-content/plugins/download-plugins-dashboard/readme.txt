=== Download Plugins and Themes from Dashboard ===
Contributors: wpcodefactory, omardabbas, karzin, anbinder, algoritmika, kousikmukherjeeli
Tags: download, plugin, theme, zip, dashboard
Requires at least: 3.1
Tested up to: 6.3
Stable tag: 1.8.5
Requires PHP: 5.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Download installed plugins and themes ZIP files directly from your admin dashboard without using FTP.

== Description ==

**Download Plugins and Themes from Dashboard** plugin lets you download installed plugins and themes ZIP files directly from your admin dashboard without using FTP.

There are no required settings in plugin - after installation **Download ZIP** links will be automatically added to all:

* plugins - to your *Plugins > Installed Plugins* menu, and
* themes - to your *Appearance > Themes* menu.

Also there are **Download all** plugins and/or themes tools in "Settings > Download Plugins and Themes".

Additionally in "Settings > Download Plugins and Themes" you can set if you want to append version number to ZIP filename, and/or if you want main plugin's or theme's directory to be included in ZIP.

Advanced settings include selecting different ZIP libraries (ZipArchive or PclZip), and setting custom temporary directory on your server.

[Pro version](https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/) has option to set **periodical** plugins and/or themes **downloads**.

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/download-plugins-and-themes-from-dashboard-wordpress-plugin/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. "Download ZIP" links will be automatically added to all plugins to your "Plugins > Installed Plugins" menu and to all themes to your "Appearance > Themes" menu.

== Screenshots ==

1. Plugin "Download ZIP" links in "Plugins > Installed Plugins".
2. Theme "Download ZIP" links in "Appearance > Themes".
3. Settings.

== Changelog ==

= 1.8.5 - 25/09/2023 =
* Update plugin icon, banner.

= 1.8.4 - 19/09/2023 =
* Fix - PHP Warnings are triggering on PHP version 8.2.
* Tested up to: 6.3.

= 1.8.3 - 18/06/2023 =
* Tested up to: 6.2.

= 1.8.2 - 10/03/2023 =
* Tested up to: 6.1.

= 1.8.1 - 17/11/2021 =
* Dev - Add github deploy setup.
* Tested up to: 5.8.

= 1.8.0 - 26/06/2021 =
* Fix - Checking for the activated Pro plugin version correctly now.
* Dev - "Send file" headers updated.
* Dev - Code refactoring.
* Description in readme.txt updated.

= 1.7.2 - 04/06/2021 =
* Tested up to: 5.7.
* Plugin author updated.

= 1.7.1 - 15/02/2021 =
* Dev - JS file minified.
* Dev - Localization - `load_plugin_textdomain()` moved to the `init` hook.
* Tested up to: 5.6.

= 1.7.0 - 20/03/2020 =
* Dev - Better system temp directory retrieving algorithm: checking `upload_tmp_dir`, `wp_upload_dir()` and `open_basedir` now (if the default `sys_get_temp_dir()` is not writable).
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.
* Tested up to: 5.3.

= 1.6.0 - 30/09/2019 =
* Fix - Settings - Security - Checking for user capability and nonce. Sanitizing and escaping data.
* Dev - Settings - "increase your WP memory limits" link updated.

= 1.5.0 - 23/07/2019 =
* Fix - "Single File" plugins download fixed (except in "Periodical" downloads).
* Fix - "Must-Use" and "Drop-in" plugins support added (except in "Periodical" and "All" downloads).

= 1.4.3 - 08/07/2019 =
* Dev - Advanced Settings - "Temporary directory" option added.
* Tested up to: 5.2.

= 1.4.2 - 19/04/2019 =
* Dev - "Tested up to" updated.

= 1.4.1 - 09/12/2018 =
* Dev - Advanced Settings - "ZIP library" option added.
* Dev - More info added in `create_zip()` error message.

= 1.4.0 - 27/09/2018 =
* Dev - "Download all" plugins/themes tools added.
* Dev - Checking for `zlib` extension availability.
* Dev - Checking system requirements only when download link is clicked.
* Dev - Deleting zip file (if exists) before creating new one.
* Dev - Pro version link (and "Periodical Downloads" section) added.
* Dev - Code refactoring.

= 1.3.0 - 18/03/2018 =
* Dev - `PclZip` fallback zip archivation library added.
* Dev - "Settings" action link added.
* Dev - Code refactoring.
* Dev - "Requires PHP" added to readme.txt.

= 1.2.0 - 11/06/2017 =
* Dev - "Add main directory to ZIP" options added.
* Dev - "Append version number to ZIP filename" options added.
* Dev - Code refactoring.

= 1.1.3 - 09/06/2017 =
* Dev - Zip files deletion from temporary folder after successful download added.

= 1.1.2 - 25/03/2017 =
* Fix - `load_plugin_textdomain` moved from `init` hook to constructor.
* Dev - System requirements error message updated.
* Dev - Language (POT) file updated.
* Dev - Plugin header info ("Text Domain" etc.) updated.
* Dev - Donate link updated.

= 1.1.1 - 10/11/2016 =
* Fix - For portability now only forward slashes (/) are used as directory separator in ZIP filenames.

= 1.1.0 - 05/10/2016 =
* Dev - Themes download functionality added (and plugin renamed).
* Dev - Plugins download action moved to from `plugins_loaded` to `admin_init` hook.
* Dev - More validation added to plugins download action.
* Dev - Using single `plugin_action_links` hook to add download action links, instead of using separate hook for each plugin.
* Dev - Checking for `ZipArchive` and `RecursiveIteratorIterator` classes to exist.
* Dev - Language (POT) file added.
* Dev - Screenshots added.
* Dev - Icons and banners added.

= 1.0.0 - 28/09/2016 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
