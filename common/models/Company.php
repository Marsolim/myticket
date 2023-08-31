<?php

namespace common\models;

use common\models\Customer;
use common\models\Depot;
use common\models\Store;
use common\models\Ticket;
use common\db\CompanyQuery;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code 
 * @property string $address
 *
 */
class Company extends Customer
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 10],
            [['address'], 'string', 'max' => 500],
            [['phone'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['name'], 'unique'],
            [['type'], 'default', Customer::TYPE_COMPANY],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Kode',
            'name' => 'Nama Perusahaan',
            'phone' => 'Telepon',
            'email' => 'E-mail',
            'address' => 'Alamat',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
    
        $this->type = Customer::TYPE_COMPANY;

        return true;
    }
    
    public static function find()
    {
        return new CompanyQuery(get_called_class());
    }

    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['store_id' => 'id'])
            ->via('stores');
    }

    public function getStores()
    {
        return $this->hasMany(Store::class, ['depot_id' => 'id'])
            ->via('depots');
    }

    public function getDepots()
    {
        return $this->hasMany(Depot::class, ['company_id' => 'id']);
    }
}
