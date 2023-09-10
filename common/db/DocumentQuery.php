<?php

namespace common\db;

use common\models\actors\User;
use yii\db\ActiveQuery;

class DocumentQuery extends ActiveQuery
{
    public $type;
    public $tableName;

    public function prepare($builder)
    {
        if ($this->type !== null) {
            $this->andWhere(["$this->tableName.type" => $this->type]);
        }
        $this->with('action', 'ticket', 'store');
        return parent::prepare($builder);
    }
}