<?php

use Fsylum\EmailTools\WP\Admin\ListTables\EmailLogListTable;

$listTable = new EmailLogListTable;
$listTable->prepare_items();
$listTable->display();
?>
