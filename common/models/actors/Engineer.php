<?php

namespace common\models\actors;

use common\db\UserQuery;
use common\models\actors\User;
use yii\helpers\ArrayHelper;
use Yii;

class Engineer extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, []);
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
}