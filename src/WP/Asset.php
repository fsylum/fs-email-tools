<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\WP\Admin\Page;
use Fsylum\EmailTools\Contracts\Service;

class Asset implements Service
{
    const KEY     = 'fs-email-tools-asset';
    const VERSION = '0.1.0';

    public function run()
    {
        add_action('admin_enqueue_scripts', [$this, 'loadAssets']);
    }

    public function loadAssets(string $hook)
    {
        if ($hook !== 'tools_page_' . Page::KEY) {
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
