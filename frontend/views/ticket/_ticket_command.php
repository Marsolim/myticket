<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\actions\closings\Awaiting;
use common\models\tickets\actions\closings\Duplicate;
use common\models\tickets\actions\closings\NoProblem;
use common\models\tickets\actions\closings\Normal;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>
<?= Html::beginTag('span', ['class'=>"d-flex ticket ticket-command", 'id' => "ts-$model->number-command"]) ?>
<?= TicketHelper::can($model, Awaiting::class) ? Html::a('<i class="fa fa-hourglass"></i>', ['ticket/close-awaiting', 'ticket' => $model->id], [
            'class' => "btn btn-link text-info text-decoration-none quick-action quick-action-link",
            'title' => "Awaiting"
        ]) : '' ?>
<?= TicketHelper::can($model, NoProblem::class) ? Html::a('<i class="fa fa-circle-question"></i>', ['ticket/close-no-problem', 'ticket' => $model->id], [
            'class' => "btn btn-link text-warning text-decoration-none quick-action quick-action-link",
            'title' => "Awaiting"
        ]) : '' ?>
<?= TicketHelper::can($model, Normal::class) ? Html::a('<i class="fa fa-circle-check"></i>', ['ticket/close-normal', 'ticket' => $model->id], [
            'class' => "btn btn-link text-success text-decoration-none quick-action quick-action-link",
            'title' => "Awaiting"
        ]) : '' ?>
<?= TicketHelper::can($model, Duplicate::class) ? Html::a('<i class="fa fa-bugs"></i>', ['ticket/close-duplicate', 'ticket' => $model->id], [
            'class' => "btn btn-link text-danger text-decoration-none quick-action quick-action-link",
            'title' => "Awaiting"
        ]) : '' ?>
<?= Html::a('<i class="fa fa-bell"></i>', ['ticket/notify', 'ticket' => $model->id], [
            'class' => "btn btn-link text-primary text-decoration-none quick-action quick-action-link",
            'title' => "Notify"
        ]) ?>
<?= Html::endTag('span') ?>