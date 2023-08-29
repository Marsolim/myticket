<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Html;
use common\models\User;
use common\models\TicketAction;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id
 * @property int $shop_id
 * @property string $number Nomor Tiket
 * @property string|null $problem_description Deskripsi Masalah
 * @property int|null $engineer_id
 * @property int $issuer_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $engineer
 * @property User $issuer
 * @property Store $store
 * @property TicketAction[] $actions
 */
class Ticket extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
    }

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
    public function rules()
    {
        return [
            [['store_id', 'number', 'issuer_id'], 'required'],
            [['store_id', 'engineer_id', 'issuer_id', 'last_action_id', 'last_status_id'], 'integer'],
            [['number'], 'string', 'max' => 20],
            [['problem'], 'string', 'max' => 255],
            [['problem_description'], 'string'],
            [['number'], 'unique'],
            [['number'], 'autonumber', 'format'=>'TS.Y.m.????'],
            ['issued_at', 'default', 'value' => time()],
            //['issued_at', 'date', 'timestampAttribute' => 'issued_at'],
            [['engineer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['engineer_id' => 'id']],
            [['issuer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['issuer_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['store_id' => 'id']],
            [['last_action_id'], 'exist', 'skipOnError' => true, 'targetClass' => TicketAction::class, 'targetAttribute' => ['last_action_id' => 'id']],
            [['last_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => TicketStatus::class, 'targetAttribute' => ['last_status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Toko',
            'number' => 'No. Servis',
            'problem' => 'Kendala',
            'problem_description' => 'Kronologis',
            'engineer_id' => 'Teknisi',
            'issuer_id' => 'Dibuat Oleh',
            'issued_at' => 'Dibuat Tgl.',
            'last_action_id' => 'T/K Terakhir',
            'last_status_id' => 'Status',
        ];
    }

    public function afterSave($insert, $changedAttributes){
        if ($insert){
            $this->refresh();
            $status = TicketStatus::findOne(['code' => 'O']);
            $newaction = new TicketAction();
            $newaction->ticket_id = $this->id;
            $newaction->engineer_id = $this->issuer_id;
            $newaction->action_date = time();
            $newaction->status_override = isset($status) ? $status->id : 1;
            $newaction->action = 'Open ticket baru.';
            $newaction->save();
            $newaction->refresh();
            $this->last_action_id = $newaction->id;
            $this->last_status_id = isset($status) ? $status->id : 1;
            $this->updateAttributes(['last_action_id', 'last_status_id']);
        }
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
     * Gets query for [[Issuer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIssuer()
    {
        return $this->hasOne(User::class, ['id' => 'issuer_id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }

    public function getStatusSummary()
    {
        $last_action = TicketAction::findOne(['id' => $this->last_action_id]);
        if (isset($last_action)){
            $scode = $last_action->status->code . ' - ' . $last_action->status->name;
            $engineer = $last_action->engineer->full_name;
            $date = date('d F Y', $last_action->action_date);
            $durasi = floor(((!in_array($last_action->status->code, ['CNA', 'CDT', 'CRA']) ? time() : $last_action->action_date) - $this->issued_at)/86400);
            return $xml = <<<EOD
Status  : $scode
Tanggal : $date
Tindakan: $last_action->action
Teknisi : $engineer
Lama penanganan : $durasi hari
EOD;
        }
        return '';
    }

    public function getStoreDetail()
    {
        $store = $this->store;
        if (isset($store)){
            $storename = Html::a($store->code.'-'.$store->name, ['store/view/', 'id' => $store->id]);
            return $xml = <<<EOD
$storename
$store->address
Telepon : $store->phone
e-Mail  : $store->email
EOD;
        }
        return '';
    }

    /**
     * Gets query for [[Actions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hasMany(TicketAction::class, ['ticket_id' => 'id']);
    }
}
