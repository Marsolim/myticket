<?php

namespace common\models;

use common\models\Customer;
use common\models\Company;
use common\models\Store;
use common\models\Ticket;
use common\db\DepotQuery;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 * @property Store[] $stores
 */
class Depot extends Customer
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
            [['code'], 'unique'],
            [['name'], 'unique'],
            [['phone'], 'string', 'max' => 255],
            [['type'], 'default', Customer::TYPE_POINT],
            [['parent_id'], 'required'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['parent_id' => 'id']],
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
            'name' => 'Nama DC',
            'phone' => 'Telepon',
            'address' => 'Alamat',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
    
        $this->type = Customer::TYPE_DEPOT;

        return true;
    }

    public static function find()
    {
        return new DepotQuery(get_called_class());
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Store::class, ['parent_id' => 'id']);
    }
    
    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['customer_id' => 'id'])
            ->via('stores');
    }
}
