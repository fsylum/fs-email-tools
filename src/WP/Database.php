<?php

namespace Fsylum\EmailTools\WP;

class Database
{
    const KEY     = 'fs_email_tools_db_version';
    const TABLE   = 'fs_email_logs';
    const VERSION = 1;

    public static function install()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table           = $wpdb->prefix . self::TABLE;

        $sql = "CREATE TABLE {$table} (
            id bigint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
            recipients_to tinytext NOT NULL,
            recipients_cc tinytext DEFAULT NULL,
            recipients_bcc tinytext DEFAULT NULL,
            subject tinytext NOT NULL,
            message longtext NOT NULL,
            attachments longtext DEFAULT NULL,
            created_at datetime DEFAULT NOW() NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
        update_option(self::KEY, self::VERSION);
    }
}
