<?php

namespace frontend\helpers;

use common\models\Holiday;
use yii\helpers\ArrayHelper;

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
        $start = $start - ($start % 84600);
        $result = [ 'due' => 0, 'ndays' => 0];
        $ducount = $duration;
        $ndays = 0;
        $holidays = Holiday::find()->andWhere(['between', 'start', $start, $start + (30 * 84600)])->all();
        $i = 1;
        while ($i <= $ducount) {
            $start = $start + self::DAY_IN_SECONDS;
            $isweekend = (!$weekend) ? false : ArrayHelper::isIn(date('N', $start), $weekend);
            $isholiday = ArrayHelper::isIn($start, ArrayHelper::getColumn($holidays, 'start'), true);
            if (!($isweekend || $isholiday))
                $i++;
            else $ndays++;
        }
        $result['due'] = $start;
        $result['ndays'] = $ndays;
        return $result;
    }
}