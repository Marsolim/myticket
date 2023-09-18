<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */


foreach($model as $k->$m)
{
?>
<?= Html::beginTag('span', ['class' => ['','',$m['color']], 'title' => $m['description']]) ?>
<?= Html::a($m['status'], ['ticket/index', ['status' => $m['id']]],
    [
        'class' => [
            $m['id'] > 7 ? 'text-decoration-strike-through' : 'text-decoration-none',
            'text-light'
        ]
    ]) ?>
<?= Html::endTag('span') ?>
<?php
}
?>