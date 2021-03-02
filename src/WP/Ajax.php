<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\Contracts\Service;

class Ajax implements Service
{
    public function run()
    {
        add_action('wp_ajax_fs_email_tools_get_email_log', [$this, 'getEmailLog']);
    }

    public function getEmailLog()
    {
        wp_send_json_error('OK');
    }
}
