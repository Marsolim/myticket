<?php

use common\models\Depot;
use common\models\User;
use frontend\db\StoreManagerQuery;
use Yii;

class StoreManager extends User
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'full_name', 'phone'], 'string', 'max' => 255],
            [['username', 'email', 'full_name', 'phone'], 'trim'],
            [['email'], 'email'],
            
            [['associate_id'], 'integer'],
            [['associate_id'], 'required'],
            [['associate_id'], 'default', 'value' => null],
            [['associate_id'], 'exist', 'skipOnError' => true, 'targetClass' => Depot::class, 'targetAttribute' => ['associate_id' => 'id']],
            
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'User Name',
            'email' => 'E-mail',
            'full_name' => 'Nama Lengkap',
            'phone' => 'No. Telepon',
            'role' => 'Role',
            'store' => 'Managed Store',
            'profile' => 'Profile',
        ];
    }

    public function getRole()
    {
        return User::ROLE_STORE_MANAGER;
    }

    public static function find()
    {
        return new StoreManagerQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert)
        {
            Yii::$app->authManager->assign(User::ROLE_STORE_MANAGER, $this->getPrimaryKey());
        }
    }
}