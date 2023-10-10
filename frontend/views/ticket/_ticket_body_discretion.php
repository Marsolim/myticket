<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\actions\Discretion;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\tickets\Ticket $model */
/** @var yii\widgets\ActiveForm $form */

$title = 'Alasan tidak tercover MC '.(empty($model->discretion) ? '<span class="small text-danger">(Tidak Ada)</span>' : '');
$cmdclass = ['ms-auto'];
if (!TicketHelper::can($model, Discretion::class)) $cmdclass[] = 'visually-hidden';
?>
<div class="d-flex">
    <div class="h6 my-1 align-self-stretch text-align-center"><?= $title ?></div>
    <?= Html::beginTag('div', ['class' => $cmdclass]) ?>
        <?= Html::a('<i class="fa fa-handshake"></i>', ['ticket/discretion', 'ticket' => $model->id], [
            'class' => "btn py-1 btn-link text-decoration-none quick-action quick-action-form",
            'title' => "Alasan tidak tercover MC"
        ]) ?>
    <?= Html::endTag('div') ?>
</div>
<?php if (!empty($model->discretion)) { ?>
<figure class="card-text p-2 rounded" style="border-left: .25rem solid #a34e78;border-right: .25rem solid #a34e78;">
    <blockquote class="blockquote">
        <?= $model->discretion->summary ?>
    </blockquote>
    <figcaption class="blockquote-footer text-end">
        <?= $model->discretion->assessor->full_name ?>
        <cite title="at"><?= date('Y F d', $model->discretion->updated_at) ?></cite>
    </figcaption>
</figure>
<?php } ?>