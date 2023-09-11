<?php

namespace common\models\tickets;

use common\db\ActionQuery;
use common\models\actors\User;
use common\models\tickets\Action;
use yii\helpers\ArrayHelper;

class Discretion extends Action
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
        $query = $query->with('assessor');
        return $query;
    }

    public function getAssessor()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}