<?php

use Fsylum\EmailTools\WP\Admin\Settings;

?>
<form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
    <?php
        settings_fields(Settings::KEY);
        do_settings_sections(Settings::KEY);
        submit_button(__('Save Changes', 'fs-email-tools'));
    ?>
</form>
