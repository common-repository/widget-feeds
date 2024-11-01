=== Feeds Widget ===
Contributors: ketsugi
Donate link: http://ketsugi.com/paypal_donate.php?plugin=WordPress%20-%20Feeds%20Widget%20Plugin
Tags: widgets, rss, feed
Requires at least: 2.1
Tested up to: 2.5
Stable tag: 1.3

A widget to display links to various context-sensitive RSS feeds.

== Description ==

This WordPress widget will display links various relevant feeds for your WordPress blog. It will always at least display links to the two standard feeds: All Entries and All Comments. It will also display links to category feeds, post feeds or search feeds, depending on the current page being viewed.

Feeds Widget will display feed links either with the usual “http://” protocol or the optional “feed://” protocol and can also be configured to display an image (such as a feed icon) before each link.

== Installation ==

1. Download the zip file (link below)
2. Extract widget-feeds.php
3. Upload widget-feeds.php to your wp-content/plugins directory
4. Log in to your WordPress blog
5. Click on “Plugins”
6. Locate the “Feeds Widget” plugin and click “Activate”
7. Go to “Presentation” > “Sidebar Widgets” and add the Feeds widget to your sidebar.

== Frequently Asked Questions ==

= How do I set/change the feed icon? =

You can configure the feed icon in the widget's options. Alternatively, use CSS to style your lists with this sample code:

`#feeds li {
  background: url(images/feed.png) top left no-repeat;
  padding-left: 18px;
}`

Where `images/feed.png` should be the full path of the image to use, or the relative path from the CSS file in which this is defined.

== Change Log ==

* 1.3 - 28 September 2007
   * [CHG] Cruft-free URL detection for tag pages updated for WordPress 2.3 compatibility
   * [ADD] Auto-discovery feed links now included in <head> element for post, search and tag pages
* 1.2 - 15 Jan 2007
   * [ADD] Option to specify a feed icon to be placed before links
* 1.1.1 - 4 Aug 2006
   * [FIX] Forgot closing PHP tag
* 1.1 - 14 Jul 2006
   * [ADD] Show feed URLs for tag pages (currently supported: UltimateTagWarrior plugin)
   * [FIX] Properly escape search feed URLs to prevent validation errors
* 1.0 - 14 Jul 2006
   * Initial release