<?php

use common\models\actors\Store;
use common\models\actors\Depot;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\StoreSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Stores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Store', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->name) ? Html::a($model->name, ['customer/view/', 'id' => $model->id]) : ''; // your url here
                },
            ],
            'code',
            'address',
            [
                'attribute' => 'parent_id',
                'label' => 'DC',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->depot) ? Html::a($model->depot->name, ['customer/view/', 'id' => $model->parent_id]) : ''; // your url here
                },
                'filter'=>ArrayHelper::map(Depot::find()->asArray()->all(), 'id', 'name'),
            ],
            [
                'label' => 'Company',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->company) ? Html::a($model->company->name, ['customer/view/', 'id' => $model->company->id]) : ''; // your url here
                },
                'filter'=>ArrayHelper::map(Depot::find()->asArray()->all(), 'id', 'name'),
            ],
            //'status_id',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Store $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>
</div>
