<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const ROLE_SYS_ADMINISTRATOR = 'System Administrator';
    const ROLE_ADMINISTRATOR = 'Administrator';
    const ROLE_STORE_MANAGER = 'Store Manager';
    const ROLE_ENGINEER = 'Engineer';
    const ROLE_GENERAL_MANAGER = 'General Manager';

    /**
     * @var UploadedFile avatar attribute
     */
    public $avatar;

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
            ['avatar', 'file'],
            ['phone', 'string'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
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
            'avatar' => 'Profile',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function getWaphone()
    {
        return preg_replace('~^0~', '62', preg_replace('~(\d{3,4})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4,7})~', '$1$2$3', $this->phone));
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Returns user role name according to RBAC
     * @return string
     */
    public function getRole()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        if (!$roles) {
            return null;
        }

        reset($roles);
        /* @var $role \yii\rbac\Role */
        $role = current($roles);

        return $role->name;
    }

    public function getProfileThumbnail()
    {
        $filename = $this->profile;
        $filename_ext = pathinfo($this->profile, PATHINFO_EXTENSION);
        $filename = preg_replace('/^(.*)\.' . $filename_ext . '$/', '$1.thumb.' . $filename_ext, $filename);
        return $filename;
    }

    public static function getUserRoleName($user = null)
    {
        if (!isset($user)) $user = Yii::$app->user->id;
        $roles = Yii::$app->authManager->getRolesByUser($user);
        if (!$roles) {
            return null;
        }

        reset($roles);
        /* @var $role \yii\rbac\Role */
        $role = current($roles);

        return $role->name;
    }

    /**
     * @var string $role
     * @var int $userid
     */
    public static function isMemberOfRole($role, $userid = null)
    {
        $userid = isset($userid) ? $userid : Yii::$app->user->id;
        $roles = Yii::$app->authManager->getRolesByUser($userid);
        $rolenames = ArrayHelper::getColumn($roles, 'name');
        if (is_array($role))
        {
            $result = false;
            foreach($role as $r)
            {
                $result |= in_array($r, $rolenames);
            }
            return $result;
        }
        return in_array($role, $rolenames);
    }

    public static function getUserRole($user = null)
    {
        if (!isset($user)) $user = Yii::$app->user->id;
        $roles = Yii::$app->authManager->getRolesByUser($user);
        if (!$roles) {
            return null;
        }

        reset($roles);
        /* @var $role \yii\rbac\Role */
        $role = current($roles);

        return $role;
    }

    public static function findByRole($name)
    {
        $userids = Yii::$app->authManager->getUserIdsByRole($name);
        if (!$userids)
        {
            return null;
        }
        return static::findAll($userids);
    }
}
