<?php

namespace frontend\models\forms;

use common\models\actors\Administrator;
use Yii;
use yii\base\Model;
use common\models\actors\User;
use common\models\actors\Depot;
use common\models\actors\Company;
use common\models\actors\Customer;
use common\models\actors\Engineer;
use common\models\actors\Store;
use frontend\models\GeneralManager;
use frontend\models\StoreManager;
use yii\imagine\Image;

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
    public $depot_id;
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
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['full_name', 'trim'],
            ['full_name', 'required'],
            ['full_name', 'string', 'min' => 2, 'max' => 255],
            
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],
            
            ['phone', 'trim'],
            ['phone', 'string', 'max' => 255],
            
            ['role', 'trim'],
            ['role', 'required'],
            ['role', 'string'],
            ['role', 'default', 'value' => 'usr'],
            ['role', 'in', 'range' => ['usr', 'adm', 'sys', 'smg', 'gmg', 'eng']],

            ['depot_id', 'required', 'when' => function($model) { return in_array($model->role, ['smg']); },],
            ['depot_id', 'exist', 'skipOnError' => true, 'targetClass' => Depot::class, 'targetAttribute' => ['depot_id' => 'id']],
            
            ['company_id', 'required', 'when' => function($model) { return in_array($model->role, ['gmg']); }, ],
            ['company_id', 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
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
        
        $user = match ($this->role) {
            'usr' => new User(),
            'eng' => new Engineer(),
            'smg' => new StoreManager(),
            'gmg' => new GeneralManager(),
            'adm' => new Administrator(),
        };

        $user->username = $this->username;
        $user->email = $this->email;
        $user->full_name = $this->full_name;
        $user->phone = $this->phone;
        $user->setPassword("hardcode123");
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->associate_id = match ($user::class) {
            StoreManager::class => $this->depot_id,
            GeneralManager::class => $this->company_id,
        };

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
            //$user->role = $this->role;
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
