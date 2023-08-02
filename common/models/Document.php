<?php

namespace common\models;

use common\models\Store;
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
class Document extends \yii\db\ActiveRecord
{
    const FILE_INVOICE = 1;
    const FILE_BAP_SPK = 2;
    const FILE_UNCATEGORIZED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'owner_id', 'filename'], 'required'],
            [['filename', 'uploadname'], 'string', 'max' => 255],
            [['type', 'related_ticket_id', 'related_store_id', 'related_taction_id', 'file_size'], 'int'],
            ['type', 'default', 'value' => self::FILE_UNCATEGORIZED],
            ['type', 'in', 'range' => [self::FILE_INVOICE, self::FILE_BAP_SPK, self::FILE_UNCATEGORIZED]],
            [['filename'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uploadname' => 'Name',
            'filename' => 'Code',
        ];
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Store::class, ['region' => 'id']);
    }
}
