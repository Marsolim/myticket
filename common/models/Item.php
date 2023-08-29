<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code 
 * @property string $address
 *
 */
class Item extends \yii\db\ActiveRecord
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
        return 'item';
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
            [['serial'], 'boolean'],
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
            'serial' => 'Ada S/N',
        ];
    }
}
