<?php

namespace Fsylum\EmailTools;

class Helper
{
    public static function sanitizeEmailsFromTextarea($data = '')
    {
        if (empty($data)) {
            return [];
        }

        $emails = explode("\n", str_replace("\r", '', $data));
        $emails = array_filter($emails);
        $emails = array_map('trim', $emails);

        return $emails;
    }

    public static function sanitizePHPMailerEmails($emails = [])
    {
        $emails = array_merge([], ...$emails);
        $emails = array_filter($emails);

        if (empty($emails)) {
            return null;
        }

        return serialize($emails);
    }

    public static function sanitizePHPMailerAttachments($attachments = [])
    {
        $attachments = wp_list_pluck($attachments, 0);
        $attachments = array_filter($attachments);

        if (empty($attachments)) {
            return null;
        }

        return serialize($attachments);
    }

    public static function jsRedirect($url = '')
    {
        echo '<script>window.location = "' . $url . '"</script>';
        exit;
    }
}
