<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\Ticket $model */

$this->title = 'Update Ticket: ' . $model->number . ' - ' . $model->problem;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'engineers' =>$engineers,
        'stores' => $stores
    ]) ?>

</div>
