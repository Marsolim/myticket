<?php

namespace common\models\tickets\actions;

use common\db\ActionQuery;
use common\models\tickets\actions\Action;
use common\models\actors\Engineer;
use yii\helpers\ArrayHelper;

class Assignment extends MetaAction
{
    public function rules()
    {
        $rules = parent::rules();
        return ArrayHelper::merge($rules, [
            [['user_id'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Engineer::class, 'targetAttribute' => ['user_id' => 'id']],
        ]);
    }
    
    public function init()
    {
        $this->type = self::class;
        parent::init();
    }

    public function beforeSave($insert)
    {
        $this->type = self::class;
        return parent::beforeSave($insert);
    }
    
    public static function find()
    {
        $query = parent::find();
        $query = $query->with('engineer');
        return $query;
    }

    /**
     * Gets query for [[Engineer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEngineer()
    {
        return $this->hasOne(Engineer::class, ['id' => 'user_id']);
    }
}