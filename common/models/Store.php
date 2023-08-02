<?php

namespace common\models;

use common\models\Region;
use common\models\SLAStatus;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
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
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'region_id'], 'required'],
            [['region_id', 'status_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 20],
            [['phone'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 500],
            [['code'], 'unique'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => SLAStatus::class, 'targetAttribute' => ['status_id' => 'id']],
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
            'email' => 'e-Mail',
            'address' => 'Alamat',
            'region_id' => 'DC',
            'status_id' => 'Status SLA',
        ];
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(SLAStatus::class, ['id' => 'status_id']);
    }

    public function getManager()
    {
        $mgs = ManagedStore::findOne(['store_id' => $this->id, 'active' => ManagedStore::STATUS_ACTIVE]);
        $user = isset($mgs) ? $mgs->user : null;
        return $user;
    }
}
