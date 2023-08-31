<?php

namespace common\db;

use common\models\Customer;
use yii\db\ActiveQuery;

class StoreQuery extends ActiveQuery
{
    // conditions appended by default (can be skipped)
    public function init()
    {
        $this->andOnCondition(['type' => Customer::TYPE_STORE]);
        parent::init();
    }

    // ... add customized query methods here ...

    public function active($state = true)
    {
        return $this->andOnCondition(['active' => $state]);
    }
}