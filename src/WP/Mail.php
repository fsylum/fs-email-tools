<?php

namespace Fsylum\EmailTools\WP;

use Fsylum\EmailTools\Service;
use Fsylum\EmailTools\WP\Option;

class Mail extends Service
{
    public function run()
    {
        add_action('phpmailer_init', [$this, 'interceptOutgoingEmails'], PHP_INT_MAX);
        add_filter('wp_mail', [$this, 'modifyOutgoingEmails'], PHP_INT_MAX);
    }

    public function interceptOutgoingEmails(&$phpmailer)
    {
        $option     = Option::get();
        $recipients = $phpmailer->getAllRecipientAddresses();

        if ((bool) $option['reroute']['status']) {
            $phpmailer->clearAllRecipients();

            foreach ($option['reroute']['recipients'] as $recipient) {
                $phpmailer->addAddress(sanitize_email($recipient));
            }
        }

        if ((bool) $option['log']['status']) {
            // TODO: log emails in db
        }
    }

    public function modifyOutgoingEmails($args)
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
