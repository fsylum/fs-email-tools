<?php

use Fsylum\EmailTools\WP\Admin\ListTables\EmailLogsListTable;

$listTable = new EmailLogsListTable;
?>
    <form action="<?php echo esc_url($this->tabUrl($key)); ?>">
        <?php
            $listTable->prepare_items();
            $listTable->search_box(__( 'Search Email Logs' ), 'fs-email-tools-logs');
            $listTable->display();
        ?>
    </form>
