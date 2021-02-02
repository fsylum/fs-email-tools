<?php

/**
 * Plugin Name:         Email Tools
 * Plugin URI:          https://github.com/fsylum/wp-email-tools
 * Description:         Collection of tools to interact with emails in WordPress including email rerouting, outgoing email logging to database and selectively disabling WordPress default emails.
 * Author:              Firdaus Zahari
 * Author URI:          https://fsylum.net
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         fs-email-helper
 * Domain Path:         /languages
 * Version:             0.1.0
 * Requires at least:   5.5
 * Requires PHP:        7.3
 */

require __DIR__ . '/vendor/autoload.php';

define('FS_EMAIL_TOOLS_PLUGIN_URL', untrailingslashit(plugin_dir_url(__FILE__)));
define('FS_EMAIL_TOOLS_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

$plugin = new Fsylum\EmailTools\Plugin;

$plugin->addService(Fsylum\EmailTools\WP\Mail::class);
$plugin->addService(Fsylum\EmailTools\WP\Asset::class);
$plugin->addService(Fsylum\EmailTools\WP\Admin\Page::class);
$plugin->addService(Fsylum\EmailTools\WP\Admin\Settings::class);

$plugin->run();
