<?php

use common\models\User;
use common\models\Store;
use common\models\ManagedStore;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ButtonDropdown;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Store', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$attributes = [
    'username',
    'email',
    'full_name',
    [
        'attribute' => 'phone',
        'format' => 'raw',
        'value' => function ($model) {
            return isset($model->phone) ? '<a href="https://wa.me/'.$model->waphone.'" title="Click to send whatsapp message to this number." class="text-decoration-none">'.$model->phone.' <i class="fa-brands fa-whatsapp"></i></a>' : '';
        }
    ],
    [
        'attribute' => 'profile',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::img('uploads/profiles/thumb/'.$model->profile, ['alt'=>'profile','width'=>'50','height'=>'50']);
        }
    ],
    [
        'label' => 'Role',
        'format' => 'raw',
        'value' => function ($model) {
            $dropdownlabel = Yii::$app->user->can('manageUser') ? (!$model->role ? 'Assign Role' : $model->role) : (!$model->role ? '' : $model->role);
            
            $ddroleitems = [
                [
                    'label' => User::ROLE_GENERAL_MANAGER,
                    'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_GENERAL_MANAGER],
                ],
                [
                    'label' => User::ROLE_STORE_MANAGER,
                    'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_STORE_MANAGER],
                ],
                [
                    'label' => User::ROLE_ENGINEER,
                    'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_ENGINEER],
                ]
            ];

            if (User::isMemberOfRole(User::ROLE_SYS_ADMINISTRATOR))
            {
                $ddroleitems[] = [
                    'label' => User::ROLE_ADMINISTRATOR,
                    'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_ADMINISTRATOR],
                ];
            }

            return Yii::$app->user->can('manageUser') ? ButtonDropdown::widget([
                'label' => $dropdownlabel,
                'dropdown' => [
                    'items' => $ddroleitems,
                ],
                'options' => ['class' => 'ajax-dropdown']
            ]) : $dropdownlabel;
        }
    ],
];

if (User::isMemberOfRole(User::ROLE_STORE_MANAGER, $model->id))
{
    $attributes[] = [
        'label' => 'Managed Store',
        'format' => 'raw',
        'value' => function ($model) {
            $cstore = ManagedStore::findOne(['user_id' => $model->id, 'active' => ManagedStore::STATUS_ACTIVE])->store;
                
            if (Yii::$app->user->can('manageUser'))
            {
                $stores = Store::find()->all();
                $items = [];
                foreach($stores as $store)
                {
                    $items[] = [
                        'label' => $store->name, 
                        'url' => ['assign-store', 'store' => $store->id, 'mgr' => $model->id],
                    ];
                }
                return ButtonDropdown::widget([
                    'label' => isset($cstore) ? $cstore->name : 'Manage Store',
                    'dropdown' => [
                        'items' => $items,
                    ],
                    'options' => ['class' => 'ajax-dropdown']
                ]);
            }
            else
            {
                return isset($cstore) ? Html::a($cstore->name, ['store/view', 'id' => $cstore->id]) : '';
            }
        }
    ];
}

?>
<div class="store-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
            if (Yii::$app->user->can('manageUser'))
            {
                echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
            }
        ?>
        <?php
            if (Yii::$app->user->id == $model->id || User::isMemberOfRole(User::ROLE_ADMINISTRATOR, Yii::$app->user->id) || User::isMemberOfRole(User::ROLE_SYS_ADMINISTRATOR, Yii::$app->user->id))
            {
                echo Html::a('Reset Password', ['site/request-password-reset'], ['class' => 'btn btn-primary']);
            }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>

</div>
