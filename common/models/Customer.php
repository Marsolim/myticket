<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\db\CustomerQuery;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 */
abstract class Customer extends \yii\db\ActiveRecord
{
    const TYPE_NULL = 0;
    const TYPE_STORE = 1;
    const TYPE_POINT = 2;
    const TYPE_COMPANY = 3;

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
            [['code'], 'unique'],
            [['name'], 'unique'],
            [['type'], 'default', 'value' => self::TYPE_NULL],
            [['type'], 'in', 'range' => [self::TYPE_STORE, self::TYPE_POINT, self::TYPE_COMPANY]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama',
            'code' => 'Kode',
            'address' => 'Alamat',
        ];
    }

    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }

    public static function isType(Customer $object = null, $type = self::TYPE_NULL)
    {
        if (!isset($object)) return false;
        return $object->type == $type;
    }

    public function isType($type = self::TYPE_NULL)
    {
        return self::isType($this, $type);
    }

    public function toString()
    {
        return implode('-', [$this->code, $this->name]);
    }
}
