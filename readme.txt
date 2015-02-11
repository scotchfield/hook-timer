=== Hook Timer ===
Contributors: sgrant
Donate link: http://scotchfield.com
Tags: hook, hooks, action, actions, filter, filters, time, timing
Requires at least: 4.0
Tested up to: 4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Track the time used between the start and end of each hook (action or filter).

== Description ==

Hook Timer attaches to the start and end of each action and filter, stores
the timestamp, and when the hook finishes, logs the total time spent. If
you want to examine why a page is slow or aren't sure where the majority of
your page load time is spent, this plugin will show you the most costly
hooks in your installation.

Hook Timer appears as a menu option in your admin dashboard, and displays
the total time spent in each hook on the previous page load. This allows
you to load a particular page that's slow, then to view the results in the
admin dashboard immediately after.

== Installation ==

Place all the files in a directory inside of wp-content/plugins (for example,
hook-timer), and activate the plugin.

You can find the admin page under Settings, titled Hook Timer.

== Frequently Asked Questions ==

= Where can I find the Hook Timer results? =

You can find the Hook Timer page in the admin dashboard, under Settings,
titled Hook Timer.

== Screenshots ==

1. A demonstration of the plugin, listing the hooks from the previous
page load, with the number of seconds spent inside each one.

== Changelog ==

= 1.0 =
* First release!

== Upgrade Notice ==

= 1.0 =
First public release.
