<?php

namespace common\models\ticket;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\actors\User;
use common\models\actors\Store;
use common\models\actors\Depot;
use common\models\actors\Company;
use common\models\doc\Document;
use common\models\ticket\Action;
use yii\helpers\ArrayHelper;

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
            'created_by' => 'Dibuat Oleh',
            'created_at' => 'Dibuat Tgl.',
        ];
    }

    public function init()
    {
        $this->status = self::STATUS_OPEN;
        parent::init();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord)
            $this->status = self::STATUS_OPEN;
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[Engineer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEngineers()
    {
        return $this->hasMany(Engineer::class, ['id' => 'user_id'])
            ->via('assignments');
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
        return $this->hasOne(Store::class, ['id' => 'store_id']);
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
        return $this->hasMany(StoreManager::class, ['association_id' => 'id'])
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
        return $this->hasMany(Document::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Invoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->getDocuments()->where(['category' => Document::FILE_INVOICE]);
    }

    /**
     * Gets query for [[BAPs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaps()
    {
        return $this->getDocuments()->where(['category' => Document::FILE_BAP]);
    }

    /**
     * Gets query for [[SPKs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSPKs()
    {
        return $this->getDocuments()->where(['category' => Document::FILE_SPK]);
    }

    public function getBarcodes()
    {
        $actions = $this->getRepairs()->andWhere(['not', ['serial' => null]])->all();
        $barcodes = ArrayHelper::getValue($actions, 'serial');
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
     * Gets query for [[LastAction]].
     *
     * @return \common\models\Action
     */
    public function getLastAction()
    {
        return $this->getActions()->orderBy(['created_at' => SORT_DESC])->one();
    }

    /**
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Repairs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRepairs()
    {
        return $this->hasMany(Repair::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Discretion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDiscretion()
    {
        return $this->hasOne(Discretion::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    public function getCovered()
    {
        return empty($this->reason);
    }

    /**
     * Gets query for [[Assignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignments()
    {
        return $this->hasMany(Assignment::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Items]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::class, ['id' => 'item_id'])
            ->via('repairs');
    }
}
