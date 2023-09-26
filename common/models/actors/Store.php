<?php

namespace common\models\actors;

use common\db\CustomerQuery;
use common\db\ObjectQuery;
use common\models\actors\Customer;
use common\models\actors\Company;
use common\models\actors\Depot;
use common\models\tickets\Ticket;
use Yii;
use yii\helpers\ArrayHelper;

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
class Store extends Customer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['parent_id'], 'required'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Depot::class, 'targetAttribute' => ['parent_id' => 'id']],
        ]);
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

    /**
     * Gets query for [[Depot]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return $this->hasOne(Depot::class, ['id' => 'parent_id']);
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
        return $this->hasOne(CustomerContract::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['customer_id' => 'id'])->inverseOf('store');
    }
}
