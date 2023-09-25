<?php

namespace common\models\tickets\actions;

use common\db\AuditedRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ticket_action".
 *
 * @property int $id
 * @property int $ticket
 * @property int $user_id
 * @property string $action_date
 * @property int $status_override
 * @property string|null $summary
 */

abstract class Action extends AuditedRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%action%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['ticket_id', 'user_id'], 'required'],
            [['ticket_id', 'user_id', 'item_id'], 'integer'],
            [['action', 'serial'], 'string', 'max' => 255],
            [['summary'], 'string', 'max' => 500],
        ]);
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
            'user_id' => 'Teknisi',
            'item_id' => 'Barang',
            'serial' => 'S/N',
            'created_at' => 'Tgl. Pekerjaan',
            'created_by' => 'Penginput',
            'summary' => 'Laporan lengkap',
        ];
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticket_id']);
    }
}
