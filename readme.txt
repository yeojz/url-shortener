=== URL Shortener ===
Contributors: geraldyeo
Donate link: http://wiki.fusedthought.com/contribute/
Tags: url-shortener, short url, url, shortlink, shorten, shortener, tinyurl, is.gd, su.pr, bit.ly, tr.im, short.ie, snipurl, snurl, sn.im, cl.lk, cl.gs, ping.fm, chilp.it, smoosh, smsh.me, u.nu, unfake.it, awe.sm, social, tweet, twitter, soso.bz, digg, Voizle, tynie
Requires at least: 2.7
Tested up to: 3.0.2
Stable tag: trunk

This plugin allows you to generate shortlinks for post/pages using URL Shorteners (e.g. Bit.ly, Su.pr, ping.fm, Digg and many others).

== Description ==

*Version 3.0 is completely re-written. For those updating from previous versions, please check your settings again.*

[URL Shortener](http://www.fusedthought.com/downloads#url-shortener-wordpress-plugin "URL Shortener") allows you to generate shortlinks for post/pages using URL Shorteners (e.g. Bit.ly, Su.pr, ping.fm, Digg and many others).

Features:

* Automatic generation of a Short URL/Shortlinks
* *Cached Shortlink* - thus generated only once. 
* Choose to generate shortlinks using permalinks or the posts ID (e.g. http://your_site/index.php?p=123).
* Relatively extensive shortlink support
* *Action Hooks available* for other plugins to utilize generated shortlinks (From Ver 3.0 Onwards)
* Nice ID links - http://your_site/123 instead of http://your_site/index.php?p=123
* Shortcode support (Ver 3.1): Place [shortlink] in your article where you want to display the shortened url.
* Append a link to short URL below your post content (Ver 3.1.1)

Refer to the documentation/wiki page at http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin for more information (eg. installation guide and known issues etc).


**Services currently supported are:**

* bit.ly
* Su.pr
* tinyurl
* Ping.fm
* Digg
* is.gd
* snipurl / Snurl / Snipr / Sn.im / Cl.lk

as well as:

* cl.gs
* short.ie 
* chilp.it
* smsh (aka sm00sh)
* urli.nl
* unfake.it 
* awe.sm
* Voizle 
* soso.bz
* Interdose API
* Cuthut.com

**Available Template Tags**

On-demand shortening function:

`<?php fts_shorturl('http://www.google.com', 'bitly'); ?>`

To show the generated links::

`<?php fts_show_shorturl($post); ?>`

Or if WordPress 3.0:

`<?php the_shortlink(); ?>`
http://codex.wordpress.org/Function_Reference/the_shortlink


**Available hooks and filters**

*  fts_use_shortlink (Action Hook)
*  fts_filter_shortlink (Filter)

**Future Versions:**

*  More services can be added upon request (http://code.google.com/p/url-shortener-plugin/issues/list)

**Support via:**

*  http://wordpress.org/tags/url-shortener
*  Contact me via my website ( http://www.fusedthought.com/contact/ )

**Notes:**
Ver 3.0 is a major re-write, do report any bugs that you may find. Thanks.

== Installation ==

1. Upload files to your `/wp-content/plugins/` directory (preserve sub-directory structure if applicable)
1. Activate the plugin through the 'Plugins' menu in WordPress

Or

1. Within your WordPress admin, go to plugins > add new
1. Search for "URL Shortener". 
1. Click Install Now for the plugin named "URL Shortener"


== Screenshots ==

http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin

== Changelog ==

= 3.1.1 =
* Minor tweak to Admin Interface / categorization of options
* Minor tweak to generation of Short URL when scheduled posts are published.
* Ability to append a link to short URL to bottom of post/page content.

= 3.1 =
* *BUGFIX* Compatibility with Plugins using Services_JSON (http://code.google.com/p/url-shortener-plugin/issues/detail?id=6)
* *UPDATED* class.FTShorten to version 2.3 (Support for interdose API - http://code.google.com/p/url-shortener-plugin/issues/detail?id=7)
* *REMOVED* u.nu service has been discountinued. Service thus removed.
* *ADDED* Shortcode [shortlink] support

= 3.0 =
* Plugin completely rewritten from gound up.
* *UPDATED* to use WordPress 3.0 Hooks
* *UPDATED* class.FTShorten to version 2.2 (with support for new services)
* *ADDED* support for Digg Short URLs
* *ADDED* Action hooks to easily add code to utilize shortlink after generation
* *ADDED* option to use permalinks or Post/Page ID to generate shortlinks
* *REMOVED* services that no longer available.
* *REMOVED* wp_rewrite redirect
* Addon module support halted temporarily
* Admin Interface redesigned.

= 2.1.1 =
* Bugfix: Twitter User/Pass handling
* Bugfix: Cli.gs non-authenticated processing

= 2.1 =
* AJAX-ed entire Bulk Short URL code.
* Improved Security of Bulk Short URL Request.
* Included updated class.FTShorten (v2.0)
* Added support for auto-updating twitter (post name / url)

= 2.0.1 =
* Bugfix: Minor error posting to ping.fm

= 2.0 =
* Short URLs now generated using Post/Page IDs instead of Permalinks ensuring correct redirection even if post title/permalink changes.
* Bugfix: Blank screen when Shorten2Ping plugin is also activated. If Shorten2Ping is activated, URL Shortener will now detect and use the URL generated by Shorten2Ping. (http://code.google.com/p/url-shortener-plugin/issues/detail?id=2)
* Added support (via Addon Module) for Digg
* Added support request (via Addon Module) for Voizle (http://code.google.com/p/url-shortener-plugin/issues/detail?id=1)
* Added ability to generate and delete Short URL in bulk.
* Ported the URL Generation functions into a class, allowing re-use in other plugins.
* Sn.im service is found to be down and marked to be removed in the next version of this plugin.

= 1.7 =
* Included an Addon module option
* Addon functions for additional service display
* Added support (via Addon Module) for Goo.gl
* Directory structure cleanup


= 1.6.3 =
* WordPress 2.9 Compatibility check.
* If WordPress.com stats plugin enabled, "Show Short URL" button in edit page beside "view" is removed.

= 1.6.2 =
* Added prefix choosing support for Sn.im / Snipr / Snipurl / Snurl

= 1.6.1 =
* Bugfix: future/scheduled posts not generating Short URL.

= 1.6 =
* Added support for Awe.sm (user request)
* Changed URL Generation method hook for future/scheduled posts

= 1.5.2 =
* Bugfix: Pingfm key not saving.

= 1.5.1 =
* Bugfix: Short URL generated was the same as post URL

= 1.5 =
* Added on-demand shortening function: fts_shorturl()
* Added supported for ping.fm, chilp.it, short.to, sm00sh, u.nu, unfake.it  
* Added personal shortening service using post id (http://yoursite/POST-ID)
* Added Prefix option for personal shortening service (http://yoursite/prefix/POST-ID)
* Added template redirection and WP_Rewrite redirection methods
* Updated administration options page

= 1.4 =
* First Public Release
* Added simple validation to options page

= 1.3 =
* Added support for snipurl, cl.gs, Short.ie

= 1.2 =
* Added support for su.pr

= 1.1 =	
* Added support for bit.ly, tr.im
* Added "Remove buttons" in post/page edit.
* Added option for automatic shorturl generation.
* Changed Custom Field name from fts_shorturl to shorturl

= 1.0 =
* Initial Private release.
* supports TinyURL, is.gd

== Upgrade Notice ==

For those upgrading from a previous version to 3.0, please check your settings as Version 3.0 was completely re-written.

Read More: http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin/upgrade-notes
