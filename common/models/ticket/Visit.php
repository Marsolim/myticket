<?php

namespace common\models\ticket;

use common\db\ActionQuery;
use common\models\ticket\Action;
use common\models\Engineer;
use yii\helpers\ArrayHelper;

class Visit extends Action
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['summary'], 'required'],
            [['user_id'], 'required'],
            [['user_id'], ]
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
        //$query = $query->with('engineer');
        return $query;
    }
}