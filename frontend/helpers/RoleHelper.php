<?php
namespace frontend\helpers;

use common\models\User;

class RoleHelper
{
    private static $labels = [
        User::ROLE_SYS_ADMINISTRATOR => [
            'icon' => '<i class="fa-solid fa-user-ninja fa-fw"></i>', 
            'label' => 'System Administrator'
        ],
        User::ROLE_ADMINISTRATOR => [
            'icon' => '<i class="fa-solid fa-user-tie fa-fw"></i>', 
            'label' => 'Administrator'
        ],
        User::ROLE_ENGINEER => [
            'icon' => '<i class="fa-solid fa-user-gear fa-fw"></i>', 
            'label' => 'Engineer'
        ],
        User::ROLE_STORE_MANAGER => [
            'icon' => '<i class="fa-solid fa-user-tag fa-fw"></i>', 
            'label' => 'Store Manager'
        ],
        User::ROLE_GENERAL_MANAGER => [
            'icon' => '<i class="fa-solid fa-user-secret fa-fw"></i>', 
            'label' => 'General Manager'
        ],
        'default' => [
            'icon' => '<i class="fa-solid fa-users fa-fw"></i>', 
            'label' => 'Assign Role'
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