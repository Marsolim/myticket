<?php

namespace common\models;

use common\db\AuditedRecord;
use common\models\actors\Store;
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
class Holiday extends AuditedRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%holiday%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'start'], 'required'],
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

    public function beforeSave($insert)
    {
        $this->start = $this->start - ($this->start % 84600);
        $this->end = $this->start + 84599;
        return parent::beforeSave($insert);
    }

    public function isWithin(int $date){
        return $this->start <= $date && $date >= $this->end;
    }
}
