<?php

namespace common\models\actors;

use common\db\AuditedRecord;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 */
abstract class Customer extends AuditedRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'phone', 'email'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 10],
            [['address'], 'string', 'max' => 500],
            [['code', 'name', 'phone', 'email'], 'trim'],
            [['email'], 'email'],
            [['code'], 'unique'],
            [['name'], 'unique'],
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

    public static function instantiate($row)
    {
        $type = $row['type'];
        return new $type();
    }

    public function toString()
    {
        return implode('-', [$this->code, $this->name]);
    }
}
