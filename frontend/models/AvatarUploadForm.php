<?php

namespace frontend\models;

use yii\base\Model;
use yii\web\UploadedFile;
use yii\imagine\Image;
use common\models\User;
use Yii;

class AvatarUploadForm extends Model
{
    public $userid;
    
    /**
     * @var UploadedFile
     */
    public $avatar;

    public function rules()
    {
        return [
            [['userid'], 'integer'],
            ['userid', 'required'],
            [['avatar'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, png'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate())
        {
            $user = User::findOne(['id' => $this->userid]);
            $filename = Yii::$app->security->generateRandomString() . '.' . $this->avatar->extension;
            $filepath = 'uploads/profiles/' . $filename;
            $this->avatar->saveAs($filepath);
            Image::thumbnail($filepath, 100, 100)->save('uploads/profiles/thumb/'.$filename, ['quality' => 80]);
            $user->profile = $filename;
            return ($user->validate() && $user->save());
        } 
        return false;
    }
}