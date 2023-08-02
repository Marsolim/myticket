<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\Ticket;
use common\models\TicketAction;
use common\models\User;
use common\models\TicketStatus;
use common\models\Document;
use kartik\file\FileInput;

/** @var yii\web\View $this */
/** @var frontend\models\Ticket $model */

$this->title = 'Servis No. ' . $model->number . ' - ' . $model->problem;
$this->params['breadcrumbs'][] = ['label' => 'Servis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->number;
\yii\web\YiiAsset::register($this);
?>
<div class="ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'number',
            [
                'attribute' => 'store_id',
                //'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    return nl2br($model->storeDetail);
                }
            ],
            [
                'attribute' => 'engineer_id',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->engineer->full_name) ? Html::a($model->engineer->full_name, ['user-profile/view/', 'id' => $model->engineer->id]) : ''; // your url here
                }
            ],
            [
                'attribute' => 'issuer_id',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->issuer->full_name) ? Html::a($model->issuer->full_name, ['user-profile/view/', 'id' => $model->issuer->id]) : ''; // your url here
                }
            ],
            [
                'attribute' => 'issued_at',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->issued_at) ? date('d F Y', $model->issued_at) : ''; // your url here
                }
            ],
            [
                'attribute' => 'problem',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->problem;
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
                'label' => 'Documents',
                'format' => 'raw',
                'value' => function ($model) {
                    return FileInput::widget([
                        'name' => 'files[]',
                        'options' => ['multiple' => true],
                        'pluginOptions' => [
                            'showPreview' => true,
                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                            'showCancel' => false,
                            'overwriteInitial'=>false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'browseIcon' => '<i class="fa-solid fa-file-arrow-up"></i> ',
                            'browseLabel' =>  '',
                            'uploadUrl' => Url::to(['site/upload-document']),
                            //'uploadUrl' => "index.php?r=site%2Fupload-document",
                            'encodeUrl' => false,
                            'uploadExtraData' => [
                                'ticket_id'=>$model->id,
                                'store_id'=>$model->store_id,
                                'type'=>Document::FILE_INVOICE,
                            ],
                            'maxFileCount' => 5
                        ]
                    ]);;
                }
            ],
        ],
    ]) ?>
    <h1><?= Html::encode('Tindakan / Kunjungan') ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'action',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {         
                    return isset($model->action) ? Html::a($model->action, ['ticket-action/view', 'id' => $model->id]) : ''; // your url here
                },
                'filter'=>ArrayHelper::map(User::find()->all(), 'id', 'full_name'),
            ],
            [
                'attribute' => 'engineer_id',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->engineer_id) ? Html::a($model->engineer->full_name, ['user/view', 'id' => $model->engineer_id]) : ''; // your url here
                },
                'filter'=>ArrayHelper::map(User::find()->all(), 'id', 'full_name'),
            ],
            [
                'attribute' => 'action_date',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model){
                    return $model->action_date ? date('d F Y', $model->action_date) : ''; // your url here
                } 
            ],
            [
                'attribute' => 'status_override',
                //'label' => 'You Label Name ',
                'format' => 'raw',
                'value' => function ($model) {
                    
                    return isset($model->status_override) ? Html::a($model->status->code.'-'.$model->status->name, ['status/view', 'id' => $model->status_override]) : ''; // your url here
                },
                'filter'=>ArrayHelper::map(TicketStatus::find()->all(), 'id', 'name'),
            ],
            /* [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TicketAction $model, $key, $index, $column) {
                    return Url::toRoute(['ticket-action/'.$action, 'id' => $model->id]);
                 }
            ], */
        ],
    ]); ?>
</div>
