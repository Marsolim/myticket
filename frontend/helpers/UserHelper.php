<?php
namespace frontend\helpers;

use common\models\actors\User;
use yii\helpers\ArrayHelper;
use Yii;

class UserHelper
{
    public static function getRole($user = null)
    {
        if (!isset($user)) $user = Yii::$app->user->id;
        
        $user = self::parseParameter($user);

        $roles = Yii::$app->authManager->getRolesByUser($user);
        if (!$roles) {
            return null;
        }

        reset($roles);
        /* @var $role \yii\rbac\Role */
        $role = current($roles);

        return $role->name;
    }

    private static function parseParameter($p)
    {
        if (is_string($p))
        {
            $ud = User::find()->where(['username' => $p])
            ->orWhere(['full_name' => $p])
            ->orWhere(['email' => $p])->one();
            $p = isset($ud) ? $ud->id : null;
        }
        else if (is_object($p))
        {
            $p = $p->id;
        }
        return $p;

    }

    public static function setRole($role, $user = null)
    {
        if (!isset($user)) $user = Yii::$app->user->id;
        $user = self::parseParameter($user);

        if (isset($user))
        {
            $user = User::findOne(['id' => $user]);
            $auth = Yii::$app->authManager;
            $prevrole = $user->role;
            if ($prevrole == $role) return true;
            if (isset($prevrole))
                $auth->revoke($auth->getRole($prevrole), $user->id);
            $auth->assign($auth->getRole($role), $user->id);
            return true;
        }
        return false;
    }
    
    /**
     * @var string $role
     * @var int $userid
     */
    public static function isMemberOfRole($role, $user = null)
    {
        $user = isset($user) ? $user : Yii::$app->user->id;
        $user = self::parseParameter($user);
        $roles = Yii::$app->authManager->getRolesByUser($user);
        $rolenames = ArrayHelper::getColumn($roles, 'name');
        if (is_array($role))
        {
            $result = false;
            foreach($role as $r)
            {
                $result |= in_array($r, $rolenames);
            }
            return $result;
        }
        return in_array($role, $rolenames);
    }

    public static function isAdministrator($user = null)
    {
        return self::isMemberOfRole([User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR], $user);
    }
    
    public static function isSystemAdmin($user = null)
    {
        return self::isMemberOfRole(User::ROLE_SYS_ADMINISTRATOR, $user);
    }

    public static function isEngineer($user = null)
    {
        return self::isMemberOfRole(User::ROLE_ENGINEER, $user);
    }
    
    public static function isManager($user = null)
    {
        return self::isMemberOfRole([User::ROLE_STORE_MANAGER, User::ROLE_GENERAL_MANAGER], $user);
    }
    
    public static function isStoreManager($user = null)
    {
        return self::isMemberOfRole(User::ROLE_STORE_MANAGER, $user);
    }
    
    public static function isGeneralManager($user = null)
    {
        return self::isMemberOfRole(User::ROLE_GENERAL_MANAGER, $user);
    }

    public static function findEngineers()
    {
        return User::find()
            ->join('INNER JOIN',['a' => 'auth_assignment'], 'user.id = a.user_id')
            ->where(['a.item_name' => User::ROLE_ENGINEER]);
    }

    public static function isSelf($user)
    {
        $user = self::parseParameter($user);
        return Yii::$app->user->id == $user;
    }
}

?>