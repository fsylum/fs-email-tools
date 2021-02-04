<?php

namespace Fsylum\EmailTools\WP\Admin\ListTables;

use WP_List_Table;

class EmailLogListTable extends WP_List_Table
{
    public function prepare_items()
    {
        $this->_column_headers = array($this->get_columns(), [], $this->get_sortable_columns());
        $this->items           = [
            [
                'recipients_to' => serialize(['test@test.com']),
                'subject' => 'test',
                'attachments' => [],
                'created_at' => current_time('mysql'),
            ],
            [
                'recipients_to' => serialize(['test@test.com', 'test2@test.com']),
                'subject' => 'test',
                'attachments' => [],
                'created_at' => current_time('mysql'),
            ],
        ];

        $this->set_pagination_args(
            array(
                'total_items' => 100,
                'per_page'    => 5,
            )
        );
        /*

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 2;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;*/
    }

    public function get_columns()
    {
        return [
            'recipients_to'  => 'Recipients',
            'subject'        => 'Subject',
            'created_at'     => 'Sent At',
            'action'         => 'Action',
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'subject'    => 'orderby',
            'created_at' => ['orderby', 'desc'],
        ];
    }

    public function column_default($item, $column_name)
    {
        switch( $column_name ) {
            case 'recipients_to':
                return implode('<br>', unserialize($item[$column_name]));
                break;

            case 'action':
                return '<div class="row-actions visible"><span class="edit"><a href="https://wp-email-tools.test/wp-admin/post.php?post=1&amp;action=edit" aria-label="Edit “Hello world!”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Hello world!” inline" aria-expanded="false">Quick&nbsp;Edit</button> | </span><span class="trash"><a href="https://wp-email-tools.test/wp-admin/post.php?post=1&amp;action=trash&amp;_wpnonce=fda44e0223" class="submitdelete" aria-label="Move “Hello world!” to the Trash">Trash</a> | </span><span class="view"><a href="https://wp-email-tools.test/?p=1" rel="bookmark" aria-label="View “Hello world!”">View</a></span></div>';
                return '<a href="#">View</a> <a href="#">Delete</a>';
                break;

            default:
                return $item[$column_name];
                break;
        }
    }
}
