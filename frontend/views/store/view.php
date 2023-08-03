<?php

use common\models\User;
use common\models\ManagedStore;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\bootstrap5\ButtonDropdown;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="store-view">

<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
</p>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information" type="button" role="tab" aria-controls="information" aria-selected="true">Informasi</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="ticket-history-tab" data-bs-toggle="tab" data-bs-target="#ticket-history" type="button" role="tab" aria-controls="ticket-history" aria-selected="false">History Servis</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="document-tab" data-bs-toggle="tab" data-bs-target="#document" type="button" role="tab" aria-controls="document" aria-selected="false">Dokumen</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="information" role="tabpanel" aria-labelledby="home-tab">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'code',
            [
                'attribute' => 'address',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->address) ? nl2br($model->address) : '';
                }
            ],
            'phone',
            'email',
            [
                'attribute' => 'region_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->region_id) ? Html::a($model->region->name, ['region/view', 'id' => $model->region_id]) : '';
                }
            ],
            [
                'attribute' => 'status_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->status_id) ? Html::a($model->status->name, ['sla-status/view', 'id' => $model->status_id]) : '';
                }
            ],
            [
                'attribute' => 'manager',
                'format' => 'raw',
                'value' => function ($model) {
                    if (Yii::$app->user->can('manageUser'))
                    {
                        $managers = User::findByRole(User::ROLE_STORE_MANAGER);
                        $items = [];
                        foreach($managers as $manager)
                        {
                            $items[] = [
                                'label' => $manager->username, 
                                'url' => ['store/assign/', 'store' => $model->id, 'mgr' => $manager['id']],
                                'linkOptions' => ['data' => [
                                    'confirm' => 'Assign '.$manager->username.' to '.$model->name.'?',
                                    'method' => 'post',
                                ]],
                            ];
                        }
                        $mgs = ManagedStore::findOne(['store_id' => $model->id, 'active' => ManagedStore::STATUS_ACTIVE]);
                        $cmanager = isset($mgs) ? $mgs->user : null;
                        return ButtonDropdown::widget([
                            'label' => isset($cmanager) ? $cmanager->username : 'Assign Manager',
                            'dropdown' => [
                                'items' => $items,
                            ],
                        ]);
                    }
                    else
                    {
                        return isset($model->manager) ? Html::a($model->manager->full_name, ['user/view', 'id' => $model->manager->id]) : '';
                    }
                }
            ]
        ],
    ]) ?>
    </div>
    <div class="tab-pane fade" id="ticket-history" role="tabpanel" aria-labelledby="ticket-history-tab">
    <?= GridView::widget([
        'dataProvider' => $ticketProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'number',
                //'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->number) ? Html::a($model->number . ' - ' . $model->problem, ['ticket/view', 'id' => $model->id]) : ''; // your url here
                }
            ],
            [
                'attribute' => 'store_id',
                //'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    return isset($model->store_id) ? Html::a($model->store->code . ' - ' . $model->store->name, ['view', 'id' => $model->store->id]) : ''; // your url here
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
    <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
    <?= ListView::widget([
        'dataProvider' => $documentProvider,
        'itemView' => '_document',
    ]) ?>
    </div>
</div>


    

</div>
