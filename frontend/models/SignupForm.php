<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Region;
use common\models\Company;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $full_name;
    public $phone;
    public $role;
    public $region_id;
    public $company_id;
    public $avatar;
    private $iid;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['avatar', 'file'],
            
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['full_name', 'trim'],
            ['full_name', 'required'],
            ['full_name', 'string', 'min' => 2, 'max' => 255],
            
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            
            ['phone', 'trim'],
            ['phone', 'string', 'max' => 255],
            
            ['role', 'trim'],
            ['role', 'required'],
            ['role', 'string'],
            ['role', 'default', 'value' => User::ROLE_ENGINEER],
            ['role', 'in', 'range' => [User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR, User::ROLE_STORE_MANAGER, User::ROLE_GENERAL_MANAGER, User::ROLE_ENGINEER]],

            ['region_id', 'required', 'when' => function($model) {
                    return in_array($model->role, [User::ROLE_STORE_MANAGER, User::ROLE_GENERAL_MANAGER]);
                },
                'whenClient' => "function (attribute, value) {
                    return $('#signupform-role').value == 'Store Manager' || $('#signupform-role').value == 'General Manager';
                }",
            ],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'id']],
            
            ['company_id', 'required', 'when' => function($model) {
                return in_array($model->role, [User::ROLE_STORE_MANAGER, User::ROLE_GENERAL_MANAGER]);
            }, 'whenClient' => "function (attribute, value) {
                return $('#signupform-role').value == 'Store Manager' || $('#signupform-role').value == 'General Manager';
            }",],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    public function getId()
    {
        return $this->iid;
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        $validate = $this->validate();
        if (!$validate) {
            return $validate;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->full_name = $this->full_name;
        $user->phone = $this->phone;
        $user->setPassword("hardcode123");
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->company_id = $this->company_id;
        $user->region_id = $this->region_id;
        //$user->generateEmailVerificationToken();
        if ($this->role == User::ROLE_STORE_MANAGER || $this->role == User::ROLE_GENERAL_MANAGER)
        {
            $user->region_id = $this->region_id;
            $user->company_id = $this->company_id;
        }

        $filename = 'default_profile.jpg';
        if ($this->avatar) 
        {
            $filename = Yii::$app->security->generateRandomString() . '.' . $this->avatar->extension;
            $filepath = 'uploads/profiles/' . $filename;
            $this->avatar->saveAs($filepath);
            Image::thumbnail($filepath, 100, 100)->save('uploads/profiles/thumb/'.$filename, ['quality' => 80]);
        }
        $user->profile = $filename;

        $result = $user->save();
        if ($result)
        {
            $user->refresh();
            $user->role = $this->role;
            $this->iid == $user->id;
        }
        return $result;
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
