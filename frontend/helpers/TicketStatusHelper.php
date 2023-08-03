<?php
namespace frontend\helpers;

use common\models\User;

class TicketStatusHelper
{
    private static $statuses = [
        'O' => [
            'icon' => '<i class="fa-solid fa-user-ninja fa-fw"></i>', 
            'label' => 'Open',
            'group' => 'Open',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => '',
            'next_status' => ['CNA', 'CDT', 'PR', 'RNI', 'RIT', 'S']
        ],
        'S' => [
            'icon' => '<i class="fa-solid fa-user-tie fa-fw"></i>',
            'label' => 'Suspended',
            'group' => 'Progress',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR],
            'url' => '',
            'next_status' => ['CNA', 'O']
        ],
        'PR' => [
            'icon' => '<i class="fa-solid fa-user-gear fa-fw"></i>', 
            'label' => 'Progress',
            'group' => 'Progress',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_ENGINEER],
            'url' => '',
            'next_status' => ['PR', 'RNI', 'RIT', 'S']
        ],
        'RNI' => [
            'icon' => '<i class="fa-solid fa-user-tag fa-fw"></i>', 
            'label' => 'Resolved (no Issue)',
            'group' => 'Progress',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_ENGINEER],
            'url' => '',
            'next_status' => ['CRA', 'S']
        ],
        'RIT' => [
            'icon' => '<i class="fa-solid fa-user-secret fa-fw"></i>', 
            'label' => 'Resolved (waiting for IT)',
            'group' => 'Progress',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_ENGINEER],
            'url' => '',
            'next_status' => ['CRA', 'S']
        ],
        'CNA' => [
            'icon' => '<i class="fa-solid fa-users fa-fw"></i>', 
            'label' => 'Closed (no Action)',
            'group' => 'Closed',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ''
        ],
        'CDT' => [
            'icon' => '<i class="fa-solid fa-users fa-fw"></i>', 
            'label' => 'Closed (duplicate Ticket)',
            'group' => 'Closed',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ''
        ],
        'CRA' => [
            'icon' => '<i class="fa-solid fa-users fa-fw"></i>', 
            'label' => 'Closed (resolution accepted)',
            'group' => 'Closed',
            'role' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER],
            'url' => ''
        ],
    ];

    public static function getLabel($role = 'default', $showtext = true)
    {
        $label = self::$labels[$role];
        if ($showtext) return implode(' ', $label);
        return $label['icon'];
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