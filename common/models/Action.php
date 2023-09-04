<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ticket_action".
 *
 * @property int $id
 * @property int $ticket
 * @property int $engineer_id
 * @property string $action_date
 * @property int $status_override
 * @property string|null $summary
 */
class Action extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'engineer_id', 'status_override'], 'required'],
            [['ticket_id', 'engineer_id', 'status_override'], 'integer'],
            [['action'], 'string', 'max' => 255],
            ['action_date', 'default', 'value' => time()],
            //['action_date', 'date', 'timestampAttribute' => 'action_date'],
            [['summary'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => 'Pekerjaan',
            'ticket_id' => 'No. Servis',
            'engineer_id' => 'Teknisi',
            'item_id' => 'Barang',
            'serial' => 'S/N',
            'created_at' => 'Tgl. Pekerjaan',
            'created_by' => 'Penginput',
            'summary' => 'Laporan lengkap',
        ];
    }

    /**
     * Gets query for [[Engineer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEngineer()
    {
        return $this->hasOne(User::class, ['id' => 'engineer_id']);
    }
    
    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(TicketStatus::class, ['id' => 'status_override']);
    }
}
