<?php

namespace common\models\tickets;

use common\db\ActionQuery;
use common\models\actors\User;
use common\models\tickets\Status;
use yii\helpers\ArrayHelper;

class ContractStatus extends Status
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
        ]);
    }
    
    public function init()
    {
        $this->type = self::class;
        parent::init();
    }

    public function beforeSave($insert)
    {
        $this->type = self::class;
        return parent::beforeSave($insert);
    }
    
    public static function find()
    {
        $query = new ActionQuery(get_called_class(), ['type' => self::class, 'tableName' => self::tableName()]);
        return $query;
    }
}