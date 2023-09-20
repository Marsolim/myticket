<?php

namespace frontend\helpers;

use common\models\tickets\actions\closings\Awaiting;
use common\models\tickets\actions\closings\Closing;
use common\models\tickets\actions\closings\Duplicate;
use common\models\tickets\actions\closings\NoProblem;
use common\models\tickets\actions\Discretion;
use common\models\tickets\actions\Open;
use common\models\tickets\actions\Repair;
use common\models\tickets\Ticket;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class TicketHelper {

    static $statuses = [
        Open::class => ['id' => 1, 'status' => 'Open', 'color' => 'bg-primary', 'description' => 'Open = Belum kunjungan'],
        Repair::class => ['id' => 2, 'status' => 'Pending', 'color' => 'bg-secondary', 'description' => 'Pending = Pekerjaan belum selesai'],
        Closing::class => ['id' => 3, 'status' => 'Selesai', 'color' => 'bg-success', 'description' => 'Selesai = Pekerjaan selesai'],
    ];

    static $closestatuses = [
        Awaiting::class => ['id' => 2, 'status' => 'No Problem', 'color' => 'bg-info', 'description' => 'No Problem = Tidak ada masalah'],
        NoProblem::class => ['id' => 3, 'status' => 'Duplicate', 'color' => 'bg-danger', 'description' => 'Duplicate = Double input AHO'],
        Duplicate::class => ['id' => 4, 'status' => 'Waiting', 'color' => 'bg-info', 'description' => 'Waiting = Menunggu remote IT'],
    ];

    static $contractstatuses = [
        Discretion::class => ['id' => 2, 'status' => 'MC <i class="fa fa-xmark"></i>', 'color' => 'bg-warning', 'description' => 'Tidak tercover MC']
    ];

    public static function getPrimaryStatus(Ticket $ticket) {
        if (empty($ticket->repairs) && $ticket->status == 1)
            return ['id' => 1, 'status' => 'Open', 'color' => 'bg-primary', 'description' => 'Open = Belum kunjungan'];
        elseif((!empty($ticket->repairs) && $ticket->status == 1) || $ticket->status == 2)
            return ['id' => 2, 'status' => 'Pending', 'color' => 'bg-secondary', 'description' => 'Pending = Pekerjaan belum selesai'];
        elseif($ticket->status >= 3)
            return ['id' => 3, 'status' => 'Selesai', 'color' => 'bg-success', 'description' => 'Selesai = Pekerjaan selesai'];
        else return null;
    }

    public static function getSecondaryStatus(Ticket $ticket) {
        if ($ticket->status == 3)
            return ['id' => 4, 'status' => 'No Problem', 'color' => 'bg-info', 'description' => 'No Problem = Tidak ada masalah'];
        elseif($ticket->status == 5)
            return ['id' => 5, 'status' => 'Duplicate', 'color' => 'bg-danger', 'description' => 'Duplicate = Double input AHO'];
        elseif($ticket->status == 7)
            return ['id' => 7, 'status' => 'Waiting', 'color' => 'bg-info', 'description' => 'Waiting = Menunggu remote IT'];
        else return null;
    }

    public static function getContractStatus(Ticket $ticket) {
        if (!empty($ticket->discretion))
            return ['id' => 8, 'status' => 'MC <i class="fa fa-xmark"></i>', 'color' => 'bg-warning', 'description' => 'Tidak tercover MC'];
        else return null;
    }

    public static function getSLAStatus(Ticket $ticket) {
        //$due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, $ticket->contract->sla);
        $due = DateTimeHelper::due(empty($ticket->created_at) ? time() : $ticket->created_at, 14);
        $ticket->closing;
        if (!empty($ticket->closing))
        {
            $days = floor(($due - $ticket->closing->created_at)/DateTimeHelper::DAY_IN_SECONDS);
            if ($due < $ticket->closing->created_at)
            {
                return ['id' => 9, 'status' => 'SLA '.$days.' hari', 'color' => 'bg-danger', 'description' => 'SLA tidak tercapai'];
            }
            else return ['id' => 10, 'status' => 'SLA '.$days.' hari', 'color' => 'bg-success', 'description' => 'SLA tercapai'];
        }
        else
        {
            $days = floor(($due - time())/DateTimeHelper::DAY_IN_SECONDS);
            if ($days >= 0)
                return ['id' => 11, 'status' => 'SLA '.$days.' hari', 'color' => 'bg-primary', 'description' => 'SLA sisa '.$days.' hari'];
            else
                return ['id' => 12, 'status' => 'SLA '.$days.' hari', 'color' => 'bg-warning', 'description' => 'SLA terlewat '.$days.' hari'];
        }
    }

    public static function getStatuses(Ticket $ticket)
    {
        $stats = [];
        $ticket->lastAction;
        if (!empty($ticket->lastAction)){
            $actionclass = $ticket->lastAction::class;           
            $stats[] = ArrayHelper::getValue(self::$statuses, $actionclass);
            if (is_subclass_of($actionclass, Closing::class)){
                $stats[] = ArrayHelper::getValue(self::$statuses, Closing::class);
                $stats[] = ArrayHelper::getValue(self::$closestatuses, $actionclass);
            }
        }
        $ticket->discretion;
        if (!empty($ticket->discretion))
            $stats[] = ArrayHelper::getValue(self::$contractstatuses, $ticket->discretion::class);
        $stats[] = self::getSLAStatus($ticket);
        return $stats;
    }
}