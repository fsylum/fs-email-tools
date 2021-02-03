<p>You can use this page to test out the configuration you've set for your site.</p>

<form action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post">
    <?php wp_nonce_field('fs-email-tools-test-email'); ?>
    <input type="hidden" name="action" value="fs_email_tools_send_test_email">

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label for="fs-email-tools-test-to">To</label></th>
                <td><input name="to" type="text" id="fs-email-tools-test-to" value="<?php echo esc_attr(get_option('admin_email')); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="fs-email-tools-test-subject">Subject</label></th>
                <td><input name="subject" type="text" id="fs-email-tools-test-subject" value="Test email from <?php echo esc_attr(get_bloginfo('name')); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="fs-email-tools-test-message">Message</label></th>
                <td><textarea name="message" id="fs-email-tools-test-message" class="regular-text" rows="10" required>It works!</textarea></td>
            </tr>
        </tbody>
    </table>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Send Test Email"></p>
</form>

