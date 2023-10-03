<?php

namespace common\models;

use common\models\actors\Store;
use Yii;
use yii\helpers\Html;
use common\models\actors\User;
use common\models\tickets\actions\Action;

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
            'opened' => 'B',
            'progress' => 'P',
            'resolved_no_issue' => 'S',
            'resolved_wait_it' => 'R',
            'closed_no_action' => 'N',
            'closed_duplicate' => 'D',
            //'closed_resolved' => 'S',
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
S: Selesai
B: Belum dikunjungi
R: Selesai (Butuh Diremote IT)
P: Pending
N: No Problem
D: Double AHO
T: Total Masalah CCTV per Cabang
EOD;
    }
}
