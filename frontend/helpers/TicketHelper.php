<?php

namespace frontend\helpers;

use common\models\tickets\actions\closings\Awaiting;
use common\models\tickets\actions\closings\Closing;
use common\models\tickets\actions\closings\Duplicate;
use common\models\tickets\actions\closings\NoProblem;
use common\models\tickets\actions\closings\Normal;
use common\models\tickets\actions\ConcreteAction;
use common\models\tickets\actions\Discretion;
use common\models\tickets\actions\MetaAction;
use common\models\tickets\actions\Open;
use common\models\tickets\actions\Recommendation;
use common\models\tickets\actions\Repair;
use common\models\tickets\Ticket;
use Yii;
use yii\helpers\ArrayHelper;

class TicketHelper 
{
    public static function getSLAStatus(Ticket $ticket) {
        //$due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, $ticket->contract->sla);
        $due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, 14);
        $ticket->closing;
        if (!empty($ticket->closing))
        {
            $days = floor(($due['due'] - $ticket->closing->created_at)/DateTimeHelper::DAY_IN_SECONDS) - $due['ndays'];
            if ($due['due'] < $ticket->closing->created_at)
            {
                return ['id' => 9, 'status' => 'SLA lewat '.(abs($days)).' hari', 'color' => 'bg-danger', 'description' => 'SLA tidak tercapai'];
            }
            else return ['id' => 10, 'status' => 'SLA '.$days.' hari', 'color' => 'bg-success', 'description' => 'SLA tercapai'];
        }
        else
        {
            $days = floor(($due['due'] - time())/DateTimeHelper::DAY_IN_SECONDS);
            if ($days >= 0)
                return ['id' => 11, 'status' => 'SLA sisa '.(14 - $days).' hari', 'color' => 'bg-primary', 'description' => 'SLA sisa '.$days.' hari'];
            else
                return ['id' => 12, 'status' => 'SLA lewat '.(abs($days)).' hari', 'color' => 'bg-warning', 'description' => 'SLA terlewat '.$days.' hari'];
        }
    }

    public static function getStatuses(Ticket $ticket)
    {
        $stats = [];
        $statuses = Yii::$app->params['ticketStatusRegister'];
        //echo VarDumper::dump($ticket->lastAction);
        if (!empty($ticket->lastAction)){
            $actionclass = $ticket->lastAction::class;           
            if (is_subclass_of($actionclass, Closing::class))
                $stats[] = ArrayHelper::getValue($statuses, Closing::class);
            $stats[] = ArrayHelper::getValue($statuses, $actionclass);
        }
        $ticket->discretion;
        if (!empty($ticket->discretion))
            $stats[] = ArrayHelper::getValue($statuses, $ticket->discretion::class);
        $stats[] = self::getSLAStatus($ticket);
        return $stats;
    }

    public static function can(Ticket $ticket, $class){
        if (is_subclass_of($class, ConcreteAction::class)){
            if ($ticket->lastAction::class instanceof Open && is_subclass_of($class, Repair::class))
                return true;
            if ($ticket->lastAction::class instanceof Repair && is_subclass_of($class, Closing::class))
                return true;
            if (is_subclass_of($ticket->lastAction, Closing::class)) return false;
        }
        elseif (is_subclass_of($class, MetaAction::class)){
            if (is_subclass_of($class, Recommendation::class)) 
                return true;
            if (empty($ticket->discretion) && is_subclass_of($class, Discretion::class)) 
                return true;
            return false;
        }
        return false;
    }
}