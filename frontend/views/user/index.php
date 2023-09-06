<?php

use common\models\actors\User;
use common\models\actors\Store;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UserHelper;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap5\ButtonDropdown;
//use kartik\grid\GridView;
use kartik\icons\FontAwesomeAsset;
//use Yii;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Profiles';
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
FontAwesomeAsset::register($this);

$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'profile',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::img('uploads/profiles/thumb/'.$model->profile, ['alt'=>'profile','width'=>'50','height'=>'50']);
        }
    ],
    [
        'attribute' => 'full_name',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->full_name, ['user/view/', 'id'=> $model->id]);
        }
    ],
    'email',
    
];

if (Yii::$app->user->can('manageUser'))
{
    $columns[] = [
        'label' => 'Role',
        'format' => 'raw',
        'value' => function ($model) {
            $dropdownlabel = Yii::$app->user->can('manageUser') ? (!$model->role ? 'Assign Role' : $model->role) : (!$model->role ? '' : $model->role);
            
            $ddroleitems = [
                [
                    'label' => User::ROLE_GENERAL_MANAGER,
                    'url' => ['assign-role', '_csrf' => Yii::$app->request->getCsrfToken(), 'user' => $model->id, 'role' => User::ROLE_GENERAL_MANAGER],
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

            if (UserHelper::isSystemAdmin(User::ROLE_SYS_ADMINISTRATOR))
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
                'options' => ['class' => 'ajax-dropdown'],
            ]) : $dropdownlabel;
        }
    ];
    $columns[] = [
        'label' => 'Managed Store',
        'format' => 'raw',
        'value' => function ($model) {
            if (UserHelper::isStoreManager($model->id))
            {
                if (Yii::$app->user->can('manageUser')){
                    $stores = Store::find()->all();
                    $items = [];
                    foreach($stores as $store)
                    {
                        $items[] = [
                            'label' => $store->name, 
                            'url' => ['assign-store', 'store' => $store->id, 'user' => $model->id],
                        ];
                    }
                    //$cstore = ManagedStore::findOne(['user_id' => $model->id, 'active' => ManagedStore::STATUS_ACTIVE]);
                    //if (isset($cstore)) $cstore = $cstore->store;
                    return ButtonDropdown::widget([
                        'label' => isset($cstore) ? $cstore->name : 'Manage Store',
                        'dropdown' => [
                            'items' => $items,
                        ],
                        'options' => ['class' => 'ajax-dropdown'],
                    ]);
                }
            }
            else
            {
                return isset($model->manager) ? Html::a($model->manager->username, ['user/view', 'id' => $model->manager->id]) : '';
            }
        },
    ];
}
/* 
$columns[] = [
    'class' => ActionColumn::className(),
    'urlCreator' => function ($action, User $model, $key, $index, $column) {
        return Url::toRoute([$action, 'id' => $model->id]);
    },
]; */

?>
<div class="user-profile-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create User Profile', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

</div>
