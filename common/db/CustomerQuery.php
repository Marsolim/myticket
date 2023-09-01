<?php

namespace common\db;

use common\models\Customer;
use yii\db\ActiveQuery;

class CustomerQuery extends ActiveQuery
{
    // conditions appended by default (can be skipped)
    public function init()
    {
        parent::init();
    }

    // ... add customized query methods here ...

    public function active($state = true)
    {
        return $this->andOnCondition(['active' => $state]);
    }

    public function companies()
    {
        return $this->andOnCondition(['type' => Customer::TYPE_COMPANY]);
    }
    
    public function depots()
    {
        return $this->andOnCondition(['type' => Customer::TYPE_DEPOT]);
    }
    
    public function stores()
    {
        return $this->andOnCondition(['type' => Customer::TYPE_STORE]);
    }
}