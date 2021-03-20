<?php

namespace Fsylum\EmailTools\WP\Admin;

use Fsylum\EmailTools\Helper;
use Fsylum\EmailTools\WP\Option;
use Fsylum\EmailTools\WP\Admin\Page;
use Fsylum\EmailTools\Contracts\Service;

class Settings implements Service
{
    const KEY = 'fs-email-tools';

    protected $option;

    public function __construct()
    {
        $this->option = Option::get();
    }

    public function run()
    {
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerSettings()
    {
        register_setting(
            self::KEY,
            Option::KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'validateAndSanitize'],
            ],
        );

        add_settings_section(
            'fs_email_tools_section_reroute',
            __('Email Rerouting', 'fs-email-tools'),
            [$this, 'displayRerouteSection'],
            Page::KEY
        );

        add_settings_field(
            'fs_email_tools_section_reroute_status',
            __('Status', 'fs-email-tools'),
            [$this, 'displayRerouteStatusField'],
            Page::KEY,
            'fs_email_tools_section_reroute'
        );

        add_settings_field(
            'fs_email_tools_section_reroute_recipients',
            __('Recipients', 'fs-email-tools'),
            [$this, 'displayRerouteRecipientsField'],
            Page::KEY,
            'fs_email_tools_section_reroute'
        );

        add_settings_section(
            'fs_email_tools_section_database_logs',
            __('Database Logs', 'fs-email-tools'),
            [$this, 'displayDatabaseLogsSection'],
            Page::KEY
        );

        add_settings_field(
            'fs_email_tools_section_database_logs_status',
            __('Status', 'fs-email-tools'),
            [$this, 'displayDatabaseLogsStatusField'],
            Page::KEY,
            'fs_email_tools_section_database_logs'
        );

        add_settings_field(
            'fs_email_tools_section_database_logs_validity',
            __('Validity', 'fs-email-tools'),
            [$this, 'displayDatabaseLogsValidityField'],
            Page::KEY,
            'fs_email_tools_section_database_logs'
        );

        add_settings_section(
            'fs_email_tools_section_bcc',
            __('Automatic BCC', 'fs-email-tools'),
            [$this, 'displayAutomaticBCCSection'],
            Page::KEY
        );

        add_settings_field(
            'fs_email_tools_section_bcc_status',
            __('Status', 'fs-email-tools'),
            [$this, 'displayAutomaticBCCStatusField'],
            Page::KEY,
            'fs_email_tools_section_bcc'
        );

        add_settings_field(
            'fs_email_tools_section_bcc_recipients',
            __('Recipients', 'fs-email-tools'),
            [$this, 'displayAutomaticBCCRecipientsField'],
            Page::KEY,
            'fs_email_tools_section_bcc'
        );
    }

    public function validateAndSanitize($data = [])
    {
        $is_valid = true;
        $data     = wp_parse_args($data, Option::$defaults);

        // typecast status to boolean directly without validationg
        $data['reroute']['status']           = (bool) $data['reroute']['status'];
        $data['reroute']['append']['status'] = (bool) $data['reroute']['append']['status'];
        $data['log']['status']               = (bool) $data['log']['status'];
        $data['log']['keep_indefinitely']    = (bool) $data['log']['keep_indefinitely'];
        $data['bcc']['status']               = (bool) $data['bcc']['status'];
        $data['reroute']['recipients']       = Helper::sanitizeEmailsFromTextarea($data['reroute']['recipients']);
        $data['bcc']['recipients']           = Helper::sanitizeEmailsFromTextarea($data['bcc']['recipients']);

        if ($data['reroute']['status']) {
            if (empty($data['reroute']['recipients'])) {
                add_settings_error(self::KEY, 'fs_email_tools_missing_email', __('Please specify the recipient(s) email address for email rerouting', 'fs-email-tools'));
            } else {
                foreach ($data['reroute']['recipients'] as $recipient) {
                    if (!is_email($recipient)) {
                        add_settings_error(self::KEY, 'fs_email_tools_invalid_email', sprintf(__('Invalid email address for email rerouting: %s', 'fs-email-tools'), $recipient));

                        $is_valid = false;
                    }
                }
            }

            if ($data['reroute']['append']['status'] && !in_array($data['reroute']['append']['location'], ['subject', 'message'])) {
                add_settings_error(self::KEY, 'fs_email_tools_invalid_append_location', __('Location must be either subject or message.', 'fs-email-tools'));

                $is_valid = false;
            }
        }

        if (!$data['log']['keep_indefinitely'] && filter_var($data['log']['keep_in_days'], FILTER_VALIDATE_INT) === false) {
            add_settings_error(self::KEY, 'fs_email_tools_invalid_keep_in_days', __('Days must be a number', 'fs-email-tools'));

            $is_valid = false;
        }

        if ($data['bcc']['status']) {
            if (empty($data['bcc']['recipients'])) {
                add_settings_error(self::KEY, 'fs_email_tools_missing_email', __('Please specify the recipient(s) email address for automatic BCC', 'fs-email-tools'));
            } else {
                foreach ($data['bcc']['recipients'] as $recipient) {
                    if (!is_email($recipient)) {
                        add_settings_error(self::KEY, 'fs_email_tools_invalid_email', sprintf(__('Invalid email address for automatic BCC: %s', 'fs-email-tools'), $recipient));

                        $is_valid = false;
                    }
                }
            }
        }

        // If any of the validation fails, replace the updated $data with the original options stored in the database
        if (!$is_valid) {
            $data = $this->option;
        }

        return $data;
    }

    public function displayRerouteSection(array $args = [])
    {
        ?>
            <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Email rerouting is a feature to reroute all of your outgoing email from this site to a different set of recipients.', 'fs-email-tools'); ?></p>
        <?php
    }

    public function displayRerouteStatusField()
    {
        ?>
            <fieldset>
                <label for="fs-email-tools-reroute-status">
                    <input name="<?php echo esc_attr(Option::KEY); ?>[reroute][status]" type="checkbox" id="fs-email-tools-reroute-status" value="1" <?php checked(true, $this->option['reroute']['status']); ?>>
                    <?php esc_html_e('Reroute all outgoing emails to the specified recipients', 'fs-email-tools'); ?>
                </label>
            </fieldset>
        <?php
    }

    public function displayRerouteRecipientsField()
    {
        ?>
            <fieldset>
                <p>
                    <label>
                        <?php esc_html_e('Fill in the recipients to reroute all of outgoing emails to, one email address per line.', 'fs-email-tools'); ?>
                    </label>
                </p>
                <p>
                    <textarea name="<?php echo esc_attr(Option::KEY); ?>[reroute][recipients]" rows="10" id="fs-email-tools-reroute-recipients" class="regular-text" spellcheck="false"><?php echo implode("\r\n", $this->option['reroute']['recipients'] ?? []); ?></textarea>
                </p>
                    <label>
                        <input name="<?php echo esc_attr(Option::KEY); ?>[reroute][append][status]" type="checkbox" id="fs-email-tools-reroute-append-status" value="1" <?php checked(true, $this->option['reroute']['append']['status']); ?>>
                        <?php esc_html_e('Also append original recipient(s) email address to the end of email', 'fs-email-tools'); ?>
                    </label>
                    <select name="<?php echo esc_attr(Option::KEY); ?>[reroute][append][location]" id="fs-email-tools-reroute-append-location">
                        <option value="subject" <?php selected('subject', $this->option['reroute']['append']['location']); ?>><?php esc_html_e('subject', 'fs-email-tools'); ?></option>
                        <option value="message" <?php selected('message', $this->option['reroute']['append']['location']); ?>><?php esc_html_e('message', 'fs-email-tools'); ?></option>
                    </select>
            </fieldset>
        <?php
    }

    public function displayDatabaseLogsSection(array $args = [])
    {
        ?>
            <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Database logs feature allows you to store a copy of all outgoing emails in your database so that you can view it again at a later time.', 'fs-email-tools'); ?></p>
        <?php
    }

    public function displayDatabaseLogsStatusField()
    {
        ?>
            <fieldset>
                <label>
                    <input name="<?php echo esc_attr(Option::KEY); ?>[log][status]" type="checkbox" id="fs-email-tools-log-status" value="1" <?php checked(true, $this->option['log']['status']); ?>>
                    <?php esc_html_e('Log all outgoing emails into the database', 'fs-email-tools'); ?>
                </label>
            </fieldset>
        <?php
    }

    public function displayDatabaseLogsValidityField()
    {
        ?>
            <fieldset>
                <p>
                    <label>
                        <input name="<?php echo esc_attr(Option::KEY); ?>[log][keep_indefinitely]" type="radio" value="0" class="tog fs-email-tools-log-keep-indefinitely" <?php checked(false, $this->option['log']['keep_indefinitely']); ?>>
                        <?php esc_html_e('Keep email logs for', 'fs-email-tools'); ?>
                    </label>
                    <label>
                        <input name="<?php echo esc_attr(Option::KEY); ?>[log][keep_in_days]" type="text" id="fs-email-tools-log-keep-in-days" step="1" min="1" value="<?php echo esc_attr($this->option['log']['keep_in_days']); ?>" class="small-text">
                        <?php esc_html_e('days.', 'fs-email-tools'); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input name="<?php echo esc_attr(Option::KEY); ?>[log][keep_indefinitely]" type="radio" value="1" class="tog fs-email-tools-log-keep-indefinitely" <?php checked(true, $this->option['log']['keep_indefinitely']); ?>>
                        <?php esc_html_e('Keep email logs indefinitely', 'fs-email-tools'); ?>
                    </label>
                </p>
            </fieldset>
        <?php
    }

    public function displayAutomaticBCCSection(array $args = [])
    {
        ?>
            <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('This feature allows you to automatically set a list of email address as BCC for your outgoing emails.', 'fs-email-tools'); ?></p>
        <?php
    }

    public function displayAutomaticBCCStatusField()
    {
        ?>
            <fieldset>
                <label>
                    <input name="<?php echo esc_attr(Option::KEY); ?>[bcc][status]" type="checkbox" id="fs-email-tools-bcc-status" value="1" <?php checked(true, $this->option['bcc']['status']); ?>>
                    <?php esc_html_e('Automatically add BCC to all outgoing emails', 'fs-email-tools'); ?>
                </label>
            </fieldset>
        <?php
    }

    public function displayAutomaticBCCRecipientsField()
    {
        ?>
            <fieldset>
                <p>
                    <label><?php esc_html_e('Fill in the recipients to be used as BCC.', 'fs-email-tools'); ?></label>
                </p>
                <p>
                    <textarea name="<?php echo esc_attr(Option::KEY); ?>[bcc][recipients]" rows="10" id="fs-email-tools-bcc-recipients" class="regular-text" spellcheck="false"><?php echo implode("\r\n", $this->option['bcc']['recipients'] ?? []); ?></textarea>
                </p>
            </fieldset>
        <?php
    }
}
