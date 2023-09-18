<?php

namespace frontend\helpers;

use common\models\tickets\Ticket;
use yii\helpers\Html;

class TicketHelper {

    public static function getPrimaryStatus(Ticket $ticket) {
        if (empty($ticket->repairs) && $ticket->status == 1)
            return ['id' => 1, 'status' => 'Open', 'class' => 'small badge rounded-pill bg-primary text-light', 'description' => 'Open = Belum kunjungan'];
        elseif((!empty($ticket->repairs) && $ticket->status == 1) || $ticket->status == 2)
            return ['id' => 2, 'status' => 'Pending', 'class' => 'small badge rounded-pill bg-secondary text-light', 'description' => 'Pending = Pekerjaan belum selesai'];
        elseif($ticket->status >= 3)
            return ['id' => 3, 'status' => 'Selesai', 'class' => 'small badge rounded-pill bg-success text-light', 'description' => 'Selesai = Pekerjaan selesai'];
        else return null;
    }

    public static function getSecondaryStatus(Ticket $ticket) {
        if ($ticket->status == 3)
            return ['id' => 3, 'status' => 'No Problem', 'class' => 'small badge rounded-pill bg-info text-light', 'description' => 'No Problem = Tidak ada masalah'];
        elseif($ticket->status == 5)
            return ['id' => 5, 'status' => 'Duplicate', 'class' => 'small badge rounded-pill bg-danger text-light', 'description' => 'Duplicate = Double input AHO'];
        elseif($ticket->status == 7)
            return ['id' => 7, 'status' => 'Waiting', 'class' => 'small badge rounded-pill bg-info text-light', 'description' => 'Waiting = Menunggu remote IT'];
        else return null;
    }

    public static function getContractStatus(Ticket $ticket) {
        if (!empty($ticket->discretion))
            return ['id' => 8, 'status' => 'MC <i class="fa fa-xmark"></i>', 'class' => 'small badge rounded-pill bg-warning text-light', 'description' => 'Tidak tercover MC'];
        else return null;
    }

    public static function getSLAStatus(Ticket $ticket) {
        if (((empty($ticket->updated_at) ? time() : $ticket->updated_at) - $ticket->created_at > (14 * 86400)))
            return ['id' => 9, 'status' => 'SLA <i class="fa fa-xmark"></i>', 'class' => 'small badge rounded-pill bg-danger text-light', 'description' => 'SLA tidak tercapai'];
        else return null;
    }

    public static function renderStatus($status)
    {
        if (empty($status)) return null;
        return Html::tag('span', Html::a($status['status'], ['ticket/index', ['status' => $status['id']]], ['class' => 'text-decoration-none text-light']), ['class' => $status['class'], 'title' => $status['description']]);
    }
}