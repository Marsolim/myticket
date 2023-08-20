<?php

namespace common\models;

use common\models\Store;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 * @property Store[] $stores
 */
class Holiday extends \yii\db\ActiveRecord
{
    const FILE_INVOICE = 1;
    const FILE_BAP_SPK = 2;
    const FILE_UNCATEGORIZED = 3;

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
        return 'holiday';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'start', 'end'], 'required'],
            [['start', 'end'], 'integer'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Name',
            'start' => 'Dari',
            'end' => 'Hingga',
        ];
    }
}
