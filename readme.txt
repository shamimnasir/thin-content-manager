=== Thin Content Manager ===
Contributors: msfreed
Plugin URI: http://pluginspire.com/
Tags: robots.txt, robots, robot, search, google, meta, meta tags, head, head section, thin content, word count, noindex, nofollow, exclusion file, block pages, disallow pages
Requires at least: 3.1
Tested up to: 3.7.1
Stable tag: trunk
Version: 1.0.1
License: GPLv3

See the body word count to identify pages with thin content, then select pages to insert robots noindex,nofollow tags into.

== Description ==
Thin Content Manager helps you easily identify thin content pages (i.e. posts and pages with minimal textual content) on your site and block them from search engines. The plugin calculates the body word count for each post/page on your site (disregarding words contained within HTML tags and shortcodes) and displays the information on a new 'Thin Content Manager' page within the Settings section of your WordPress admin panel. The admin page then provides a simple point-and-click interface to automatically insert robots noindex and nofollow tags - &lt;meta name="robots" content="noindex, nofollow"&gt; - into the head section of pages you select. These tags tell Google and other search engine spiders to ignore those pages, which is important for improving your site's quality score and trust ranking for SEO purposes.

This plugin works with with single site installs only.

== Installation ==
### Install through the Wordpress Admin

* It is recommended that you use the built in Wordpress installer to install plugins.
	* Dashboard > Plugins Menu > Add New Button
* In the Search box, enter: thin content
* Find the Plugin "Thin Content Manager"
* Click Install Now and proceed through the plugin setup process.
	* Activate the plugin when asked.
	* If you have returned to the Plugin Admin, locate the "Thin Content Manager" Plugin and click the Activate link.

### Upload and Install

* If uploading, upload the /thin-content-manager/ folder to /wp-content/plugins/ folder for your Worpdress install.
* Then open the Wordpress Admin:
	* Dashboard > Plugins Menu
* Locate the "Thin Content Manager" Plugin in your listing of plugins. (sort by Inactive)
* Click the Activate link to start the plugin.

== Frequently Asked Questions ==

== Screenshots ==
1. Thin Content Manager automation settings and tag manager

== Changelog ==
= 1.0.1 =
Updated word count function to correctly handle shortcodes.

= 1.0.0 =
Initial release.
