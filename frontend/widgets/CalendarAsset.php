<?php
// your_app/votewidget/VoteWidgetAsset.php

namespace frontend\widgets;

use yii\web\AssetBundle;

class CalendarAsset extends AssetBundle
{
    public $js = [
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js',
        //'js/calendar.js'
    ];

    public $css = [
        // CDN lib
        //'//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css',
        //'css/calendar.css',
        'css/_calendar.css'
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