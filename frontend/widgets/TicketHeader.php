<?php
// your_app/votewidget/VoteWidget.php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use kartik\helpers\Enum;
use Yii;

class TicketHeader extends Widget
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
        TicketHeaderAsset::register($this->getView());
        return $this->render('_ticketheader', 
        ['model' => $this->model, 'options' => $this->options, 'id' => 'ticket-header-'.Yii::$app->security->generateRandomString()]);
    }
}
?>