<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\helpers\RoleHelper;
use frontend\helpers\TStatusHelper;
use common\models\User;
use common\models\Document;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

//$steps = $model->getModels();
//ArrayHelper::multisort($steps, ['action_date'], [SORT_DESC]);
//$last = end($steps);
?>
<div id='wrap'>
    <div id='calendar'></div>
    <div style='clear:both'></div>
</div>