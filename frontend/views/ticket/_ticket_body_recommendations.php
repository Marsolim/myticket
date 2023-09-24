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
            'class' => "btn py-1 btn-link text-decoration-none quick-action quick-action-form",
            'title' => "Rekomendasi pekerjaan"
        ]) ?>
    </div>
</div>
<?php
foreach($model->recommendations as $rec) {
?>
<figure class="card-text p-1 rounded" style="border-left: .25rem solid #a34e78;border-right: .25rem solid #a34e78;">
  <blockquote class="blockquote">
    <p><?= $rec->summary ?></p>
  </blockquote>
  <figcaption class="blockquote-footer text-end">
  <?= $rec->evaluator->full_name ?>
  <cite title="at"><?= date('Y F d', $rec->updated_at) ?></cite>
  </figcaption>
</figure>
<?php
}
?>