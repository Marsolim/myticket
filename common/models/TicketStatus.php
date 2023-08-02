<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ticket_status".
 *
 * @property int $id ID
 * @property string $name Nama
 * @property string $code Kode
 */
class TicketStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 5],
            [['name'], 'unique'],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
        ];
    }

    public function can($action)
    {
        return !str_starts_with($this->name, $action) && !str_starts_with($this->name, 'Close');
    }
}
