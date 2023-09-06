<?php

namespace common\models\ticket;

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
abstract class Action extends \yii\db\ActiveRecord
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
        return '{{%action%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'type'], 'required'],
            [['ticket_id', 'user_id', 'item_id'], 'integer'],
            [['action', 'type'], 'string', 'max' => 255],
            [['summary'], 'string', 'max' => 500],
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

    public static function instantiate($row)
    {
        $type = $row['type'];
        return new $type();
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
