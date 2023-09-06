<?php

namespace common\models\ticket;

use common\db\ActionQuery;
use common\models\actors\User;
use common\models\ticket\Action;
use yii\helpers\ArrayHelper;

class Closing extends Action
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['summary'], 'required'],
            [['user_id'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']]
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
        $query = $query->with('operator');
        return $query;
    }

    public function getOperator()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}