<?php
/**
 * Plugin Name:         Email Tools
 * Plugin URI:          https://github.com/fsylum/fs-email-tools
 * Description:         Collection of tools to interact with emails in WordPress including email rerouting, outgoing email logging to the database, and automatic BCC to specified email address.
 * Author:              Firdaus Zahari
 * Author URI:          https://fsylum.net
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Version:             1.2.0
 * Requires at least:   5.6
 * Requires PHP:        7.3
 */

require __DIR__ . '/vendor/autoload.php';

define('FS_EMAIL_TOOLS_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('FS_EMAIL_TOOLS_PLUGIN_URL', untrailingslashit(plugin_dir_url(__FILE__)));
define('FS_EMAIL_TOOLS_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('FS_EMAIL_TOOLS_VERSION', '1.2.0');

register_activation_hook(__FILE__, [Fsylum\EmailTools\WP\Database::class, 'install']);
register_activation_hook(__FILE__, [Fsylum\EmailTools\WP\Cron::class, 'install']);
register_deactivation_hook(__FILE__, [Fsylum\EmailTools\WP\Cron::class, 'uninstall']);

$plugin = new Fsylum\EmailTools\Plugin;

$plugin->addService(new Fsylum\EmailTools\WP\Ajax);
$plugin->addService(new Fsylum\EmailTools\WP\Cron);
$plugin->addService(new Fsylum\EmailTools\WP\Mail);
$plugin->addService(new Fsylum\EmailTools\WP\Asset);
$plugin->addService(new Fsylum\EmailTools\WP\Admin\Page);
$plugin->addService(new Fsylum\EmailTools\WP\Admin\Settings);

$plugin->run();
