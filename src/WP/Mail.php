<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\WP\Option;
use Fsylum\EmailTools\Models\Log;
use Fsylum\EmailTools\Contracts\Service;

class Mail implements Service
{
    public function run()
    {
        add_action('phpmailer_init', [$this, 'interceptOutgoingEmails'], PHP_INT_MAX);
        add_filter('wp_mail', [$this, 'modifyOutgoingEmails'], PHP_INT_MAX);
    }

    public function interceptOutgoingEmails(&$phpmailer)
    {
        $option = Option::get();

        if ((bool) $option['log']['status']) {
            (new Log)->insert($phpmailer);
        }

        if ((bool) $option['reroute']['status']) {
            $phpmailer->clearAddresses();

            foreach ($option['reroute']['recipients'] as $recipient) {
                $phpmailer->addAddress(sanitize_email($recipient));
            }
        }

        if ((bool) $option['bcc']['status']) {
            foreach ($option['bcc']['recipients'] as $recipient) {
                $phpmailer->addBCC(sanitize_email($recipient));
            }
        }
    }

    public function modifyOutgoingEmails(array $args = [])
    {
        $option = Option::get();

        if ((bool) $option['reroute']['status'] && (bool) $option['reroute']['append']['status']) {
            $recipients = $args['to'];

            if (is_array($recipients)) {
                $recipients = implode(', ', $recipients);
            }

            $appended_text = sprintf('(Originally for: %s)', $recipients);

            switch ($option['reroute']['append']['location']) {
                case 'subject':
                    $args['subject'] .= ' ' . $appended_text;
                    break;

                case 'message':
                    $args['message'] .= "\r\n\r\n" . $appended_text;
                    break;
            }
        }

        return $args;
    }
}
