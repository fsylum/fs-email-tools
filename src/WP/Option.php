<?php

namespace Fsylum\EmailTools\WP;

class Option
{
    const KEY = 'fs_email_tools_options';

    public static $defaults = [
        'reroute' => [
            'status'     => false,
            'recipients' => [],
            'append'     => [
                'status'   => false,
                'location' => 'subject',
            ],
        ],
        'log' => [
            'status'            => false,
            'keep_indefinitely' => false,
            'keep_in_days'      => 7,
        ],
        'bcc' => [
            'status'     => false,
            'recipients' => [],
        ],
    ];

    public static function get()
    {
        return array_replace_recursive(self::$defaults, get_option(self::KEY, []));
    }

    public static function isCurrentlyActive()
    {
        $option = self::get();

        return $option['reroute']['status'] || $option['log']['status'] || $option['bcc']['status'];
    }
}
