<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use frontend\widgets\TActionThread;
use frontend\widgets\TActionInput;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\ticket\Ticket;
use common\models\ticket\Action;
use common\models\actors\User;
use common\models\doc\Document;
use kartik\file\FileInput;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var frontend\models\Ticket $model */

$this->title = 'Servis No. ' . $model->number . ' - ' . $model->problem;
$this->params['breadcrumbs'][] = ['label' => 'Servis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->number;
\yii\web\YiiAsset::register($this);

$dataProvider = new ArrayDataProvider(['allModels' => $model->actions]);
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
                    return nl2br($model->store->name);
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
</div>
