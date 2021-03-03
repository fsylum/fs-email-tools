<?php

namespace Fsylum\EmailTools\Models;

use Fsylum\EmailTools\Helper;
use Fsylum\EmailTools\WP\Database;
use PHPMailer\PHPMailer\PHPMailer;

class Log
{
    protected $id;
    protected $data = [];

    public function __construct($id)
    {
        $this->id = absint($id);

        return $this;
    }

    public function insertFromPHPMailer(PHPMailer $phpmailer)
    {
        global $wpdb;

        $recipients_to  = Helper::sanitizePHPMailerEmails($phpmailer->getToAddresses());
        $recipients_cc  = Helper::sanitizePHPMailerEmails($phpmailer->getCcAddresses());
        $recipients_bcc = Helper::sanitizePHPMailerEmails($phpmailer->getBccAddresses());
        $subject        = $phpmailer->Subject;
        $message        = $phpmailer->Body;
        $attachments    = Helper::sanitizePHPMailerAttachments($phpmailer->getAttachments());
        $headers        = $phpmailer->createHeader();
        $created_at     = current_time('mysql', true);

        return $wpdb->insert(
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

    public function fetch()
    {
        global $wpdb;

        $table = $wpdb->prefix . Database::TABLE;

        $this->data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, recipients_to, recipients_cc, recipients_bcc, subject, message, attachments, headers, created_at FROM {$table} WHERE id = %d LIMIT 1",
                $this->id
            ),
            ARRAY_A
        );

        return $this;
    }

    public function toArray()
    {
        $data = $this->data;

        foreach ([
            'recipients_to',
            'recipients_cc',
            'recipients_bcc',
            'attachments',
        ] as $key) {
            $data[$key] = array_values(maybe_unserialize($data[$key]));
        }

        return $data;
    }

    public function markAsRead()
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix . Database::TABLE,
            ['is_read' => 1],
            ['id' => $this->id],
            ['%d'],
            ['%d']
        );
    }

    public function bulkDelete(array $ids = [])
    {
        global $wpdb;

        $ids    = array_map('absint', $ids);
        $ids    = array_filter($ids);
        $ids    = array_unique($ids);
        $format = implode(', ', array_fill(0, count($ids), '%d'));
        $table  = $wpdb->prefix . Database::TABLE;

        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table} WHERE ID IN ($format)",
                $ids
            )
        );
    }
}
