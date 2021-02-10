<?php

namespace Fsylum\EmailTools\Models;

use Fsylum\EmailTools\WP\Database;
use PHPMailer\PHPMailer\PHPMailer;

class Log
{
    protected $id;

    public function fromId($id)
    {
        $this->id = absint($id);

        return $this;
    }

    public function insertFromPHPMailer(PHPMailer $phpmailer)
    {
        global $wpdb;

        $recipients_to  = Helper::parsePHPMailerEmails($phpmailer->getToAddresses());
        $recipients_cc  = Helper::parsePHPMailerEmails($phpmailer->getCcAddresses());
        $recipients_bcc = Helper::parsePHPMailerEmails($phpmailer->getBccAddresses());
        $subject        = $phpmailer->Subject;
        $message        = $phpmailer->Body;
        $attachments    = Helper::parsePHPMailerAttachments($phpmailer->getAttachments());
        $headers        = $phpmailer->createHeader();
        $created_at     = current_time('mysql', true);

        $wpdb->insert(
            $wpdb->prefix . Database::TABLE,
            compact('recipients_to', 'recipients_cc', 'recipients_bcc', 'subject', 'message', 'attachments', 'headers', 'created_at'),
            '%s'
        );
    }

    public function delete()
    {
        global $wpdb;

        return $wpdb->delete($wpdb->prefix . Database::TABLE, ['id' => $this->id], ['%d']);
    }
}
