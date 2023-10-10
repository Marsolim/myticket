<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\docs\Inquiry;
use common\models\docs\Invoice;
use common\models\docs\WorkOrder;
use common\models\tickets\actions\Assignment;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\tickets\Ticket $model */
/** @var yii\widgets\ActiveForm $form */
$title = ['Teknisi'];
if (empty($model->engineers)) $title[] = '<span class="small text-danger">(Tidak Ada)</span>';
$title = implode(' ', $title);

$engineers = ArrayHelper::getColumn($model->engineers, function($e){ return Html::a($e->full_name, ['user/view', 'id' => $e->id]);}, false);

$cmdclass = ['ms-auto'];
if (!TicketHelper::can($model, Assignment::class)) $cmdclass[] = 'visually-hidden';
?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <?= Html::beginTag('div', ['class' => $cmdclass]) ?>
        <?= Html::a('<i class="fa fa-users-gear"></i>', ['ticket/assignment', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action quick-action-form",
            'title' => "Assigned Engineers"
        ]) ?>
    <?= Html::endTag('div') ?>
</div>
<?php
if (!empty($engineers)) {
?>
<div class="d-flex flex-row align-items-center">
<?= implode(', ', $engineers) ?>
</div>
<?php
}
?>