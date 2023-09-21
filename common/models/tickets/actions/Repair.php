<?php

namespace common\models\tickets\actions;

use common\db\ObjectQuery;
use common\models\actors\Engineer;
use common\models\Item;
use yii\helpers\ArrayHelper;

class Repair extends ConcreteAction
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['action'], 'required'],
            [['item_id'], 'required'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Engineer::class, 'targetAttribute' => ['user_id' => 'id']]
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
        $query = parent::find();
        $query = $query->with('operator');
        return $query;
    }

    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'item_id']);
    }
    
    public function getOperator()
    {
        return $this->hasOne(Engineer::class, ['id' => 'user_id']);
    }
}