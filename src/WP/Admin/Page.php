<?php

namespace Fsylum\EmailTools\WP\Admin;

use Fsylum\EmailTools\Service;

class Page extends Service
{
    public function run()
    {
        add_action('admin_menu', [$this, 'addPage']);
    }

    public function addPage()
    {
        add_management_page(__('Email Tools', 'fs-email-tools'), __('Email Tools', 'fs-email-tools'), $this->plugin::CAPABILITY, $this->plugin::SLUG, [$this, 'pageDisplay']);
    }

    public function pageDisplay()
    {
        if (!current_user_can($this->plugin::CAPABILITY)) {
            return;
        }

        if (isset($_GET['settings-updated']) && empty(get_settings_errors(Settings::KEY))) {
            add_settings_error(Settings::KEY, 'fs_email_tools_status', __( 'Settings saved.', 'fs-email-tools' ), 'updated');
        }

        settings_errors(Settings::KEY);

        $all_tabs    = $this->tabs();
        $default_tab = array_keys($all_tabs);
        $default_tab = array_shift($default_tab);
        $current_tab = !empty($_GET['tab']) && in_array($_GET['tab'], array_keys($all_tabs)) ? sanitize_key($_GET['tab']) : $default_tab;
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
            'settings'       => 'Settings',
            'database-logs'  => 'Database Logs',
            'disable-emails' => 'Disable Internal Emails',
            'test-email'     => 'Send Test Email',
        ];
    }

    private function tabUrl($tab)
    {
        return add_query_arg([
            'page' => $this->plugin::SLUG,
            'tab' => $tab,
        ], admin_url('tools.php'));
    }
}