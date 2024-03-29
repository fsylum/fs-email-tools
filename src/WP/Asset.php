<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\WP\Admin\Page;
use Fsylum\EmailTools\Contracts\Service;

class Asset implements Service
{
    const KEY = 'fs-email-tools-asset';

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
            self::KEY . '-js-admin',
            FS_EMAIL_TOOLS_PLUGIN_URL . '/assets/dist/js/admin.js',
            ['underscore', 'jquery', 'jquery-ui-datepicker', 'jquery-ui-dialog', 'wp-util', 'wp-i18n'],
            wp_get_environment_type() === 'production' ? FS_EMAIL_TOOLS_VERSION : time()
        );

        wp_enqueue_style(
            self::KEY . '-css-admin',
            FS_EMAIL_TOOLS_PLUGIN_URL . '/assets/dist/css/admin.css',
            ['wp-jquery-ui-dialog'],
            wp_get_environment_type() === 'production' ? FS_EMAIL_TOOLS_VERSION : time()
        );

        wp_set_script_translations(self::KEY . '-js-admin', 'fs-email-tools');
    }
}
