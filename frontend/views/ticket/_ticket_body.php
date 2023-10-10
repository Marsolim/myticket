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
/** @var common\models\tickets\Ticket $model */
/** @var yii\widgets\ActiveForm $form */

$ticketid = md5($model->number);

?>
<?= Html::beginTag('div', ['class' => ['card-body', $expanded ? 'show' : 'collapse'], 'id' => "ts-$ticketid-body",]) ?>
<div class="text-justify">
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <?= $this->render('_ticket_body_recommendations', ['model' => $model]) ?>
        </li>
        <li class="list-group-item">
            <?= $this->render('_ticket_body_discretion', ['model' => $model]) ?>
        </li>
        <li class="list-group-item">
            <?= $this->render('_ticket_body_documents', ['model' => $model]) ?>
        </li>
        <li class="list-group-item">
            <?= $this->render('_ticket_body_engineers', ['model' => $model]) ?>
        </li>
        <li class="list-group-item">
            <?= $this->render('_ticket_body_repairs', ['model' => $model]) ?>
        </li>
    </ul>
</div>
<?= Html::endTag('div') ?>