<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\Service;
use Fsylum\EmailTools\WP\Admin\Settings;

class Asset extends Service
{
    const KEY     = 'fs-email-tools-asset';
    const VERSION = '0.1.0';

    public function run()
    {
        add_action('admin_enqueue_scripts', [$this, 'loadAssets']);
    }

    public function loadAssets($hook)
    {
        if ($hook !== 'tools_page_' . Settings::KEY) {
            return;
        }

        wp_enqueue_script(
            self::KEY . '-js-app',
            FS_EMAIL_TOOLS_PLUGIN_URL . '/assets/dist/js/app.js',
            ['jquery'],
            wp_get_environment_type() === 'production' ? self::VERSION : time()
        );
    }
}
