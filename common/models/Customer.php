<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\db\CustomerQuery;
use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 */
abstract class Customer extends \yii\db\ActiveRecord
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

    // public function init()
    // {
    //     $this->type = self::class;
    //     parent::init();
    // }

    // public function beforeSave($insert)
    // {
    //     $this->type = self::class;
    //     parent::beforeSave($insert);
    // }

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
