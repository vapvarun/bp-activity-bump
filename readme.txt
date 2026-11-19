=== BP Activity Bump ===
Contributors: wbcomdesigns
Tags: activity bump
Requires at least: 5.0
Requires PHP: 8.1
Tested up to: 7.1
Stable tag: 1.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin is used to bump activity when anyone likes activity or comment on activity.

== Description ==

This plugin basically provides you the ability to bump activity on comment or like on activity. 

== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to WB Plugins > Activity Bump > General and choose what bumps an activity.

== Changelog ==
= 1.2.5 - November 2026 =

* Fix      - A bumped post's original time keeps refreshing live ("x ago") like every other post.
* Fix      - The stream now reorders right after a bump on sites with a persistent object cache (Redis, Memcached).
* Fix      - Liking a comment no longer changes that comment's date; only top-level activities are bumped.
* Fix      - The "Settings saved." notice is visible again after saving the Bump On option.
* Fix      - The Bump On setting shows its default (Both) on a fresh install and only accepts known values.
* Fix      - Bumping works with the BuddyPress REST API and BuddyBoss reactions; the single activity page keeps BuddyPress's own timestamp markup.
* Improve  - Bump hooks register only when the Activity component is on, and the plugin stays active, with a notice, while BuddyPress is deactivated.
* Improve  - Removed an unused activity query that ran on every bump; the bump is one update by activity ID.
* Improve  - The Themes & Extension page loads its own icons instead of a third-party font from a CDN.
* Security - The wbcom extensions AJAX handler now requires the manage_options capability.
* Dev      - Removed the bundled update checker; updates come from GitHub releases.
* Dev      - Added bin/build-release.sh, bin/local-ci.sh and .distignore; admin CSS uses design tokens and PHP 8.1 is the minimum.

= 1.2.4 =
* Fix: (#32) Issue with php8.2
* Fix: PHPCS fixes

= 1.2.3 =
* Fix: (#31) Fatal error on saving setting
* Fix: (#30)Setting messages issue. 
* Fix: Distorted admin options issue

= 1.2.2 =
* Fix: Fixed Plugin redirect issue when multi plugin activate the same time
* Fix: Added activity bump data in rest api activity endpoint
* Fix: Fixed plugin activation issue

= 1.2.0 =
* New Feature: Added wbcom wrapper and admin setting
* New Feature: Added plugin updater files
* Fix: (#7) Fixed Notice & Warning issue

= 1.1.0 =
* Fix: #8 Youtube embeded video not getting with embeded after bump
* Fix: #7 Fixed Image issue with BuddyPress rtMedia Plugin 

= 1.0.1 =
* BP 4.3.0 compatibility check.

= 1.0.0 =
This is initial version.

== Upgrade Notice ==

= 1.0.0 =
This is initial version.
