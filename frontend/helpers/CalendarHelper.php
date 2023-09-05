<?php

namespace frontend\helpers;

use common\models\Holiday;
use DatePeriod;
use DateTime;
use DateInterval;
use yii\helpers\ArrayHelper;

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
       foreach($period as $key => $value)
       {
            $value->setTime(0,0);
            if (self::isWeekend($value))
            $weekends[] = [ 
                'start' => date(DATE_ATOM, $value->getTimeStamp()),
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

    public static function isHoliday($date)
    {
        return Holiday::find()
            ->andWhere(['<=', 'start' => $date])
            ->andWhere(['>=', 'end' => $date])->count() > 0;
    }

    public static function isNonWorkday(int $date, $weekend = [6, 7])
    {
        //date_default_timezone_set($timezone);
        $isholiday = false;
        $isweekend = ArrayHelper::isIn(date('N', $date), $weekend);
        if (!$isweekend)
            $isholiday = Holiday::find()->andWhere(['<=', 'start' => $date])->andWhere(['>=', 'end' => $date])->count() > 0;
        return !($isweekend||$isholiday);
    }

    public static function toTimeStamp($date)
    {
        $date = new DateTime($date);
        return $date->getTimeStamp();
    }
}