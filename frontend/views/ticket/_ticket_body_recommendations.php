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

$title = 'Rekomendasi '.(empty($model->recommendations) ? '<span class="small text-danger">(Tidak Ada)</span>' : '');

?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <div class="ms-auto">
        <?= Html::a('<i class="fa fa-handshake"></i>', ['ticket/visit', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action",
            'title' => "Rekomendasi pekerjaan"
        ]) ?>
    </div>
</div>
<?php
foreach($model->recommendations as $rec) {
?>
<p class="card-text">
    <?= $rec->summary ?>
    <span class="small">
    <?= $rec->evaluator->full_name ?>
    </span>
</p>
<?php
}
?>