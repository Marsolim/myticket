<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\Ticket $model */

$this->title = 'Create Ticket';
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'stores' => $stores,
        'engineers' => $engineers
    ]) ?>

</div>
