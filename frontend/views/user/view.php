<?php

use common\models\User;
use common\models\Store;
use common\models\ManagedStore;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ButtonDropdown;
use kartik\editable\Editable;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
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
<div class="user-view">

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

    <!-- <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?> -->
    <?= Html::beginTag('section', ['class'=>'vh-100', 'style'=>'background-color: #f4f5f7;']) ?>
    <?= Html::beginTag('div', ['class'=>'container py-5 h-100']) ?>
    <?= Html::beginTag('div', ['class'=>"row d-flex justify-content-center align-items-center h-100"]) ?>
    <?= Html::beginTag('div', ['class'=>"col col-lg-6 mb-4 mb-lg-0"]) ?>
    <?= Html::beginTag('div', ['class'=>"card mb-3", 'style'=>"border-radius: .5rem;"]) ?>
    <?= Html::beginTag('div', ['class'=>"row g-0"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-md-4 gradient-custom text-center text-safe",
        'style'=>"border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;"]) ?>
    <?= Html::img('uploads/profiles/thumb/'.$model->profile, ['alt'=>'profile', 'class'=>"img-fluid my-5", 'style'=>"width: 80px;"]) ?>
    <?= Editable::widget([
    'name'=>'full_name', 
    'asPopover' => false,
    'value' => $model->full_name,
    'header' => 'Name',
    'size'=>'md',
    'options' => ['class'=>'form-control', 'placeholder'=>'Enter user full name...']
]) ?>
    <?= Html::tag('p', $model->role)?>
                    <i class="far fa-edit mb-5"></i>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-md-8"]) ?>
    <?= Html::beginTag('div', ['class'=>"card-body p-4"]) ?>
                        <h6>Information</h6>
                        <hr class="mt-0 mb-4">
                        <div class="row pt-1">
                        <div class="col-6 mb-3">
                            <h6>Email</h6>
                            <p class="text-muted">info@example.com</p>
                        </div>
                        <div class="col-6 mb-3">
                            <h6>Phone</h6>
                            <p class="text-muted">123 456 789</p>
                        </div>
                        </div>
                        <h6>Projects</h6>
                        <hr class="mt-0 mb-4">
                        <div class="row pt-1">
                        <div class="col-6 mb-3">
                            <h6>Recent</h6>
                            <p class="text-muted">Lorem ipsum</p>
                        </div>
                        <div class="col-6 mb-3">
                            <h6>Most Viewed</h6>
                            <p class="text-muted">Dolor sit amet</p>
                        </div>
                        </div>
                        <div class="d-flex justify-content-start">
                        <a href="#!"><i class="fab fa-facebook-f fa-lg me-3"></i></a>
                        <a href="#!"><i class="fab fa-twitter fa-lg me-3"></i></a>
                        <a href="#!"><i class="fab fa-instagram fa-lg"></i></a>
                        </div>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('section') ?>
</div>
