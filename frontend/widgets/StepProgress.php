<?php
// your_app/votewidget/VoteWidget.php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;

class StepProgress extends Widget
{
    public $model;

    public $options;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        // Register AssetBundle
        StepProgressAsset::register($this->getView());
        return $this->render('_stepprogress', 
        ['model' => $this->model, 'options' => $this->options, 'id' => Yii::$app->security->generateRandomString()]);
    }
}
?>