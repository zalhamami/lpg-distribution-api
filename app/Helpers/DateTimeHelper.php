<?php

namespace App\Helpers;

use DateTime;

class DateTimeHelper
{
    /**
     * @param $date
     * @return false|string|null
     */
    public static function convertIsoToDatetime($date)
    {
        if (!$date) return null;
        $datetime = date('Y-m-d H:i:s', strtotime($date));
        return $datetime;
    }

    /**
     * @param $date
     * @return string|null
     * @throws \Exception
     */
    public static function convertDatetimeToIso($date)
    {
        if (!$date) return null;
        $datetime = new DateTime($date);
        return $datetime->format(DateTime::ATOM);
    }
}
