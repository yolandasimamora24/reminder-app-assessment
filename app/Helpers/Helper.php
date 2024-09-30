<?php

namespace App\Helpers;

use Carbon\Carbon;

class Helper
{

    /**
     * The function returns an array of reminder status.
     */
    public static function reminderStatus(): array
    {
        return [
            'pending', 
            'completed',
            'cancelled',
        ];
    } 

    /**
     * The getRandomDate function generates a random date and time within the current year and returns
     * it in the format "Y-m-d H:i:s".
     * 
     * @return string a random date and time in the format "Y-m-d H:i:s".
     */
    public static function getRandomDate(string $format = 'Y-m-d H:i:s'): string
    {
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;

        $minDate = Carbon::create($currentYear, 1, 1, 0, 0, 0);
        $maxDate = Carbon::create($nextYear, 12, 31, 23, 59, 59);

        $randomDate = Carbon::createFromTimestamp(rand($minDate->timestamp, $maxDate->timestamp));

        return $randomDate->format($format);
    }

    public static function maskEmail(string $email): string
    {
        list($localPart, $domain) = explode('@', $email);
        $localPartLength = strlen($localPart);

        if ($localPartLength > 1) {
            $maskedLocalPart = $localPart[0] . str_repeat('*', max($localPartLength - 2, 0)) . $localPart[$localPartLength - 1];
        } else {
            // Handle case where local part is only one character or empty
            $maskedLocalPart = $localPart . str_repeat('*', max($localPartLength - 1, 0));
        }
        return $maskedLocalPart . '@' . $domain;
    }

}