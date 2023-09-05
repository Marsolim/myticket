<?php

namespace frontend\helpers;

class DateTimeHelper
{
    const DAY_IN_SECONDS = 86400;
    const HOUR_IN_SECONDS = 3600;
    const MINUTE_IN_SECONDS = 60;
    /**
     * Calculate due date based on $start in $duration days exclude weekend and holidays.
     * @param int $start Start of the date in unixtimestamp
     * @param int $duration Durations in days
     * @param array $weekend The defined weekends of the week.
     * @return int The due date in unixtimestamp.
     */
    public static function due(int $start, int $duration, $weekend = [6, 7])
    {
        $ducount = $duration;
        if (!$weekend) return $start + ($duration * self::DAY_IN_SECONDS);
        $i = 1;
        while ($i <= $ducount) {
            $start = $start + self::DAY_IN_SECONDS;
            if (CalendarHelper::isNonWorkday($start, $weekend))
                $i++;
        }
        return $start;
    }
}