<?php

use common\models\Store;
use common\models\Region;
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

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    
                    return !empty($model->name) ? Html::a($model->name, ['store/view/', 'id' => $model->id]) : ''; // your url here
                },
            ],
            'code',
            'address',
            [
                'attribute' => 'region_id',
                'format' => 'raw',
                'value' => function ($model) {
                    
                    return !empty($model->region_id) ? Html::a($model->region->name, ['region/view/', 'id' => $model->region_id]) : ''; // your url here
                },
                'filter'=>ArrayHelper::map(Region::find()->asArray()->all(), 'id', 'name'),
            ],
            //'status_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Store $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

</div>
