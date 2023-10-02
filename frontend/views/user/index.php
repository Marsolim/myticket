<?php

use common\models\actors\Administrator;
use common\models\actors\Engineer;
use common\models\actors\User;
use common\models\actors\Store;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UserHelper;
use frontend\models\GeneralManager;
use frontend\models\StoreManager;
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
            return Html::img('uploads/profiles/thumb/'.$model->profile, ['class' => 'rounded-circle', 'alt'=>'profile','width'=>'50','height'=>'50']);
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
    [
        'label' => 'Role',
        'format' => 'raw',
        'value' => function ($model) {
            return match ($model::class) {
                User::class => UserHelper::renderUserCommands($model),
                Administrator::class => 'Administrator',
                Engineer::class => 'Engineer',
                StoreManager::class => 'Store Manager',
                GeneralManager::class => 'General Manager',
            };
        }
    ],
    [
        'label' => 'Associate',
        'format' => 'raw',
        'value' => function ($model) {
            $associate = $model->associate;
            return match ($model::class) {
                User::class => '',
                Administrator::class => '',
                Engineer::class => '',
                StoreManager::class => empty($associate) ? '' : "$associate->code - $associate->name",
                GeneralManager::class => empty($associate) ? '' : "$associate->code - $associate->name",
            };
        },
    ]
];

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
