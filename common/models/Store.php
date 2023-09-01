<?php

namespace common\models;

use common\models\Customer;
use common\models\Company;
use common\models\Point;
use common\models\Ticket;
use common\db\StoreQuery;
use Yii;

/**
 * This is the model class for table "shop".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $address Alamat
 * @property int $region_id
 * @property int|null $status_id
 *
 * @property Region $region
 * @property SLAStatus $status
 */
class Store extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'point_id'], 'required'],
            [['parent_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 20],
            [['phone', 'email'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 500],
            [['code'], 'unique'],
            [['name'], 'unique'],
            [['type'], 'default', Customer::TYPE_STORE],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Depot::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama Toko',
            'code' => 'Kode',
            'phone' => 'Telepon',
            'email' => 'E-mail',
            'address' => 'Alamat',
            'depot_id' => 'Depot (DC)',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
    
        $this->type = Customer::TYPE_STORE;

        return true;
    }

    public static function find()
    {
        return new StoreQuery(get_called_class());
    }

    /**
     * Gets query for [[Depot]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return $this->hasOne(Region::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'parent_id'])
            ->via('depot');
    }

    /**
     * Gets query for [[Contract]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(CustomerContract::class, ['customer_id' => 'id'])
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['customer_id' => 'id']);
    }
}
