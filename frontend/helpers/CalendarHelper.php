<?php

namespace frontend\helpers;

use DatePeriod;
use DateTime;
use DateInterval;

class CalendarHelper
{

    public static function getWeekends($start, $end, $weekends = [6,7])
    {
        //DatePeriod::IN
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end)
       );
       $weekends = [];
       date_default_timezone_set("Asia/Jakarta");
       foreach($period as $key => $value)
       {
            $value->setTime(0,0);
            if (self::isWeekend($value))
            $weekends[] = [ 
                'start' => date(DATE_ISO8601, $value->getTimeStamp()),
                'allDay' => true,
                'overlap' => false
            ];
       }
       return $weekends;
    }

    public static function isWeekend($date) 
    {
        return (date('N', $date->getTimestamp()) >= 6);
    }

    public static function toTimeStamp($date)
    {
        $date = new DateTime($date);
        return $date->getTimeStamp();
    }
}