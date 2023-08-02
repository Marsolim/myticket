<?php

namespace common\models;

use Yii;
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
class TicketSummary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'store_id', 'engineer_id', 'issuer_id', 'since', 'till', 'age', 't_action_count', 'opened', 'suspended', 'progress', 'resolved_no_issue', 'resolved_wait_it', 'closed_no_action', 'closed_duplicate', 'closed_resolved'], 'integer'],
            [['engineer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['engineer_id' => 'id']],
            [['issuer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['issuer_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['store_id' => 'id']],
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
            'engineer_id' => 'Teknisi',
            'issuer_id' => 'Pembuat',
            'since' => 'Dibuat Tgl.',
            'till' => 'Hingga',
            'age' => 'Durasi',
            't_action_count' => 'Jumlah Kunjungan',
            'opened' => 'O',
            'suspended' => 'S',
            'progress' => 'PR',
            'resolved_no_issue' => 'RNI',
            'resolved_wait_it' => 'RIT',
            'closed_no_action' => 'CNA',
            'closed_duplicate' => 'CDT',
            'closed_resolved' => 'CRA',
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

    public static function renderLegends()
    {
        return $xml = <<<EOD
O  : Belum dikunjungi (baru Input).
S  : Dihentikan.
PR : Sudah kunjungan.
RNI: Selesai (oleh teknisi).
RIT: Selesai dan menunggu remote IT (oleh teknisi).
CNA: Ditutup karena tidak ada tindakan.
CDT: Ditutup karena double input.
CRA: Konfirmasi selesai (oleh Manajer Cabang).
EOD;
    }
}
