<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Html;
use common\models\User;
use common\models\Store;
use common\models\Depot;
use common\models\Company;
use common\models\Action;

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
 * @property Action[] $actions
 */
class Ticket extends \yii\db\ActiveRecord
{
    const STATUS_OPEN = 1;
    const STATUS_PENDING = 2;
    const STATUS_CLOSED_NOPROBLEM = 3;
    const STATUS_CLOSED_NORMAL = 4;
    const STATUS_CLOSED_DOUBLE_AHO = 5;
    const STATUS_CLOSED_NORMAL_IT = 7;

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
            [['customer_id', 'number', 'status'], 'required'],
            [['customer_id'], 'integer'],
            [['number'. 'external_number'], 'string', 'max' => 20],
            [['problem'], 'string', 'max' => 255],
            [['number'], 'unique'],
            [['number'], 'autonumber', 'format'=>'TS.{Y.m}.????'],
            [['status'], 'default', 'value' => self::STATUS_OPEN],
            [['status'], 'in', 'range' => [
                self::STATUS_OPEN,
                self::STATUS_PENDING,
                self::STATUS_CLOSED_NOPROBLEM,
                self::STATUS_CLOSED_NORMAL,
                self::STATUS_CLOSED_DOUBLE_AHO,
                self::STATUS_CLOSED_NORMAL_IT
            ]],
            //['issued_at', 'default', 'value' => time()],
            //['issued_at', 'date', 'timestampAttribute' => 'issued_at'],
            //[['engineer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['engineer_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['customer_id' => 'id']],
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
            'external_number' => 'No. AHO',
            'problem' => 'Kendala',
            'recommendation' => 'Rekomendasi',
            'reason' => 'Alasan Tidak Tercover MC',
            'created_by' => 'Dibuat Oleh',
            'created_at' => 'Dibuat Tgl.',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert)
        {
            $this->refresh();
            $newaction = new Action();
            $newaction->ticket_id = $this->id;
            $newaction->engineer_id = $this->created_by;
            $newaction->action = 'Open ticket baru.';
            $newaction->save();
            $newaction->refresh();
        }
    }

    /**
     * Gets query for [[Engineer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEngineers()
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
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Depot]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return $this->hasOne(Depot::class, ['id' => 'parent_id'])
            ->via('store');
    }
    
    public function getManagers()
    {
        return $this->hasMany(User::class, ['association_id' => 'id'])
            ->via('depot')
            ->where(['status' => User::STATUS_ACTIVE]);
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'parent_id'])
            ->via('depot');
    }

    /**
     * Gets query for [[Documents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::class, ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[Invoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->getDocuments()->where(['category' => self::FILE_INVOICE]);
    }

    /**
     * Gets query for [[BAPs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaps()
    {
        return $this->getDocuments()->where(['category' => self::FILE_INVOICE]);
    }

    public function getStatusSummary()
    {
        $last_action = Action::findOne(['id' => $this->last_action_id]);
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

    public function getBarcodes()
    {
        $actions = $this->getActionsWithItem()->andWhere(['not', ['serial' => null]])->all();
        $barcodes = [];
        foreach($actions as $action)
        {
            $barcodes[] = $action->serial;
        }
        return implode(', ', $barcodes);
    }

    /**
     * Gets query for [[Actions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hasMany(Action::class, ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[ActionsWithItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionsWithItem()
    {
        return $this->hasMany(Action::class, ['ticket_id' => 'id'])->where(['not', ['action.item_id' => null]]);
    }

    /**
     * Gets query for [[Items]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::class, ['id' => 'item_id'])
            ->via('actionsWithItem');
    }
}
