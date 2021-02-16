<?php

namespace Fsylum\EmailTools\Factories;

use DateTime;
use DateTimeZone;
use Fsylum\EmailTools\WP\Database;

class LogFactory
{
    public function __construct(array $args = [], $page = 1, $per_page = 10)
    {
        $args = wp_parse_args($args, [
            's'          => '',
            'orderby'    => 'created_at',
            'order'      => 'DESC',
            'start_date' => false,
            'end_date'   => false,
        ]);

        if (!empty($args['start_date'])) {
            $args['start_date'] = DateTime::createFromFormat(get_option('date_format'), $args['start_date'], wp_timezone())->setTimezone(new DateTimeZone('UTC'));
        }

        if (!empty($args['end_date'])) {
            $args['end_date'] = DateTime::createFromFormat(get_option('date_format'), $args['end_date'], wp_timezone())->setTimezone(new DateTimeZone('UTC'));
        }

        $args['orderby'] = in_array($args['orderby'], ['subject', 'created_at']) ? $args['orderby'] : 'created_at';
        $args['order']   = in_array($args['order'], ['asc', 'desc']) ? strtoupper($args['order']) : 'DESC';

        $this->args     = $args;
        $this->page     = absint($page);
        $this->per_page = absint($per_page);

        return $this;
    }

    public function get()
    {
        global $wpdb;

        $start  = ($this->page - 1) * $this->per_page;
        $table  = $wpdb->prefix . Database::TABLE;
        $wheres = [];

        if (!empty($this->args['s'])) {
            $wheres[] = $wpdb->prepare(
                '(recipients_to LIKE %s OR subject LIKE %s OR message LIKE %s)',
                '%'. $wpdb->esc_like($this->args['s']) . '%',
                '%'. $wpdb->esc_like($this->args['s']) . '%',
                '%'. $wpdb->esc_like($this->args['s']) . '%'
            );
        }

        if (!empty($this->args['start_date'])) {
            $wheres[] = $wpdb->prepare(
                '(created_at >= %s)',
                $this->args['start_date']->format('Y-m-d') . ' 00:00:00'
            );
        }

        if (!empty($this->args['end_date'])) {
            $wheres[] = $wpdb->prepare(
                '(created_at <= %s)',
                $this->args['end_date']->format('Y-m-d') . ' 23:59:59'
            );
        }

        $wheres = implode(' AND ', $wheres);

        if (empty($wheres)) {
            $wheres = '1=1';
        }

        $total_items = $wpdb->get_var("SELECT count(id) FROM {$table} WHERE {$wheres}");
        $items       = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, recipients_to, subject, created_at FROM {$table} WHERE {$wheres} ORDER BY {$this->args['orderby']} {$this->args['order']} LIMIT %d,%d",
                $start,
                $this->per_page
            ),
            ARRAY_A
        );

        return [
            'items'       => $items,
            'total_items' => $total_items,
            'per_page'    => $this->per_page,
        ];
    }
}
