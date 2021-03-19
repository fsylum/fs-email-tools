<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\WP\Option;
use Fsylum\EmailTools\WP\Database;
use Fsylum\EmailTools\Contracts\Service;
use Fsylum\EmailTools\Factories\LogFactory;

class Cron implements Service
{
    const HOOK_NAME = 'fs_email_tools_cron_delete_expired_logs';

    public function run()
    {
        add_action(self::HOOK_NAME, [$this, 'deleteExpiredLogs']);
        //add_filter('cron_schedules', [$this, 'modifyCronSchedules']);
    }

    public function modifyCronSchedules($schedules)
    {
        $schedules['fs_test'] = [
            'interval' => 10,
            'display'  => __('FS Email Tools Cron Schedule Test')
        ];

        return $schedules;
    }

    public function deleteExpiredLogs()
    {
        global $wpdb;

        $option = Option::get();

        if ((bool) $option['log']['keep_indefinitely']) {
            return;
        }

        $table    = $wpdb->prefix . Database::TABLE;
        $days     = absint($option['log']['keep_in_days']);
        $end_date = wp_date(get_option('date_format'), time() - ($days * DAY_IN_SECONDS));
        $logs     = (new LogFactory(['end_date' => $end_date], 1, 9999))->get();
        $log_ids  = implode(',', array_map('absint', wp_list_pluck($logs['items'], 'id')));

        $wpdb->query("DELETE FROM $table WHERE id IN($log_ids)");
    }

    public static function install()
    {
        if (!wp_next_scheduled(self::HOOK_NAME)) {
            wp_schedule_event(time(), 'daily', self::HOOK_NAME);
        }
    }

    public static function uninstall()
    {
        wp_clear_scheduled_hook(self::HOOK_NAME);
    }
}
