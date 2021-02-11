<?php

namespace Fsylum\EmailTools\WP\Admin\ListTables;

use DateTimeZone;
use WP_List_Table;
use Fsylum\EmailTools\Helper;
use Fsylum\EmailTools\Models\Log;
use Fsylum\EmailTools\WP\Database;

class EmailLogsListTable extends WP_List_Table
{
    const PER_PAGE = 10;

    public function prepare_items()
    {
        $this->process_bulk_actions();

        $this->_column_headers = array($this->get_columns(), [], $this->get_sortable_columns());
        $result                = $this->getEmailLogs($_REQUEST);
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
        $delete_url = add_query_arg([
            'action' => 'fs_email_tools_delete_email_log',
            'id'     => absint($item['id']),
        ], admin_url('admin-post.php'));

        $actions = [
            'view'   => sprintf('<a href="#" class="js-view-email-log" data-id="%d">View</a>', $item['id']),
            'delete' => sprintf('<a href="%s" class="js-delete-email-log">Delete</a>', wp_nonce_url($delete_url, 'fs-email-tools-delete-nonce')),
        ];

        return sprintf('%s %s', $item['subject'], $this->row_actions($actions) );
    }

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="ids[]" value="%d">', $item['id']);
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

    private function getEmailLogs(array $args = [])
    {
        global $wpdb;

        $args = wp_parse_args($args, [
            's'       => '',
            'orderby' => 'created_at',
            'order'   => 'DESC',
        ]);

        $args['orderby'] = in_array($args['orderby'], ['subject', 'created_at']) ? $args['orderby'] : 'created_at';
        $args['order']   = in_array($args['order'], ['asc', 'desc']) ? strtoupper($args['order']) : 'DESC';
        $page            = $this->get_pagenum();
        $start           = ($page - 1) * self::PER_PAGE;
        $table           = $wpdb->prefix . Database::TABLE;

        if (empty($args['s'])) {
            $where_query = '1=1';
        } else {
            $where_query = $wpdb->prepare(
                'recipients_to LIKE %s OR subject LIKE %s OR message LIKE %s',
                '%'. $wpdb->esc_like($args['s']) . '%',
                '%'. $wpdb->esc_like($args['s']) . '%',
                '%'. $wpdb->esc_like($args['s']) . '%'
            );
        }

        $total_items = $wpdb->get_var("SELECT count(id) FROM {$table} WHERE {$where_query} ");
        $items       = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, recipients_to, subject, created_at FROM {$table} WHERE {$where_query} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d,%d",
                $start,
                self::PER_PAGE
            ),
            ARRAY_A
        );

        return compact('items', 'total_items');
    }

    private function process_bulk_actions()
    {
        switch ($this->current_action()) {
            case 'delete':
                $redirect = $_SERVER['HTTP_REFERER'];

                if (empty($redirect)) {
                    $redirect = $this->tabUrl('email-logs');
                }

                $result = (new Log)->bulkDelete($_REQUEST['ids']);

                $redirect = add_query_arg([
                    'deleted' => $result ? 'yes' : 'no',
                ], $redirect);

                Helper::jsRedirect($redirect);
                break;
        }
    }
}
