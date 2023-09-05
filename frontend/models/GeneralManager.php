<?php

namespace frontend\models;

use common\db\UserQuery;
use common\models\Depot;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;

class GeneralManager extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['associate_id'], 'integer'],
            [['associate_id'], 'required'],
            [['associate_id'], 'default', 'value' => null],
            [['associate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['associate_id' => 'id']],
        ]);
    }

    public function getRole()
    {
        return User::ROLE_GENERAL_MANAGER;
    }

    public function getDepots()
    {
        return $this->hasMany(Depot::class, ['parent_id' => 'id'])
            ->via('company');
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'associate_id']);
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
        $query = $query->with('company', 'depots');
        return $query;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert)
        {
            Yii::$app->authManager->assign(User::ROLE_GENERAL_MANAGER, $this->getPrimaryKey());
        }
    }
}