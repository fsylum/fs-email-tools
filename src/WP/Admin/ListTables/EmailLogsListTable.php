<?php

namespace Fsylum\EmailTools\WP\Admin\ListTables;

use DateTimeZone;
use WP_List_Table;
use Fsylum\EmailTools\Plugin;
use Fsylum\EmailTools\WP\Database;
use Fsylum\EmailTools\WP\Admin\Page;

class EmailLogsListTable extends WP_List_Table
{
    const PER_PAGE = 10;

    public function prepare_items()
    {
        $this->_column_headers = array($this->get_columns(), [], $this->get_sortable_columns());
        $result                = $this->getItems($_REQUEST);
        $this->items           = $result['items'];

        $this->set_pagination_args(
            array(
                'total_items' => $result['total_items'],
                'per_page'    => self::PER_PAGE,
            )
        );
    }

    public function no_items()
    {
        _e('No email logs found.', 'fs-email-tools');
    }

    public function get_columns()
    {
        return [
            'cb'            => '<input type="checkbox">',
            'subject'       => 'Subject',
            'recipients_to' => 'Recipient(s)',
            'created_at'    => 'Sent At',
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'subject'    => ['subject', false],
            'created_at' => ['created_at', false],
        ];
    }

    public function column_subject($item)
    {
        $view_url = add_query_arg(
            [
                'page'   => Page::KEY,
                'tab'    => 'email-logs',
                'action' => 'view',
                'id'     => $item['id'],
            ],
            admin_url('tools.php')
        );

        $delete_url = add_query_arg(
            [
                'page'     => Page::KEY,
                'tab'      => 'email-logs',
                'action'   => 'delete',
                'id'       => $item['id'],
                '_wpnonce' => wp_create_nonce('fs-email-tools-delete-email-log'),
            ],
            admin_url('tools.php')
        );

        $actions = [
            'view'   => sprintf('<a href="%s">View</a>', $view_url),
            'delete' => sprintf('<a href="%s">Delete</a>', $delete_url),
        ];

        return sprintf('%s %s', $item['subject'], $this->row_actions($actions) );
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="email_log_id[]" value="%d">', $item['id']
        );
    }

    public function column_default($item, $column_name)
    {
        switch($column_name) {
            case 'recipients_to':
                return implode('<br>', unserialize($item[$column_name]));
                break;

            case 'created_at':
                return wp_date('Y-m-d H:i:s', strtotime($item[$column_name]), new DateTimeZone('UTC'));
                break;

            default:
                return $item[$column_name];
                break;
        }
    }

    protected function get_bulk_actions()
    {
        return [
            'delete' => 'Delete'
        ];
    }

    private function getItems(array $args = [])
    {
        global $wpdb;

        $args = wp_parse_args($args, [
            'paged'   => 1,
            'search'  => '',
            'orderby' => 'created_at',
            'order'   => 'DESC',
        ]);

        $args['orderby'] = in_array($args['orderby'], ['subject', 'created_at']) ? $args['orderby'] : 'created_at';
        $args['order']   = in_array($args['order'], ['ASC', 'DESC']) ? $args['order'] : 'DESC';

        $page        = max(1, absint($args['paged']));
        $start       = ($page - 1) * self::PER_PAGE;
        $table       = $wpdb->prefix . Database::TABLE;
        $total_items = $wpdb->get_var("SELECT count(id) FROM {$table}");
        $items       = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT SQL_CALC_FOUND_ROWS id, recipients_to, subject, created_at FROM {$table} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d,%d",
                $start,
                self::PER_PAGE
            ),
            ARRAY_A
        );

        return compact('items', 'total_items');
    }
}
