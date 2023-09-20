<?php

namespace common\models\tickets\actions;

use common\db\ActionQuery;
use common\models\tickets\Ticket;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ticket_action".
 *
 * @property int $id
 * @property int $ticket
 * @property int $user_id
 * @property string|null $summary
 */

abstract class ConcreteAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['user_id'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attributes = parent::attributeLabels();
        return ArrayHelper::merge($attributes, [
            'ticket_id' => 'No. Servis',
            'user_id' => 'Teknisi',
            'created_at' => 'Tgl. Kerja',
            'created_by' => 'Penginput',
            'summary' => 'Pekerjaan',
        ]);
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
