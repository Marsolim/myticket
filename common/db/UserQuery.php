<?php

namespace common\db;

use common\models\User;
use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    // conditions appended by default (can be skipped)
    public function init()
    {
        parent::init();
    }

    // ... add customized query methods here ...

    public function active($state = User::STATUS_ACTIVE)
    {
        return $this->andOnCondition(['status' => $state]);
    }

    public function engineers($id = null)
    {
        $q = $this->active()
            ->innerJoin('auth_assignment a', 'user.id = a.user_id')
            ->where('a.item_name' => User::ROLE_ENGINEER);
        if (isset($id))
            $q = $q->where(['user.id' => $id]);
        return $q;
    }
    
    public function managers($id = null)
    {
        $q = $this->active()->with('associate', 'company')
            ->innerJoin('auth_assignment a', 'user.id = a.user_id')
            ->where('a.item_name' => [User::ROLE_GENERAL_MANAGER, User::ROLE_STORE_MANAGER]);
        if (isset($id))
            $q = $q->where(['user.id' => $id]);
        return $q;
    }
    
    public function submanagers($id = null)
    {
        $q = $this->active()->with('associate', 'company')
            ->innerJoin('auth_assignment a', 'user.id = a.user_id')
            ->where('a.item_name' => [User::ROLE_STORE_MANAGER]);
        if (isset($id))
            $q = $q->where(['user.id' => $id]);
        return $q;
    }

    public function generalmanagers($id = null)
    {
        $q = $this->active()->with('associate', 'company')
            ->innerJoin('auth_assignment a', 'user.id = a.user_id')
            ->where('a.item_name' => [User::ROLE_GENERAL_MANAGER]);
        if (isset($id))
            $q = $q->where(['user.id' => $id]);
        return $q;
    }
    
    public function administrators($id = null)
    {
        $q = $this->active()->with('associate', 'company')
            ->innerJoin('auth_assignment a', 'user.id = a.user_id')
            ->where('a.item_name' => [User::ROLE_ADMINISTRATOR, User::ROLE_SYS_ADMINISTRATOR]);
        if (isset($id))
            $q = $q->where(['user.id' => $id]);
        return $q;
    }
}