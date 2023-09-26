<?php

use common\models\actors\User;
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
        ],
    ]) ?>
    </div>
</div>


    

</div>
