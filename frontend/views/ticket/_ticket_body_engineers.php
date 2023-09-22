<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\docs\Inquiry;
use common\models\docs\Invoice;
use common\models\docs\WorkOrder;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */
$title = ['Teknisi'];
if (empty($model->engineers)) $title[] = '<span class="small text-danger">(Tidak Ada)</span>';
$title = implode(' ', $title);

?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <div class="ms-auto">
        <?= Html::a('<i class="fa fa-users-gear"></i>', ['ticket/assignment', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action",
            'title' => "Invoice"
        ]) ?>
    </div>
</div>
<?php
if (!empty($model->engineers)) {
?>
<div class="d-flex flex-row align-items-center">
<?php
foreach($model->engineers as $doc) {
?>
<?= Html::a($doc->full_name, ['user/view', 'id' => $doc->id]) ?>
<?php
}
?>
</div>
<?php
}
?>