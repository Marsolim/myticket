<?php

namespace common\models\actors;

use common\db\CustomerQuery;
use common\db\ObjectQuery;
use common\models\actors\Customer;
use common\models\actors\Company;
use common\models\actors\Store;
use common\models\tickets\Ticket;
use Yii;
use yii\helpers\ArrayHelper;

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
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['parent_id'], 'required'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['parent_id' => 'id']],
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
            'name' => 'Nama DC',
            'phone' => 'Telepon',
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
        $query = $query->with('company');
        return $query;
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'parent_id']);
    }
}
