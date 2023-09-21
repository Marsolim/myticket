<?php

namespace common\models\tickets;

use common\db\AuditedRecord;
use Yii;
use common\models\actors\User;
use common\models\actors\Store;
use common\models\actors\Depot;
use common\models\actors\Company;
use common\models\actors\Engineer;
use common\models\docs\Document;
use common\models\docs\Invoice;
use common\models\tickets\actions\Action;
use common\models\tickets\actions\Assignment;
use common\models\tickets\actions\closings\Closing;
use common\models\tickets\actions\ConcreteAction;
use common\models\tickets\actions\Discretion;
use common\models\tickets\actions\Recommendation;
use common\models\tickets\actions\MetaAction;
use common\models\tickets\actions\Repair;
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
class Ticket extends AuditedRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'number', 'status'], 'required'],
            [['customer_id', 'status', 'status_closed', 'status_contract'], 'integer'],
            [['number'. 'external_number'], 'string', 'max' => 20],
            [['problem'], 'string', 'max' => 255],
            [['number'], 'unique'],
            [['number'], 'autonumber', 'format'=>'TS.{Y.m}.????'],
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
            'customer_id' => 'Toko',
            'number' => 'No. Servis',
            'external_number' => 'No. AHO',
            'problem' => 'Kendala',
            'created_by' => 'Dibuat Oleh',
            'created_at' => 'Dibuat Tgl.',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        
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
    
    public function getManager()
    {
        return $this->hasOne(StoreManager::class, ['association_id' => 'id'])
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
        return $this->hasMany(Invoice::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[BAPs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInquiries()
    {
        return $this->hasMany(Invoice::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[SPKs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkOrders()
    {
        return $this->hasMany(Invoice::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    public function getReplacements()
    {
        return $this->hasMany(Repair::class, ['ticket_id' => 'id'])->onCondition(['not', ['serial' => null]])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Actions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hasMany(ConcreteAction::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Actions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetaActions()
    {
        return $this->hasMany(MetaAction::class, ['ticket_id' => 'id'])->inverseOf('ticket');
    }

    /**
     * Gets query for [[LastAction]].
     *
     * @return \common\models\Action
     */
    public function getLastAction()
    {
        return $this->hasOne(ConcreteAction::class, ['ticket_id' => 'id'])->addOrderBy(['created_at' => SORT_DESC])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecommendations()
    {
        return $this->hasMany(Recommendation::class, ['ticket_id' => 'id'])->inverseOf('ticket');
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
    public function getLastRepair()
    {
        return $this->hasOne(Repair::class, ['ticket_id' => 'id'])->addOrderBy(['created_at' => SORT_DESC])->inverseOf('ticket');
    }

    /**
     * Gets query for [[Discretion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClosing()
    {
        return $this->hasOne(Closing::class, ['ticket_id' => 'id'])->inverseOf('ticket');
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

    public static function find()
    {
        $query = parent::find();
        $query->with('actions', 'metaActions', 'recommendations', 'replacements', 'closing', 'repairs', 'assignments', 'engineers', 'lastAction');
        return $query;
    }
}
