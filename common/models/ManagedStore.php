<?php

namespace common\models;

use common\models\User;
use common\models\Store;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "shop".
 *
 * @property int $id
 * @property int $user_id
 * @property int $store_id
 * @property int $effective_since Alamat
 * @property int $revoked_since
 *
 * @property User $user
 * @property Store $store
 */
class ManagedStore extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'managed_store';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'store_id'], 'required'],
            [['user_id', 'store_id', 'active'], 'integer'],
            ['active', 'default', 'value' => self::STATUS_INACTIVE],
            ['active', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Manager',
            'store_id' => 'Toko',
            'active' => 'Aktif',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }
}
