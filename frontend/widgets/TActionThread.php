<?php
// your_app/votewidget/VoteWidget.php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use kartik\helpers\Enum;
use Yii;

class TActionThread extends Widget
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
        TActionThreadAsset::register($this->getView());
        return $this->render('_tactionthread', 
        ['model' => $this->model, 'options' => $this->options, 'id' => Yii::$app->security->generateRandomString()]);
    }
}
?>