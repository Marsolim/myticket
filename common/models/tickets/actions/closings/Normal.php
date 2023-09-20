<?php

namespace common\models\tickets\actions\closings;

use common\db\ActionQuery;
use common\models\actors\User;
use common\models\tickets\actions\closings\Closing;
use yii\helpers\ArrayHelper;

class Normal extends Closing
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