<?php
// your_app/votewidget/VoteWidget.php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;

class Calendar extends Widget
{
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
         // Register AssetBundle
        CalendarAsset::register($this->getView());
        return $this->render('_calendar', 
        [
            //'model' => $this->model->model, 
            //'options' => $this->model->options, 
            'id' => 'ca'.Yii::$app->security->generateRandomString()
        ]);
    }
}
?>