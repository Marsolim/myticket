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
$title = ['Dokumen'];
if (empty($model->documents)) $title[] = '<span class="small text-danger">(Tidak Ada)</span>';
$title = implode(' ', $title);

?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <div class="ms-auto">
        <?= Html::a('<i class="fa fa-upload"></i>', ['ticket/upload-invoice', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action",
            'title' => "Invoice"
        ]) ?>
        <?= Html::a('<i class="fa fa-upload"></i>', ['ticket/upload-inquiry', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action",
            'title' => "BAP"
        ]) ?>
        <?= Html::a('<i class="fa fa-upload"></i>', ['ticket/upload-work-order', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action",
            'title' => "SPK"
        ]) ?>
    </div>
</div>
<?php
if (!empty($model->documents)) {
?>
<div>
<?php
foreach($model->documents as $doc) {
    $type = match ($doc::class) {
        Invoice::class => 'Invoice',
        Inquiry::class => 'BAP',
        WorkOrder::class => 'SPK',
    }
?>
<?= Html::a($type.' - '.$doc->uploadname, ['document/download', 'id' => $doc->id], ['class' => 'btn btn-link']) ?>
<?php
}
?>
</div>
<?php
}
?>