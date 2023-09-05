<?php

namespace frontend\models;

use common\db\UserQuery;
use common\models\User;
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

    public function getRole()
    {
        return User::ROLE_ENGINEER;
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
        $query = new UserQuery(get_called_class(), ['type' => self::class, 'tableName' => self::tableName()]);
        return $query;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert)
        {
            Yii::$app->authManager->assign(User::ROLE_ENGINEER, $this->getPrimaryKey());
        }
    }
}