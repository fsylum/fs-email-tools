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
}
