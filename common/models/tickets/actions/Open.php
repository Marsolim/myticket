<?php

namespace common\models\tickets\actions;

use common\db\ActionQuery;
use common\db\ObjectQuery;
use common\models\tickets\actions\Action;
use common\models\actors\User;
use yii\helpers\ArrayHelper;

class Open extends ConcreteAction
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
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
        $query = new ObjectQuery(get_called_class(), ['type' => self::class, 'tableName' => self::tableName()]);
        $query = $query->with('evaluator');
        return $query;
    }

    public function getEvaluator()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}