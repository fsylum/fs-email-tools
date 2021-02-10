<?php

use Fsylum\EmailTools\WP\Admin\Page;
use Fsylum\EmailTools\WP\Admin\ListTables\EmailLogsListTable;

$listTable = new EmailLogsListTable;
?>
    <form action="<?php echo esc_url(admin_url('tools.php')); ?>">
        <input type="hidden" name="page" value="<?php echo esc_attr(Page::KEY); ?>">
        <input type="hidden" name="tab" value="<?php echo esc_attr($current_tab); ?>">

        <?php
            $listTable->prepare_items();
            $listTable->search_box(__( 'Search Email Logs' ), 'fs-email-tools-logs');
            $listTable->display();
        ?>
    </form>
