<?php

namespace App\Helpers;

use App\TicketNotification;
use App\CarbonCopy;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    /**
     * Get escalation admin emails based on warning stage.
     *
     * @param string $stage ('day3' or 'day5')
     * @param string $type ('unread' or 'unanswered')
     * @return array
     */
    public static function getEscalationEmails($stage, $type)
    {
        $admins = [
            'npacheco@ics.com.ph',
            'jwong@ics.com.ph',
            'macosta@ics.com.ph',
        ];

        if ($type === 'unanswered') {
            return $stage === 'day5' ? $admins : [];
        }

        // Default for unread: day3 and day5
        return in_array($stage, ['day3', 'day5']) ? $admins : [];
    }

    /**
     * Resolve warning stage from notification type string.
     *
     * @param string $notificationType
     * @return string
     */
    public static function resolveWarningStage($notificationType)
    {
        if (strpos($notificationType, '5_day') !== false) {
            return 'day5';
        }

        if (strpos($notificationType, '3_day') !== false || strpos($notificationType, 'daily') !== false) {
            return 'day3';
        }

        return 'day1';
    }

    /**
     * Get display name from nickname or full name.
     *
     * @param string|null $nickname
     * @param string|null $fullName
     * @return string
     */
    public static function displayName($nickname, $fullName)
    {
        $nickname = trim((string) $nickname);
        if ($nickname !== '') {
            return $nickname;
        }

        $firstName = trim((string) strtok(trim((string) $fullName), ' '));
        return $firstName !== '' ? $firstName : 'Engineer';
    }

    /**
     * Normalize email string or array into validated array of emails.
     *
     * @param mixed $emails
     * @return array
     */
    public static function normalizeEmails($emails)
    {
        if (empty($emails)) {
            return [];
        }

        if (is_array($emails)) {
            $emails = implode(',', $emails);
        }

        $normalized = preg_replace('/[\s;]+/', ',', strtolower($emails));
        $candidates = array_filter(array_map('trim', explode(',', $normalized)));

        return array_values(array_filter($candidates, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }));
    }

    /**
     * Get default transformation for BCC emails.
     *
     * @return array
     */
    public static function getDefaultBCC()
    {
        return self::normalizeEmails('dramos@ics.com.ph,mescario@ics.com.ph,jesurena@ics.com.ph');
    }
}
