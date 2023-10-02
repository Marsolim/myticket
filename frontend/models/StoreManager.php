<?php

namespace frontend\models;

use common\db\ObjectQuery;
use common\db\UserQuery;
use common\models\actors\Depot;
use common\models\actors\Company;
use common\models\actors\User;
use Yii;
use yii\helpers\ArrayHelper;

class StoreManager extends User
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
            [['associate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Depot::class, 'targetAttribute' => ['associate_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'User Name',
            'email' => 'E-mail',
            'full_name' => 'Nama Lengkap',
            'phone' => 'No. Telepon',
            'role' => 'Role',
            'store' => 'Managed Store',
            'profile' => 'Profile',
        ];
    }

    public function getStores()
    {
        return $this->hasMany(Stores::class, ['parent_id' => 'id'])
            ->via('depot');
    }

    public function getDepot()
    {
        return $this->hasOne(Depot::class, ['id' => 'associate_id']);
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'parent_id'])
            ->via('depot');
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
        $query = $query->with('depot.company');
        return $query;
    }
}