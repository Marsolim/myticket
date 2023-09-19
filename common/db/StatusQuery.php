<?php

namespace common\db;

use common\models\ticket\Assignment;
use common\models\actors\User;
use yii\db\ActiveQuery;

class StatusQuery extends ActiveQuery
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

}