<?php

namespace Fsylum\EmailTools;

class Helper
{
    public static function sanitizeEmailsFromTextarea(string $data = '')
    {
        if (empty($data)) {
            return [];
        }

        $emails = explode("\n", str_replace("\r", '', $data));
        $emails = array_filter($emails);
        $emails = array_map('trim', $emails);

        return $emails;
    }

    public static function parsePHPMailerEmails(array $emails = [])
    {
        $emails = array_merge([], ...$emails);
        $emails = array_filter($emails);

        if (empty($emails)) {
            return null;
        }

        return serialize($emails);
    }

    public static function parsePHPMailerAttachments(array $attachments = [])
    {
        $attachments = wp_list_pluck($attachments, 0);
        $attachments = array_filter($attachments);

        if (empty($attachments)) {
            return null;
        }

        return serialize($attachments);
    }

}
