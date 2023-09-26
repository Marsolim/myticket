<?php

namespace common\models\actors;

use common\models\actors\Customer;
use common\models\actors\Depot;
use common\models\actors\Store;
use common\models\tickets\Ticket;
use common\db\CustomerQuery;
use common\db\ObjectQuery;
use Yii;
use yii\helpers\ArrayHelper;

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
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [

        ]);
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
        //$query = $query->with('depots', 'stores');
        return $query;
    }

    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['customer_id' => 'id'])
            ->via('stores');
    }

    public function getStores()
    {
        return $this->hasMany(Store::class, ['parent_id' => 'id'])->inverseOf('depot')
            ->via('depots');
    }

    public function getDepots()
    {
        return $this->hasMany(Depot::class, ['parent_id' => 'id'])->inverseOf('company');
    }
}
