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
            'class' => "btn py-1 btn-link text-decoration-none quick-action quick-action-form",
            'title' => "Alasan tidak tercover MC"
        ]) ?>
    </div>
</div>
<?php
if (!empty($model->discretion)) {
?>
<figure class="card-text p-2 rounded" style="border-left: .25rem solid #a34e78;border-right: .25rem solid #a34e78;">
  <blockquote class="blockquote">
    <p><?= $model->discretion->summary ?></p>
  </blockquote>
  <figcaption class="blockquote-footer text-end">
  <?= $model->discretion->assessor->full_name ?>
  <cite title="at"><?= date('Y F d', $model->discretion->updated_at) ?></cite>
  </figcaption>
</figure>
<?php
}
?>