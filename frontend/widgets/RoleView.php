<?php
// your_app/votewidget/VoteWidget.php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class RoleView extends Widget
{
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
         // Register AssetBundle
        RoleViewAsset::register($this->getView());
        return $this->render('_userprofile', 
        ['model' => $this->model->model, 'options' => $this->model->options, 'id' => Yii::$app->security->generateRandomString()]);
    }
}
?>