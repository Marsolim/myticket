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

$title = 'Alasan tidak tercover MC '.(empty($model->discretion) ? '<span class="small text-danger">(Tidak Ada)</span>' : '');

?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <div class="ms-auto">
        <?= Html::a('<i class="fa fa-handshake"></i>', ['ticket/discretion', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action",
            'title' => "Alasan tidak tercover MC"
        ]) ?>
    </div>
</div>
<?php
if (!empty($model->discretion)) {
?>
<p class="card-text">
    <?= $model->discretion->summary ?>
    <span class="small">
    <?= $model->discretion->assessor->full_name ?>
    </span>
</p>
<?php
}
?>