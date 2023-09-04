<?php

use common\models\User;
use common\models\Store;
use common\models\ManagedStore;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\helpers\UserHelper;
use kartik\editable\Editable;
use kartik\select2\Select2;
use kartik\file\FileInput;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// $attributes = [
//     'username',
//     'email',
//     'full_name',
//     [
//         'attribute' => 'phone',
//         'format' => 'raw',
//         'value' => function ($model) {
//             return isset($model->phone) ? '<a href="https://wa.me/'.$model->waphone.'" title="Click to send whatsapp message to this number." class="text-decoration-none">'.$model->phone.' <i class="fa-brands fa-whatsapp"></i></a>' : '';
//         }
//     ],
//     [
//         'attribute' => 'profile',
//         'format' => 'raw',
//         'value' => function ($model) {
//             return Html::img('uploads/profiles/thumb/'.$model->profile, ['alt'=>'profile','width'=>'50','height'=>'50']);
//         }
//     ],
//     [
//         'label' => 'Role',
//         'format' => 'raw',
//         'value' => function ($model) {
//             $dropdownlabel = Yii::$app->user->can('manageUser') ? (!$model->role ? 'Assign Role' : $model->role) : (!$model->role ? '' : $model->role);
            
//             $ddroleitems = [
//                 [
//                     'label' => User::ROLE_GENERAL_MANAGER,
//                     'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_GENERAL_MANAGER],
//                 ],
//                 [
//                     'label' => User::ROLE_STORE_MANAGER,
//                     'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_STORE_MANAGER],
//                 ],
//                 [
//                     'label' => User::ROLE_ENGINEER,
//                     'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_ENGINEER],
//                 ]
//             ];

//             if (User::isMemberOfRole(User::ROLE_SYS_ADMINISTRATOR))
//             {
//                 $ddroleitems[] = [
//                     'label' => User::ROLE_ADMINISTRATOR,
//                     'url' => ['assign-role', 'user' => $model->id, 'role' => User::ROLE_ADMINISTRATOR],
//                 ];
//             }

//             return Yii::$app->user->can('manageUser') ? ButtonDropdown::widget([
//                 'label' => $dropdownlabel,
//                 'dropdown' => [
//                     'items' => $ddroleitems,
//                 ],
//                 'options' => ['class' => 'ajax-dropdown']
//             ]) : $dropdownlabel;
//         }
//     ],
// ];

// if (User::isMemberOfRole(User::ROLE_STORE_MANAGER, $model->id))
// {
//     $attributes[] = [
//         'label' => 'Managed Store',
//         'format' => 'raw',
//         'value' => function ($model) {
//             $cstore = ManagedStore::findOne(['user_id' => $model->id, 'active' => ManagedStore::STATUS_ACTIVE])->store;
                
//             if (Yii::$app->user->can('manageUser'))
//             {
//                 $stores = Store::find()->all();
//                 $items = [];
//                 foreach($stores as $store)
//                 {
//                     $items[] = [
//                         'label' => $store->name, 
//                         'url' => ['assign-store', 'store' => $store->id, 'mgr' => $model->id],
//                     ];
//                 }
//                 return ButtonDropdown::widget([
//                     'label' => isset($cstore) ? $cstore->name : 'Manage Store',
//                     'dropdown' => [
//                         'items' => $items,
//                     ],
//                     'options' => ['class' => 'ajax-dropdown']
//                 ]);
//             }
//             else
//             {
//                 return isset($cstore) ? Html::a($cstore->name, ['store/view', 'id' => $cstore->id]) : '';
//             }
//         }
//     ];
// }

$formatJs = <<< 'JS'
var formatRepo = function (repo) {
    if (repo.loading) {
        return repo.text;
    }
    var markup =
'<div class="row">' + 
    '<div class="col-sm-5">' +
        '<p style="margin-left:5px">' + repo.text + '</p>' + 
    '</div>' +
'</div>';
    if (repo.description) {
      markup += '<p>' + repo.description + '</p>';
    }
    return '<div style="overflow:hidden;">' + markup + '</div>';
};
var formatRepoSelection = function (repo) {
    return repo.text || repo.text;
}
JS;
 
// Register the formatting script
$this->registerJs($formatJs, View::POS_HEAD);
 
// script to parse the results into the format expected by Select2
$resultsJs = <<< JS
function (data, params) {
    params.page = params.page || 1;
    return {
        results: data.results,
        pagination: {
            more: (params.page * 30) < data.total_count
        }
    };
}
JS;

$roles = [
    User::ROLE_ENGINEER => User::ROLE_ENGINEER,
    User::ROLE_GENERAL_MANAGER => User::ROLE_GENERAL_MANAGER,
];
if (UserHelper::isAdministrator())
    $roles[User::ROLE_ADMINISTRATOR] = User::ROLE_ADMINISTRATOR;

$uploadevent = <<<JS
    $('#fi-user-avatar').on('filebatchselected', function(event) {
        $(this).fileinput('upload');
    });
JS;
$this->registerJs($uploadevent, View::POS_READY);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::beginTag('section', ['class'=>'vh-50', 'style'=>'background-color: #f4f5f7;']) ?>
    <?= Html::beginTag('div', ['class'=>'container py-5 h-50']) ?>
    <?= Html::beginTag('div', ['class'=>"row d-flex justify-content-center align-items-center h-50"]) ?>
    <?= Html::beginTag('div', ['class'=>"col col-lg-15 mb-4 mb-lg-0"]) ?>
    <?= Html::beginTag('div', ['class'=>"card mb-3", 'style'=>"border-radius: .5rem;"]) ?>
    <?= Html::beginTag('div', ['class'=>"row g-0"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-md-4 gradient-custom text-center text-safe",
        'style'=>"border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;"]) ?>
    <div class='ms-4 my-5'>
    <?= FileInput::widget([
        'name' => 'avatar',
        'options' => ['multiple'=> false, 'accept' => 'image/*', 'id' => 'fi-user-avatar'],
        'pluginOptions' => [
            'initialPreview'=>[
                "uploads/profiles/thumb/".$model->profile,
            ],
            'minFileCount' => 1,
            'maxFileCount'=> 1,
            'initialPreviewAsData'=>true,
            'initialCaption'=>"Profile",
            'initialPreviewConfig' => [
                [
                    'caption' => '',
                    'showRemove' => false,
                    'showUpload' => false, // will be always false for resumable uploads
                    'showDownload' => true,
                    'showZoom' => false,
                    'showDrag' => false,
                    'showRotate' => false,
                ],
            ],
            'overwriteInitial'=>true,
            'initialPreviewDownloadUrl' => "uploads/profiles/thumb/".$model->profile,
            //'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false,
            'showDownload' => true,
            'browseIcon' => '<i class="fas fa-camera"></i> ',
            'uploadUrl' => Url::to(['user/upload-avatar']),
            //'uploadUrl' => "index.php?r=site%2Fupload-document",
            'encodeUrl' => false,
            'uploadExtraData' => [
                'userid'=>$model->id,
            ],
        ]
    ]); ?>
    </div>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-md-8"]) ?>
    <?= Html::beginTag('div', ['class'=>"card-body p-4"]) ?>
    <?= Html::tag('h6', 'Login') ?>
    <?= Html::beginTag('hr', ['class'=>"mt-0 mb-4"]) ?>
    <?= Html::beginTag('div', ['class'=>"row pt-1"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3 overflow-visible"]) ?>
    <?= Html::tag('h6', 'User Name') ?>
    <?= Editable::widget([
        'model' => $model,
        'attribute' => 'username',
        'asPopover' => false,
        'header' => 'User Name',
        'size'=>'md',
        'buttonsTemplate'=>'',
        'formOptions'=>[
            'validateOnBlur'=>false,
            'enableAjaxValidation'=>true,
            'validateOnChange'=>false,
        ],
        'options' => [
            'class'=>'form-control-plaintext text-decoration',
            'placeholder'=>'Enter user name...'
        ]
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'Role') ?>
    <?= !UserHelper::isAdministrator() ? 
        Html::tag('p', $model->role, ['class'=>'text-mute']) : Editable::widget([
        'model' => $model,
        'attribute' => 'role',
        'asPopover' => false,
        'header' => 'Role',
        'inputType' => Editable::INPUT_DROPDOWN_LIST,
        'data' => $roles,
        'buttonsTemplate'=>'{submit}',
        'options' => ['class'=>'form-control-plaintext', 'prompt'=>'Select role...'],
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::tag('h6', 'Information') ?>
    <?= Html::beginTag('hr', ['class'=>"mt-0 mb-4"]) ?>
    <?= Html::beginTag('div', ['class'=>"row pt-1"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-12 mb-3"]) ?>
    <?= Html::tag('h6', 'Full Name') ?>
    <?= Editable::widget([
        'model' => $model,
        'attribute' => 'full_name',
        'asPopover' => false,
        'header' => 'Name',
        'size'=>'md',
        'inlineSettings'=>['options'=>['class' => 'col-auto card panel panel-default']],
        'buttonsTemplate'=>'',
        'formOptions'=>[
            'validateOnBlur'=>false,
            'enableAjaxValidation'=>true,
            'validateOnChange'=>false,
        ],
        'options' => [
            'class'=>'col-auto form-control-plaintext text-decoration-underline', 
            'placeholder'=>'Enter user full name...',
        ]
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'E-mail') ?>
    <?= Editable::widget([
        'model' => $model,
        'attribute' => 'email',
        'asPopover' => false,
        'header' => 'E-mail',
        'size'=>'md',
        'inlineSettings' => ['options'=>['class'=>'col card panel panel-default']],
        'buttonsTemplate'=>'{submit}',
        'options' => ['class'=>'form-control', 'placeholder'=>'Enter user e-mail...']
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'Phone') ?>
    <?= Editable::widget([
        'model' => $model,
        'attribute' => 'phone',
        'asPopover' => false,
        'header' => 'Phone',
        'size'=>'md',
        'buttonsTemplate'=>'{submit}',
        'options' => ['class'=>'', 'placeholder'=>'Enter user phone...']
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::tag('h6', 'Association') ?>
    <?= Html::beginTag('hr', ['class'=>"mt-0 mb-4"]) ?>
    <?= Html::beginTag('div', ['class'=>"row pt-1"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3 overflow-visible"]) ?>
    <?= Html::tag('h6', 'Distribution Center') ?>
    <?= Editable::widget([
        'model' => $model,
        'attribute' => 'associate_id',
        'displayValue' => empty($model->associate) ? $model->associate->toString() : null,
        'asPopover' => false,
        'header' => 'Distribution Center',
        'inputType' => Editable::INPUT_SELECT2,
        'options' => [
            'class'=>'form-control',
            'options' => [
                'placeholder'=>'Select distribution center...',
                'value' => empty($model->associate) ? $model->associate->toString() : null,
                'initValueText' => 'kartik-v/yii2-widgets',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::toRoute('region/list'),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'processResults' => new JsExpression($resultsJs),
                    'cache' => true
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('formatRepo'),
                'templateSelection' => new JsExpression('formatRepoSelection'),
            ]
        ],
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'Company') ?>
    <?= Editable::widget([
        'model' => $model,
        'attribute' => 'company_id',
        'displayValue' => isset($model->company_id) ? $model->company->toString() : null,
        'asPopover' => false,
        'header' => 'Company',
        'inputType' => Editable::INPUT_SELECT2,
        'options' => [
            'class'=>'form-control',
            'options' => [
                'placeholder'=>'Select company...',
                'value' => isset($model->company_id) ? $model->company->toString() : null,
                'initValueText' => 'kartik-v/yii2-widgets',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::toRoute('company/list'),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'processResults' => new JsExpression($resultsJs),
                    'cache' => true
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('formatRepo'),
                'templateSelection' => new JsExpression('formatRepoSelection'),
            ]
        ],
    ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"d-flex justify-content-start"]) ?>
    <?= (UserHelper::isSelf($model->id) || UserHelper::isAdministrator()) ?
        Html::a('Reset Password', ['site/request-password-reset'], ['class' => 'btn btn-primary']) : '' ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('section') ?>
</div>
