<?php

namespace Fsylum\EmailTools\Models;

use Fsylum\EmailTools\WP\Database;
use PHPMailer\PHPMailer\PHPMailer;

class Log
{
    public function insertFromPHPMailer(PHPMailer $phpmailer)
    {
        global $wpdb;

        $recipients_to  = $this->parseEmails($phpmailer->getToAddresses());
        $recipients_cc  = $this->parseEmails($phpmailer->getCcAddresses());
        $recipients_bcc = $this->parseEmails($phpmailer->getBccAddresses());
        $subject        = $phpmailer->Subject;
        $message        = $phpmailer->Body;
        $attachments    = $this->parseAttachments($phpmailer->getAttachments());
        $headers        = $phpmailer->createHeader();
        $created_at     = current_time('mysql', true);

        $wpdb->insert(
            $wpdb->prefix . Database::TABLE,
            compact('recipients_to', 'recipients_cc', 'recipients_bcc', 'subject', 'message', 'attachments', 'headers', 'created_at'),
            '%s'
        );
    }

    public function parseEmails($emails)
    {
        $emails = array_merge([], ...$emails);
        $emails = array_filter($emails);

        if (empty($emails)) {
            return null;
        }

        return serialize($emails);
    }

    public function parseAttachments($attachments)
    {
        $attachments = wp_list_pluck($attachments, 0);
        $attachments = array_filter($attachments);

        if (empty($attachments)) {
            return null;
        }

        return serialize($attachments);
    }
}
