<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\search\Store $model */
/** @var yii\widgets\ActiveForm $form */

$tickets = $model->tickets;

?>
<?= Html::tag('span', $model->code, ['class' => 'store store-code']) ?>
<?= Html::tag('span', $model->name, ['class' => 'store store-name']) ?>
<?= Html::beginTag('span', 
    [ 
        'class'=>[
            'store',
            'store-ticket-count',
            'store-ticket-count-label',
            'hover-hook',
            'small', 'badge',
            'rounded-pill',
            'bg-secondary',
            'text-light',
        ]
    ]) ?>
<?= Html::a(count($tickets), ['ticket/index', 'store_id' => $model->id], ['class'=>'text-decoration-none text-light', 'title'=>"Jumlah ticket", 'data-method'=>"POST"]) ?>
<?= Html::endTag('span') ?>
<?php

?>
<?= Html::beginTag('span', 
    [ 
        'class'=>[
            'store',
            'store-ticket-count',
            'store-ticket-count-expansion',
            'show-on-hover',
            'small', 'badge',
            'rounded-pill',
            'bg-success',
            'text-light',
        ]
    ]) ?>
<?= Html::a(count($tickets), ['ticket/index', 'store_id' => $model->id, 'status' => 1], ['class'=>'text-decoration-none text-light', 'title'=>"Jumlah ticket yang sudah selesai", 'data-method'=>"POST"]) ?>
<div class="vr"></div>
<a class="text-decoration-none text-warning" title="Jumlah ticket sudah selesai tidak tercover MC" href="#" data-method="POST">1</a>
<div class="vr"></div>
<a class="text-decoration-none text-danger" title="Jumlah ticket selesai dan SLA tidak tercapai" href="#" data-method="POST">1</a>
<?= Html::endTag('span') ?>
<?php
?>
<?= Html::beginTag('span', 
    [ 
        'class'=>[
            'store',
            'store-ticket-count',
            'store-ticket-count-expansion',
            'show-on-hover',
            'small', 'badge',
            'rounded-pill',
            'bg-info',
            'text-light',
        ]
    ]) ?>
<a class="text-decoration-none text-light" title="Jumlah ticket selesai dan menunggu remote IT" href="#" data-method="POST">1</a>
<div class="vr"></div>
<a class="text-decoration-none text-warning" title="Jumlah ticket selesai dan menunggu remote IT tidak tercover MC" href="#" data-method="POST">1</a>
<div class="vr"></div>
<a class="text-decoration-none text-danger" title="Jumlah ticket selesai dan menunggu remote IT dan SLA tidak tercapai" href="#" data-method="POST">1</a>
<?= Html::endTag('span') ?>
<?= Html::beginTag('span', 
    [ 
        'class'=>[
            'store',
            'store-ticket-count',
            'store-ticket-count-expansion',
            'show-on-hover',
            'small', 'badge',
            'rounded-pill',
            'bg-danger',
            'text-light',
        ]
    ]) ?>
<a class="text-decoration-none text-light" title="Jumlah ticket double AHO" href="#" data-method="POST">2</a>
<?= Html::endTag('span') ?>
