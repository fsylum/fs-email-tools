<?php

namespace Fsylum\EmailTools\WP\Admin;

use Fsylum\EmailTools\Contracts\Service;

class Page implements Service
{
    const CAPABILITY = 'manage_options';
    const KEY        = 'fs-email-tools';

    public function run()
    {
        add_action('admin_menu', [$this, 'addPage']);
        add_action('admin_post_fs_email_tools_send_test_email', [$this, 'sendTestEmail']);
    }

    public function addPage()
    {
        add_management_page(__('Email Tools', 'fs-email-tools'), __('Email Tools', 'fs-email-tools'), self::CAPABILITY, self::KEY, [$this, 'displayPage']);
    }

    public function displayPage()
    {
        if (!current_user_can(self::CAPABILITY)) {
            return;
        }

        $all_tabs    = $this->tabs();
        $current_tab = !empty($_GET['tab']) && in_array($_GET['tab'], array_keys($all_tabs)) ? sanitize_key($_GET['tab']) : array_keys($all_tabs)[0];

        if (isset($_GET['settings-updated']) && empty(get_settings_errors(Settings::KEY))) {
            add_settings_error(Settings::KEY, 'fs_email_tools_status', __( 'Settings saved.', 'fs-email-tools' ), 'updated');
        }

        if ($current_tab === 'test-email' && isset($_GET['sent'])) {
            if (sanitize_text_field($_GET['sent']) === 'yes') {
                add_settings_error(Settings::KEY, 'fs_email_tools_status', __( 'Test email is successfully sent.', 'fs-email-tools' ), 'updated');
            } else {
                add_settings_error(Settings::KEY, 'fs_email_tools_status', __( 'Failed to send test email.', 'fs-email-tools' ));
            }
        }

        settings_errors(Settings::KEY);
        ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

                <h2 class="nav-tab-wrapper">
                    <?php foreach ($all_tabs as $key => $title): ?>
                        <a href="<?php echo esc_url($this->tabUrl($key)); ?>" class="nav-tab <?php echo $current_tab === $key ? 'nav-tab-active' : ''; ?>"><?php echo esc_html($title); ?></a>
                    <?php endforeach; ?>
                </h2>

                <?php require FS_EMAIL_TOOLS_PLUGIN_PATH . '/templates/tabs/' . $current_tab . '.php'; ?>
            </div>
        <?php
    }

    private function tabs()
    {
        return [
            'settings'   => 'Settings',
            'email-logs' => 'Email Logs',
            'test-email' => 'Send Test Email',
        ];
    }

    private function tabUrl(string $tab)
    {
        return add_query_arg([
            'page' => self::KEY,
            'tab'  => $tab,
        ], admin_url('tools.php'));
    }

    public function sendTestEmail()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'fs-email-tools-test-email')) {
            wp_die('Invalid request');
        }

        $to      = sanitize_email($_REQUEST['to']);
        $subject = sanitize_text_field($_REQUEST['subject']);
        $message = sanitize_textarea_field($_REQUEST['message']);

        $result = wp_mail($to, $subject, $message);

        wp_redirect(
            add_query_arg([
                'sent' => $result ? 'yes' : 'no',
            ], $this->tabUrl('test-email'))
        );
        exit;
    }
}
