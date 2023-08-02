<?php

use common\models\User;
use common\models\ManagedStore;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ButtonDropdown;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shop-view">

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
