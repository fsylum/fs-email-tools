<?php

namespace Fsylum\EmailTools\Factories;

use Fsylum\EmailTools\WP\Database;

class LogFactory
{
    public function __construct(array $args = [], $page = 1, $per_page = 10)
    {
        $args = wp_parse_args($args, [
            's'       => '',
            'orderby' => 'created_at',
            'order'   => 'DESC',
        ]);

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

        $start = ($this->page - 1) * $this->per_page;
        $table = $wpdb->prefix . Database::TABLE;

        if (empty($this->args['s'])) {
            $where_query = '1=1';
        } else {
            $where_query = $wpdb->prepare(
                'recipients_to LIKE %s OR subject LIKE %s OR message LIKE %s',
                '%'. $wpdb->esc_like($this->args['s']) . '%',
                '%'. $wpdb->esc_like($this->args['s']) . '%',
                '%'. $wpdb->esc_like($this->args['s']) . '%'
            );
        }

        $total_items = $wpdb->get_var("SELECT count(id) FROM {$table} WHERE {$where_query} ");
        $items       = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, recipients_to, subject, created_at FROM {$table} WHERE {$where_query} ORDER BY {$this->args['orderby']} {$this->args['order']} LIMIT %d,%d",
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
