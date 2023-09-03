<?php

namespace common\db;

use common\models\Customer;
use yii\db\ActiveQuery;

class DepotQuery extends ActiveQuery
{
    // conditions appended by default (can be skipped)
    public function init()
    {
        $this->andOnCondition(['type' => Customer::TYPE_DEPOT]);
        $this->with('company');
        parent::init();
    }

    // ... add customized query methods here ...

    public function active($state = true)
    {
        return $this->andOnCondition(['active' => $state]);
    }
}