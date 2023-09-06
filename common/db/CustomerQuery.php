<?php

namespace common\db;

use common\models\actors\Customer;
use yii\db\ActiveQuery;

class CustomerQuery extends ActiveQuery
{
    public $type;
    public $tableName;

    public function prepare($builder)
    {
        if ($this->type !== null) {
            $this->andWhere(["$this->tableName.type" => $this->type]);
        }
        return parent::prepare($builder);
    }

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
}