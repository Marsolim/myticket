<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\actions\Recommendation;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\tickets\Ticket $model */
/** @var yii\widgets\ActiveForm $form */

$title = 'Rekomendasi '.(empty($model->recommendations) ? '<span class="small text-danger">(Tidak Ada)</span>' : '');

$cmdclass = ['ms-auto'];
if (!TicketHelper::can($model, Recommendation::class)) $cmdclass[] = 'visually-hidden';
?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <?= Html::beginTag('div', ['class' => $cmdclass]) ?>
        <?= Html::a('<i class="fa fa-handshake"></i>', ['ticket/visit', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action quick-action-form",
            'title' => "Rekomendasi pekerjaan"
        ]) ?>
    <?= Html::endTag('div') ?>
</div>
<?php
foreach($model->recommendations as $rec) {
?>
<figure class="card-text p-1 rounded" style="border-left: .25rem solid #a34e78;border-right: .25rem solid #a34e78;">
    <blockquote class="blockquote">
        <?= $rec->summary ?>
    </blockquote>
    <figcaption class="blockquote-footer text-end">
        <?= $rec->evaluator->full_name ?>
        <cite title="at"><?= date('Y F d', $rec->updated_at) ?></cite>
    </figcaption>
</figure>
<?php
}
?>