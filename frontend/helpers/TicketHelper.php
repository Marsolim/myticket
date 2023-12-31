<?php

namespace frontend\helpers;

use common\models\actors\Company;
use common\models\actors\Depot;
use common\models\actors\Engineer;
use common\models\actors\Store;
use common\models\tickets\actions\Assignment;
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
use frontend\models\GeneralManager;
use frontend\models\StoreManager;
use kartik\helpers\Enum;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TicketHelper 
{
    public static function getSLAStatus(Ticket $ticket) {
        //$due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, $ticket->contract->sla);
        if (!isset($ticket->sla_due)){
            $ticket->sla_due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, $ticket->store->contract->sla)['due'];
            $ticket->save();
        } 
        //$due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, 14);
        $status = 'SLA exp. '.(date('d-m-Y', $ticket->sla_due));
        if (!empty($ticket->lastAction) && is_subclass_of($ticket->lastAction, Closing::class))
        {
            //$days = floor(($due['due'] - $ticket->lastAction->created_at)/DateTimeHelper::DAY_IN_SECONDS) - $due['ndays'];
            if ($ticket->sla_due < $ticket->lastAction->created_at)
            {
                return ['id' => 9, 'status' => $status, 'color' => 'bg-danger', 'description' => 'SLA tidak tercapai'];
            }
            else return ['id' => 10, 'status' => $status, 'color' => 'bg-success', 'description' => 'SLA tercapai'];
        }
        else
        {
            //$days = floor(($due['due'] - time())/DateTimeHelper::DAY_IN_SECONDS);
            if ($ticket->sla_due >= time()){
                $days = floor(($ticket->sla_due - time())/DateTimeHelper::DAY_IN_SECONDS);
                return ['id' => 11, 'status' => $status, 'color' => 'bg-primary', 'description' => "SLA will exp. in $days days"];
            }
            else
                return ['id' => 12, 'status' => $status, 'color' => 'bg-warning', 'description' => 'SLA was exp. '.Enum::timeElapsed(date(DATE_ATOM, $ticket->sla_due))];
        }
    }

    public static function getStatuses(Ticket $ticket)
    {
        $stats = [];
        $statuses = Yii::$app->params['ticketStatusRegister'];
        //echo VarDumper::dump($ticket->lastAction);
        if (!empty($ticket->lastAction)){
            if (is_subclass_of($ticket->lastAction, Closing::class))
                $stats[] = ArrayHelper::getValue($statuses, Closing::class);
            $stats[] = ArrayHelper::getValue($statuses, $ticket->lastAction::class);
        }
        $ticket->discretion;
        if (!empty($ticket->discretion))
            $stats[] = ArrayHelper::getValue($statuses, $ticket->discretion::class);
        $stats[] = self::getSLAStatus($ticket);
        return $stats;
    }

    public static function can(Ticket $ticket, $class){
        return match (true) {
            $ticket->lastAction instanceof Open => 
                match ($class) {
                    Repair::class, Duplicate::class, NoProblem::class => true,
                    Discretion::class, Assignment::class, Recommendation::class => true,
                    default => false,
                } ,
            $ticket->lastAction instanceof Repair =>
                match ($class) {
                    Awaiting::class, Normal::class, Repair::class => true,
                    Discretion::class, Assignment::class, Recommendation::class => true,
                    default => false,
                } ,
            $ticket->lastAction instanceof Closing => false,
            default => false
        };
    }

    public static function summary(){
        $query = new Query();
        $columns = [
            'b' => "sum(if((a.type = 'common\\\\models\\\\tickets\\\\actions\\\\Open'),1,0))",
            'p' => "sum(if((a.type = 'common\\\\models\\\\tickets\\\\actions\\\\Repair'),1,0))",
            's' => "sum(if((a.type = 'common\\\\models\\\\tickets\\\\actions\\\\closings\\\\Normal'),1,0))",
            'r' => "sum(if((a.type = 'common\\\\models\\\\tickets\\\\actions\\\\closings\\\\Awaiting'),1,0))",
            'n' => "sum(if((a.type = 'common\\\\models\\\\tickets\\\\actions\\\\closings\\\\NoProblem'),1,0))",
            'd' => "sum(if((a.type = 'common\\\\models\\\\tickets\\\\actions\\\\closings\\\\Duplicate'),1,0))",
        ];
        $user = UserHelper::findUser(null);
        if ($user instanceof StoreManager || $user instanceof Engineer){
            $columns['customer_id'] = 'c.id';
            $columns['customer_name'] = "CONCAT(c.code, ' - ', c.name)";
        }
        else if ($user instanceof GeneralManager){
            $columns['customer_id'] = 'd.id';
            $columns['customer_name'] = "CONCAT(d.code, ' - ', d.name)";
        }
        else{
            $columns['customer_id'] = 'd.id';
            $columns['customer_name'] = "CONCAT(d.code, ' - ', d.name)";
        }
        $query->select($columns)
            ->from(['t' => 'ticket'])
            ->leftJoin(['a' => 'action'], 't.last_action_id = a.id')
            ->leftJoin(['c' => 'customer'], ['AND', 't.customer_id = c.id', ['c.type' => Store::class]])
            ->leftJoin(['d' => 'customer'], ['AND', 'c.parent_id = d.id', ['d.type' => Depot::class]])
            ->leftJoin(['cp' => 'customer'], ['AND', 'd.parent_id = cp.id', ['cp.type' => Company::class]]);
        if ($user instanceof StoreManager)
            $query->where(['d.id' => $user->depot->id])->groupBy(['c.id']);
        else if ($user instanceof GeneralManager)
            $query->where(['cp.id' => $user->company->id])->groupBy(['d.id']);
        else if ($user instanceof Engineer)
            $query
                ->leftJoin(['as' => 'action'], ['AND', 't.id = as.ticket_id', ['as.type' => Assignment::class]])
                ->where(['as.user_id' => $user->id])->groupBy(['d.id']);
        else $query->groupBy(['d.id']);
        return $query;
    }
}