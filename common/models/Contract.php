<?php

namespace common\models;

use common\db\AuditedRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\db\CustomerQuery;
use common\models\actors\Customer;
use common\models\actors\Store;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 */
class Contract extends AuditedRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_NONACTIVE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%contract%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'sla'], 'required'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['sla'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NONACTIVE]],
            [['expiry'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer',
            'sla' => 'SLA',
            'status' => 'Status',
            'expiry' => 'Expired',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Store::class, ['id' => 'customer_id'])->inverseOf('contract');
    }

    public function toString()
    {
        return implode('-', [$this->code, $this->name]);
    }
}
