<?php

namespace common\models\tickets\closings;

use common\db\ActionQuery;
use common\models\actors\User;
use common\models\tickets\Action;
use yii\helpers\ArrayHelper;

abstract class Closing extends Action
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['user_id'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']]
        ]);
    }
    
    public function getOperator()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}