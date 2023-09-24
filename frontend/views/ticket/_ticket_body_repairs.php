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
$title = ['Pekerjaan'];
if (empty($model->repairs)) $title[] = '<span class="small text-danger">(Tidak Ada)</span>';
$title = implode(' ', $title);

?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <div class="ms-auto">
        <?= Html::a('<i class="fa fa-screwdriver-wrench"></i>', ['ticket/repair', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action quick-action-form",
            'title' => "Invoice"
        ]) ?>
    </div>
</div>
<?php
if (!empty($model->repairs)) {
?>
<div class="d-flex flex-row align-items-center">
<?php
foreach($model->repairs as $doc) {
?>
<div class="row small card-text">
    <div class="col-2"><?= date('Y F d H:m.s', $doc->created_at) ?></div>
    <div class="col-4"><?= $doc->action ?></div>
    <div class="col-4"><?= empty($doc->item) ? '' : $doc->item->name ?></div>
    <div class="col-2"><?= $doc->serial ?></div>
</div>
<?php
}
?>
</div>
<?php
}
?>