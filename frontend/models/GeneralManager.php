<?php

namespace frontend\models;

use common\db\ObjectQuery;
use common\models\actors\Depot;
use common\models\actors\Company;
use common\models\actors\User;
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

    public function getStores()
    {
        return $this->hasMany(Store::class, ['parent_id' => 'id'])
            ->via('depots');
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
        $query = new ObjectQuery(get_called_class());
        $query = $query->with('company', 'depots', 'stores');
        return $query;
    }
}