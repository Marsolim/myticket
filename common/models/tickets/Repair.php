<?php

namespace common\models\tickets;

use common\db\ActionQuery;
use common\models\actors\Engineer;
use common\models\tickets\Action;
use common\models\Item;
use yii\helpers\ArrayHelper;

class Repair extends Action
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
        $query = new ActionQuery(get_called_class(), ['type' => self::class, 'tableName' => self::tableName()]);
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