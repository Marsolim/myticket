<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>
<?= Html::beginTag('span', ['class'=>"ticket", 'id' => 'ts-'.$model->number]) ?>
<?= Html::beginTag('a', [ 'href' => Url::to(['ticket/view', 'id' => $model->id]), 'class' => ['ticket-link']]) ?>
<?= empty($model->number) ? '' : Html::tag('span', $model->number, ['class' => 'ticket ticket-number', 'title' => 'Nomor tiket']) ?>
<?= empty($model->problem) ? '' : Html::tag('span', $model->problem, ['class' => 'ticket ticket-title', 'title' => 'Kendala']) ?>
<?= empty($model->external_number) ? '' : Html::tag('span', $model->external_number, ['class' => 'ticket ticket-aho', 'title' => 'Nomor AHO']) ?>
<?= Html::endTag('a') ?>
<?= Html::endTag('span') ?>