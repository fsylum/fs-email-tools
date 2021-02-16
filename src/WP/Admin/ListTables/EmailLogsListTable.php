<?php

namespace Fsylum\EmailTools\WP\Admin\ListTables;

use DateTimeZone;
use WP_List_Table;
use Fsylum\EmailTools\Helper;
use Fsylum\EmailTools\Models\Log;
use Fsylum\EmailTools\WP\Admin\Page;
use Fsylum\EmailTools\Factories\LogFactory;

class EmailLogsListTable extends WP_List_Table
{
    public function prepare_items()
    {
        $this->process_bulk_actions();

        $this->_column_headers = array($this->get_columns(), [], $this->get_sortable_columns());
        $result                = (new LogFactory($_REQUEST, $this->get_pagenum()))->get();
        $this->items           = $result['items'];

        $this->set_pagination_args(
            array(
                'total_items' => $result['total_items'],
                'per_page'    => $result['per_page'],
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
            'resend' => sprintf('<a href="#" class="js-resend-email-log" data-id="%d">Resend</a>', $item['id']),
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
                return wp_date(
                    sprintf('%s %s', get_option('date_format'), get_option('time_format')),
                    strtotime($item[$column_name])
                );
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

    protected function extra_tablenav($which)
    {
        if ($which === 'bottom') {
            return;
        }

        ob_start();
        ?>
            <div class="alignleft actions">
                <label for="filter-start-date" class="screen-reader-text">Filter by start date</label>
                <input type="text" id="filter-start-date" placeholder="Select a start date" name="start_date" value="<?php echo esc_attr(sanitize_text_field($_GET['start_date'] ?? '')) ?>">
                <label for="filter-end-date" class="screen-reader-text">Filter by end date</label>
                <input type="text" id="filter-end-date" placeholder="Select an end date" name="end_date" value="<?php echo esc_attr(sanitize_text_field($_GET['end_date'] ?? '')) ?>">
                <input type="submit" class="button" value="Filter">
            </div>
        <?php

        echo ob_get_clean();
    }

    private function process_bulk_actions()
    {
        switch ($this->current_action()) {
            case 'delete':
                $redirect = $_SERVER['HTTP_REFERER'];

                if (empty($redirect)) {
                    $redirect = (new Page)->tabUrl('email-logs');
                }

                if (!empty($_REQUEST['ids'])) {
                    $result = (new Log)->bulkDelete($_REQUEST['ids']);
                }

                $redirect = add_query_arg([
                    'deleted' => $result ? 'yes' : 'no',
                ], $redirect);

                Helper::jsRedirect($redirect);
                break;
        }
    }
}
