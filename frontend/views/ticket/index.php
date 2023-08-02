<?php

use common\models\Ticket;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\TicketSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Servis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    <?php
        if (Yii::$app->user->can("issueTicket"))
        {
            echo Html::a('Servis Baru', ['create'], ['class' => 'btn btn-success']).'   ';
        }
    ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'number',
                //'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->number) ? Html::a($model->number . ' - ' . $model->problem, ['view', 'id' => $model->id]) : ''; // your url here
                }
            ],
            [
                'attribute' => 'store_id',
                //'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->store_id) ? Html::a($model->store->code . ' - ' . $model->store->name, ['store/view/', 'id' => $model->store->id]) : ''; // your url here
                }
            ],
            [
                'attribute' => 'issued_at',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->issued_at ? date('d F Y', $model->issued_at) : ''; // your url here
                }
            ],
            [
                'attribute' => 'engineer_id',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->engineer_id) ? Html::a($model->engineer->full_name, ['user-profile/view/', 'id' => $model->engineer->id]) : ''; // your url here
                }
            ],
            [
                'attribute' => 'last_status_id',
                'label' => 'Status terakhir',
                'format' => 'raw',
                'value' => function ($model) {
                    return nl2br($model->statusSummary);
                }
            ],
            [
                //'class' => ActionColumn::className(),
                'label' => 'Command',
                'format' => 'raw',
                'value' => function ($model) {
                    return implode(' ', $model->commands());
                }
            ],
        ],
    ]); ?>


</div>
