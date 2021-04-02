<p><?php _e('You can use this page to test out the configuration you have set for your site.', 'fs-email-tools'); ?></p>

<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post">
    <?php wp_nonce_field('fs-email-tools-test-email'); ?>
    <input type="hidden" name="action" value="fs_email_tools_send_test_email">

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label for="fs-email-tools-test-to"><?php _e('To', 'fs-email-tools'); ?></label></th>
                <td><input name="to" type="text" id="fs-email-tools-test-to" value="<?php echo esc_attr(get_option('admin_email')); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="fs-email-tools-test-subject"><?php _e('Subject', 'fs-email-tools'); ?></label></th>
                <td><input name="subject" type="text" id="fs-email-tools-test-subject" value="<?php _e('Test email from', 'fs-email-tools'); ?> <?php echo esc_attr(get_bloginfo('name')); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="fs-email-tools-test-message"><?php _e('Message', 'fs-email-tools'); ?></label></th>
                <td><textarea name="message" id="fs-email-tools-test-message" class="regular-text" rows="10" required><?php _e('It works!', 'fs-email-tools'); ?></textarea></td>
            </tr>
        </tbody>
    </table>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Send Test Email', 'fs-email-tools'); ?>"></p>
</form>

