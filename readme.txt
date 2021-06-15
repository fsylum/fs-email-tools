=== FS Email Tools ===
Contributors: fsylum
Tags: email, mail, developer tool
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 1.2.3
Requires PHP: 7.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Collection of tools to interact with emails in WordPress including email rerouting, outgoing email logging to the database, and automatic BCC to specified email address.

== Description ==

The plugin provides a number of tools to modify the behaviour of outgoing emails from your site. This is particularly useful if you are trying to debug any email issues that are happening.

Features that this plugin offers includes:

* Automatic rerouting for all outgoing emails to any number of the specified email address.
* Logging outgoing emails into the database to be viewed later.
* Adding specified email addresses as BCC for all outgoing emails.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/fs-email-tools` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Tools->Email Tools screen to configure the plugin

== Screenshots ==

1. Plugin settings page to customize the outgoing email behaviour
2. Listing for the stored email logs in the database
3. Preview of the stored email log
4. UI to send test email

== Changelog ==

**1.2.3**

* [FIXED] Minor code formatting fix
* [FIXED] Implement i18n to string in Javascript files

**1.2.2**

* [FIXED] Exclude Docker related files from the final plugin files

**1.2.1**

* [FIXED] Fixed Github Actions deploy script (unrelated to the actual plugin)

**1.2.0**

* [FIXED] Fatal error when activating the plugin due to missing `vendor` directory

**1.1.3**

* [FIXED] Code cleanup

**1.1.2**

* [FIXED] Fixed Github Actions deploy script (unrelated to the actual plugin)

**1.1.1**

* [FIXED] Removed unneeded source files from the plugin

**1.1.0**

* [ADDED] Added link to the Settings page in the Plugins page

**1.0.0**

* Initial release
