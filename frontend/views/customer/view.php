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
                'attribute' => 'parent_id',
                'label' => 'Distribution Center',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->depot) ? Html::a($model->depot->name, ['region/view', 'id' => $model->parent_id]) : '';
                }
            ],
            [
                'label' => 'SLA',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->contract) ? $model->contract->sla : '14';
                }
            ],
        ],
    ]) ?>

    

</div>
