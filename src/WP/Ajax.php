<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\Models\Log;
use Fsylum\EmailTools\Contracts\Service;

class Ajax implements Service
{
    public function run()
    {
        add_action('wp_ajax_fs_email_tools_get_email_log', [$this, 'getEmailLog']);
    }

    public function getEmailLog()
    {
        $log = (new Log)->find(absint($_REQUEST['id']));

        if (empty($log->data())) {
            return;
        }

        $log->markAsRead();
        $log->fetch();

        wp_send_json_success($log->data());
    }
}
