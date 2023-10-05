<?php
namespace frontend\helpers;

use common\models\actors\Administrator;
use common\models\actors\Engineer;
use common\models\actors\User;
use frontend\models\GeneralManager;
use frontend\models\StoreManager;
use InvalidArgumentException;
use yii\helpers\ArrayHelper;
use Yii;
use yii\web\IdentityInterface;

class UserHelper
{
    public static function getRole($user = null)
    {
        $user = self::findUser($user);

        $roles = Yii::$app->authManager->getRolesByUser($user);
        if (!$roles) {
            return null;
        }

        reset($roles);
        /* @var $role \yii\rbac\Role */
        $role = current($roles);

        return $role->name;
    }

    public static function findUser($p)
    {
        if (is_string($p))
        {
            $ud = User::find()->where(['username' => $p])
            ->orWhere(['full_name' => $p])
            ->orWhere(['email' => $p])->one();
            $p = isset($ud) ? $ud : null;
        }
        else if (($p instanceof IdentityInterface && !($p instanceof User)) ||
            is_int($p))
        {
            $p = User::findOne(['id' => is_int($p) ? $p : $p->id]);
        }
        else if (is_null($p)) $p = User::findOne(['id' => Yii::$app->user->id]);
        else throw new InvalidArgumentException();
        return $p;
    }

    public static function setRole($role, $user = null)
    {
        $user = self::findUser($user);

        if (isset($user))
        {
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
        $user = self::findUser($user);
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
        $user = self::findUser($user);
        return $user instanceof Administrator;
    }
    
    public static function isSystemAdmin($user = null)
    {
        $user = self::findUser($user);
        return $user instanceof Administrator;
    }

    public static function isEngineer($user = null)
    {
        $user = self::findUser($user);
        if ($user instanceof Engineer) return $user;
        return null;
    }
    
    public static function isManager($user = null)
    {
        $user = self::findUser($user);
        return $user instanceof GeneralManager || $user instanceof StoreManager;
    }
    
    public static function isStoreManager($user = null)
    {
        $user = self::findUser($user);
        if ($user instanceof StoreManager) return $user;
        return null;
    }
    
    public static function isGeneralManager($user = null)
    {
        $user = self::findUser($user);
        if ($user instanceof GeneralManager) return $user;
        return null;
    }

    public static function findEngineers()
    {
        return Engineer::find();
    }

    public static function isSelf($user)
    {
        $user = self::findUser($user);
        return Yii::$app->user->id == $user->id;
    }

    public static function renderUserCommands($user){
        return '';
    }
}

?>