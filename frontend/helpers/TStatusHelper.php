<?php
namespace frontend\helpers;

use common\models\User;
use common\models\TicketStatus;
use yii\helpers\Html;

class TStatusHelper
{
    const OPEN = 'O';
    const SUSPENDED = 'S';
    const PROGRESSING = 'PR';
    const RESOLVED = 'RNI';
    const RESOLVED_IT = 'RIT';
    const CLOSED_DOUBLE = 'CDT';
    const CLOSED_RESOLVED = 'CRA';
    const CLOSED_NA = 'CNA';

    private static $statuses = [
        self::OPEN => [
            'icon' => '<i class="fa-solid fa-door-open fa-fw text-primary"></i>', 
            'label' => 'Open',
            'group' => 'Open',
            'tooltip' => 'Buka tiket servis No. %number$s.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ['report', 'id' => null, 'cmd' => self::OPEN],
            'next_status' => [self::SUSPENDED, self::PROGRESSING, self::RESOLVED, self::RESOLVED_IT, self::CLOSED_NA, self::CLOSED_DOUBLE]
        ],
        self::SUSPENDED => [
            'icon' => '<i class="fa-solid fa-lock fa-fw text-danger"></i>',
            'label' => 'Suspended',
            'group' => 'Progress',
            'tooltip' => 'Suspend tiket servis No. %number$s.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR],
            'url' => ['report', 'id' => null, 'cmd' => self::SUSPENDED],
            'next_status' => [self::OPEN, self::CLOSED_NA]
        ],
        self::PROGRESSING => [
            'icon' => '<i class="fa-solid fa-list fa-fw"></i>', 
            'label' => 'Progress',
            'group' => 'Progress',
            'tooltip' => 'Buat laporan progress tiket servis No. %number$s.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_ENGINEER],
            'url' => ['report', 'id' => null, 'cmd' => self::PROGRESSING],
            'next_status' => [self::SUSPENDED, self::PROGRESSING, self::RESOLVED, self::RESOLVED_IT, self::CLOSED_NA]
        ],
        self::RESOLVED => [
            'icon' => '<i class="fa-solid fa-list-check fa-fw text-success"></i>', 
            'label' => 'Resolved (no Issue)',
            'group' => 'Progress',
            'tooltip' => 'Buat laporan resolusi tiket servis No. %number$s.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_ENGINEER],
            'url' => ['report', 'id' => null, 'cmd' => self::RESOLVED],
            'next_status' => [self::SUSPENDED, self::CLOSED_RESOLVED]
        ],
        self::RESOLVED_IT => [
            'icon' => '<i class="fa-solid fa-laptop-code fa-fw text-success"></i>', 
            'label' => 'Resolved (waiting for IT)',
            'group' => 'Progress',
            'tooltip' => 'Buat laporan resolusi tiket servis No. %number$s.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_ENGINEER],
            'url' => ['report', 'id' => null, 'cmd' => self::RESOLVED_IT],
            'next_status' => [self::SUSPENDED, self::CLOSED_RESOLVED]
        ],
        self::CLOSED_NA => [
            'icon' => '<i class="fa-solid fa-circle-xmark fa-fw text-danger"></i>', 
            'label' => 'Closed (no Action)',
            'group' => 'Closed',
            'tooltip' => 'Tutup tiket servis No. %number$s karena tidak ada tindakan.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ['report', 'id' => null, 'cmd' => self::CLOSED_NA]
        ],
        self::CLOSED_DOUBLE => [
            'icon' => '<i class="fa-solid fa-circle-xmark fa-fw text-warning"></i>', 
            'label' => 'Closed (duplicate Ticket)',
            'group' => 'Closed',
            'tooltip' => 'Tutup tiket servis No. %number$s karena double input.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ['report', 'id' => null, 'cmd' => self::CLOSED_DOUBLE]
        ],
        self::CLOSED_RESOLVED => [
            'icon' => '<i class="fa-solid fa-circle-check fa-fw text-success"></i>', 
            'label' => 'Closed (resolution accepted)',
            'group' => 'Closed',
            'tooltip' => 'Tutup tiket servis No. %number$s.',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ['report', 'id' => null, 'cmd' => self::CLOSED_RESOLVED]
        ],
    ];

    private static $groups = [
        'Open' => [self::OPEN],
        'Progress' => [self::PROGRESSING, self::RESOLVED, self::RESOLVED_IT],
        'Suspend' => [self::SUSPENDED],
        'Closed' => [self::CLOSED_NA, self::CLOSED_DOUBLE, self::CLOSED_RESOLVED],
    ];

    public static function createNextCommand($ticket)
    {
        $role = User::getUserRoleName();
        $ts = TicketStatus::findOne(['id' => $ticket->last_status_id]);
        $open = self::$statuses[self::OPEN];
        if (isset($ts))
        {
            $status = self::$statuses[$ts->code];
            if (isset($status['next_status']))
            {
                $nextsts = [];
                foreach($status['next_status'] as $st)
                {
                    $st = self::$statuses[$st];
                    if (in_array($role, $st['role']))
                    {
                        $st['url']['id'] = $ticket->id;
                        $nextsts[] = Html::a($st['icon'], $st['url'], 
                        [
                            'class' => 'text-decoration-none',
                            'arial-label' => $st['label'],
                            'title' => StringHelper::sprintfn($st['tooltip'], ['number' => $ticket->number])
                        ]);
                    }
                }
                return implode('  ', $nextsts);
            }
        }
        else return Html::a($open['icon'], $open['url'], 
        [
            'class' => 'text-decoration-none',
            'arial-label' => $open['label'],
            'title' => StringHelper::sprintfn($open['tooltip'], ['number' => $ticket->number])
        ]);
        return '';
    }

    public static function createStep($taction)
    {
        $status = self::$statuses[$taction->status->code];
        return [
            'title' => implode(' ', [date('d F Y', $taction->action_date), $status['label'], $taction->action]),
            'caption' => ['content' => $taction->summary, 'author' => $taction->engineer->full_name],
            'next' => isset($status['next_status']) ? $status['next_status'] : null
        ];
    }

    public static function getLabel($taction, $showtext = true)
    {
        $status = self::$statuses[$taction->status->code];
        if ($showtext) return implode(' ', [$status['icon'], $status['label']]);
        return $status['icon'];
    }

    public static function getRoleLabelOptions($user = null)
    {
        if (!isset($user)) $user = Yii::$app->user->id;
        $retlabels = self::$labels;
        $newlabels = [];
        ArrayHelper::remove($retlabels, 'default');
        ArrayHelper::remove($retlabels, User::ROLE_SYS_ADMINISTRATOR);
        ArrayHelper::remove($retlabels, User::getUserRoleName($user));
        if (User::getUserRoleName(Yii::$app->user->id) != User::ROLE_SYS_ADMINISTRATOR)
        {
            ArrayHelper::remove($retlabels, User::ROLE_ADMINISTRATOR);
        }
        foreach($retlabels as $role => $label)
        {
            $newlabels[$role] = implode(' ', $label);
        }
        return $newlabels;
    }
}

?>