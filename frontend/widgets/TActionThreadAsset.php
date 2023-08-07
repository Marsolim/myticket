<?php
// your_app/votewidget/VoteWidgetAsset.php

namespace frontend\widgets;

use yii\web\AssetBundle;

class TActionThreadAsset extends AssetBundle
{
    public $css = [
         // CDN lib
        'css/step-progress.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}