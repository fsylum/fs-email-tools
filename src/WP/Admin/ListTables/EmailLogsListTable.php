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

        $per_page = get_user_meta(
            get_current_user_id(),
            get_current_screen()->get_option('per_page', 'option'),
            true
        );

        $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];
        $result                = (new LogFactory($_REQUEST, $this->get_pagenum(), absint($per_page)))->get();
        $this->items           = $result['items'];

        $this->set_pagination_args([
            'total_items' => $result['total_items'],
            'per_page'    => $result['per_page'],
        ]);
    }

    public function no_items()
    {
        _e('No email logs found.', 'fs-email-tools');
    }

    public function get_columns()
    {
        return [
            'cb'            => '<input type="checkbox">',
            'subject'       => __('Subject', 'fs-email-tools'),
            'recipients_to' => __('Recipient(s)', 'fs-email-tools'),
            'created_at'    => __('Sent At', 'fs-email-tools'),
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
            'view'   => sprintf('<a href="#" class="js-view-email-log" data-id="%d">' . __('View', 'fs-email-tools') . '</a>', $item['id']),
            'delete' => sprintf('<a href="%s" class="js-delete-email-log">' . __('Delete', 'fs-email-tools') . '</a>', wp_nonce_url($delete_url, 'fs-email-tools-delete-nonce')),
        ];

        $subject = sprintf(
            (bool) $item['is_read'] ? '%s' : '<strong>%s</strong>',
            $item['subject']
        );

        return sprintf('%s %s', $subject, $this->row_actions($actions));
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
            'delete' => __('Delete', 'fs-email-tools'),
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
                <label for="filter-start-date" class="screen-reader-text"><?php _e('Filter by start date', 'fs-email-tools'); ?></label>
                <input type="text" id="filter-start-date" placeholder="<?php _e('Select a start date', 'fs-email-tools'); ?>" name="start_date" value="<?php echo esc_attr(sanitize_text_field($_GET['start_date'] ?? '')) ?>">
                <label for="filter-end-date" class="screen-reader-text"><?php _e('Filter by end date', 'fs-email-tools'); ?></label>
                <input type="text" id="filter-end-date" placeholder="<?php _e('Select an end date', 'fs-email-tools'); ?>" name="end_date" value="<?php echo esc_attr(sanitize_text_field($_GET['end_date'] ?? '')) ?>">
                <input type="submit" class="button" value="<?php _e('Filter', 'fs-email-tools'); ?>">
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
                    $result = (new Log)->bulkDelete(array_map('absint', $_REQUEST['ids']));
                }

                $redirect = add_query_arg([
                    'deleted' => $result ? 'yes' : 'no',
                ], $redirect);

                Helper::jsRedirect($redirect);
                break;
        }
    }
}
