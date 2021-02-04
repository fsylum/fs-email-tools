<?php

namespace Fsylum\EmailTools\WP\Admin;

use Fsylum\EmailTools\Plugin;
use Fsylum\EmailTools\Service;
use Fsylum\EmailTools\WP\Option;

class Settings extends Service
{
    const KEY = 'fs-email-tools';

    protected $option;

    public function __construct(Plugin $plugin)
    {
        $this->option = Option::get();

        parent::__construct($plugin);
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
                'type' => 'array',
                'sanitize_callback' => [$this, 'validateAndSanitize'],
            ],
        );

        add_settings_section(
            'fs_email_tools_section_reroute',
            __('Email Rerouting', 'fs-email-tools'),
            [$this, 'displayRerouteSection'],
            $this->plugin::SLUG
        );

        add_settings_field(
            'fs_email_tools_section_reroute_status',
            __('Status', 'fs-email-tools'),
            [$this, 'displayRerouteStatusField'],
            $this->plugin::SLUG,
            'fs_email_tools_section_reroute'
        );

        add_settings_field(
            'fs_email_tools_section_reroute_recipients',
            __('Recipients', 'fs-email-tools'),
            [$this, 'displayRerouteRecipientsField'],
            $this->plugin::SLUG,
            'fs_email_tools_section_reroute'
        );

        add_settings_section(
            'fs_email_tools_section_database_logs',
            __('Database Logs', 'fs-email-tools'),
            [$this, 'displayDatabaseLogsSection'],
            $this->plugin::SLUG
        );

        add_settings_field(
            'fs_email_tools_section_database_logs_status',
            __('Status', 'fs-email-tools'),
            [$this, 'displayDatabaseLogsStatusField'],
            $this->plugin::SLUG,
            'fs_email_tools_section_database_logs'
        );

        add_settings_field(
            'fs_email_tools_section_database_logs_validity',
            __('Validity', 'fs-email-tools'),
            [$this, 'displayDatabaseLogsValidityField'],
            $this->plugin::SLUG,
            'fs_email_tools_section_database_logs'
        );
    }

    public function validateAndSanitize($data)
    {
        $is_valid = true;
        $data     = wp_parse_args($data, Option::$defaults);

        // typecast status to boolean directly without validationg
        $data['reroute']['status']           = (bool) $data['reroute']['status'];
        $data['reroute']['append']['status'] = (bool) $data['reroute']['append']['status'];
        $data['log']['status']               = (bool) $data['log']['status'];
        $data['log']['keep_indefinitely']    = (bool) $data['log']['keep_indefinitely'];

        // validate each recipients to be a valid email
        $data['reroute']['recipients'] = explode("\n", str_replace("\r", '', $data['reroute']['recipients']));
        $data['reroute']['recipients'] = array_filter($data['reroute']['recipients']);
        $data['reroute']['recipients'] = array_map('sanitize_email', $data['reroute']['recipients']);

        if ($data['reroute']['status']) {
            if (empty($data['reroute']['recipients'])) {
                add_settings_error(self::KEY, 'fs_email_tools_missing_email', 'Please specify the recipient(s) email address');
            } else {
                foreach ($data['reroute']['recipients'] as $recipient) {
                    if (!is_email($recipient)) {
                        add_settings_error(self::KEY, 'fs_email_tools_invalid_email', sprintf(__('Invalid email address: %s', 'fs-email-tools'), $recipient));

                        $is_valid = false;
                    }
                }
            }

            if ($data['reroute']['append']['status'] && !in_array($data['reroute']['append']['location'], ['subject', 'message'])) {
                add_settings_error(self::KEY, 'fs_email_tools_invalid_append_location', 'Location must be either subject or message.');

                $is_valid = false;
            }
        }

        if (!$data['log']['keep_indefinitely'] && filter_var($data['log']['keep_in_days'], FILTER_VALIDATE_INT) === false) {
            add_settings_error(self::KEY, 'fs_email_tools_invalid_keep_in_days', 'Days must be a number');

            $is_valid = false;
        }

        // If any of the validation fails, replace the updated $data with the original options stored in the database
        if (!$is_valid) {
            $data = $this->option;
        }

        return $data;
    }

    public function displayRerouteSection($args)
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
                    Reroute all outgoing emails to the specified recipients
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
                        Fill in the recipients to reroute all of outgoing emails to, one email address per line.
                    </label>
                </p>
                <p>
                    <textarea name="<?php echo esc_attr(Option::KEY); ?>[reroute][recipients]" rows="10" id="fs-email-tools-reroute-recipients" class="regular-text" spellcheck="false"><?php echo implode("\r\n", $this->option['reroute']['recipients'] ?? []); ?></textarea>
                </p>
                    <label>
                        <input name="<?php echo esc_attr(Option::KEY); ?>[reroute][append][status]" type="checkbox" id="fs-email-tools-reroute-append-status" value="1" <?php checked(true, $this->option['reroute']['append']['status']); ?>>
                        Also append original recipient(s) email address to the end of email
                    </label>
                    <select name="<?php echo esc_attr(Option::KEY); ?>[reroute][append][location]" id="fs-email-tools-reroute-append-location">
                        <option value="subject" <?php selected('subject', $this->option['reroute']['append']['location']); ?>>subject</option>
                        <option value="message" <?php selected('message', $this->option['reroute']['append']['location']); ?>>message</option>
                    </select>
            </fieldset>
        <?php
    }

    public function displayDatabaseLogsSection($args)
    {
        ?>
            <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Database logs feature allows you to store a copy of all outgoing emails in your database so that you can view it again at a later time.'); ?></p>
        <?php
    }

    public function displayDatabaseLogsStatusField()
    {
        ?>
            <fieldset>
                <label>
                    <input name="<?php echo esc_attr(Option::KEY); ?>[log][status]" type="checkbox" id="fs-email-tools-log-status" value="1" <?php checked(true, $this->option['log']['status']); ?>>
                    Log all outgoing emails into the database
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
                        Keep email logs for
                    </label>
                    <label>
                        <input name="<?php echo esc_attr(Option::KEY); ?>[log][keep_in_days]" type="text" id="fs-email-tools-log-keep-in-days" step="1" min="1" value="<?php echo esc_attr($this->option['log']['keep_in_days']); ?>" class="small-text">
                        days.
                    </label>
                </p>
                <p>
                    <label><input name="<?php echo esc_attr(Option::KEY); ?>[log][keep_indefinitely]" type="radio" value="1" class="tog fs-email-tools-log-keep-indefinitely" <?php checked(true, $this->option['log']['keep_indefinitely']); ?>> Keep email logs indefinitely</label>
                </p>
            </fieldset>
        <?php
    }
}
