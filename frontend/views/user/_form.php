<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use frontend\helpers\UserHelper;
use common\models\User;
//use kartik\widgets\FileInput;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\icons\FontAwesomeAsset;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

FontAwesomeAsset::register($this);

$roles = [
    ['value' => User::ROLE_ENGINEER, 'display' => User::ROLE_ENGINEER],
    ['value' => User::ROLE_GENERAL_MANAGER, 'display' => User::ROLE_GENERAL_MANAGER],
];
if (UserHelper::isAdministrator())
    $roles[] = ['value' => User::ROLE_ADMINISTRATOR, 'display' => User::ROLE_ADMINISTRATOR];

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
    
?>

<div class="user-profile-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 
            'validateOnBlur'=>false,
            'enableAjaxValidation'=>true,
            'validateOnChange'=>false,]]); ?>
    <?= Html::beginTag('section', ['class'=>'vh-10', 'style'=>'background-color: #f4f5f7;']) ?>
    <?= Html::beginTag('div', ['class'=>'container py-5 h-10']) ?>
    <?= Html::beginTag('div', ['class'=>"row d-flex justify-content-center align-items-center h-10"]) ?>
    <?= Html::beginTag('div', ['class'=>"col col-lg-15 mb-4 mb-lg-0"]) ?>
    <?= Html::beginTag('div', ['class'=>"card mb-3", 'style'=>"border-radius: .5rem;"]) ?>
    <?= Html::beginTag('div', ['class'=>"row g-0"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-md-4 gradient-custom text-center text-safe",
        'style'=>"border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;"]) ?>
    <?= $form->field($model, 'avatar', ['labelOptions' => ['style' => 'display: none'], 'options' => ['class' => 'ms-4 my-5']])
        ->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'initialPreview'=>[
                "uploads/profiles/thumb/default_profile.jpg",
            ],
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
            'initialPreviewDownloadUrl' => "uploads/profiles/thumb/default_profile.jpg",
            //'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false,
            'showDownload' => true,
            'browseIcon' => '<i class="fas fa-camera"></i> ',
        ]
    ]); ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-md-8"]) ?>
    <?= Html::beginTag('div', ['class'=>"card-body p-4"]) ?>
    <?= Html::tag('h6', 'Login') ?>
    <?= Html::beginTag('hr', ['class'=>"mt-0 mb-4"]) ?>
    <?= Html::beginTag('div', ['class'=>"row pt-1"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'User Name') ?>
    <?= $form->field($model, 'username', ['labelOptions' => ['style' => 'display: none']])->textInput(['class' => 'form-control class-content-title_series', 'placeholder' => 'User name']) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'Role') ?>
    <?= $form->field($model, 'role', ['labelOptions' => ['style' => 'display: none']])->dropDownList(ArrayHelper::map(
            $roles,
            'value',
            'display'
        ), ['prompt' => 'Select role...']
    ) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::tag('h6', 'Information') ?>
    <?= Html::beginTag('hr', ['class'=>"mt-0 mb-4"]) ?>
    <?= Html::beginTag('div', ['class'=>"row pt-1"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-12 mb-3"]) ?>
    <?= Html::tag('h6', 'Full Name') ?>
    <?= $form->field($model, 'full_name', ['labelOptions' => ['style' => 'display: none']])->textInput(['maxlength' => true]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'E-mail') ?>
    <?= $form->field($model, 'email', ['labelOptions' => ['style' => 'display: none']])->textInput(['class' => 'form-control class-content-title_series', 'placeholder' => 'Email']) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'Phone') ?>
    <?= $form->field($model, 'phone', ['labelOptions' => ['style' => 'display: none']])->textInput(['maxlength' => true]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::tag('h6', 'Association') ?>
    <?= Html::beginTag('hr', ['class'=>"mt-0 mb-4"]) ?>
    <?= Html::beginTag('div', ['class'=>"row pt-1"]) ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3 overflow-visible"]) ?>
    <?= Html::tag('h6', 'Distribution Center') ?>
    <?= $form->field($model, 'region_id', ['labelOptions' => ['style' => 'display: none']])
        ->widget(Select2::classname(), [
            'class'=>'form-control',
            'options' => [
                'placeholder'=>'Select distribution center...',
                'value' => isset($model->region_id) ? $model->region->toString() : null,
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
        ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"col-6 mb-3"]) ?>
    <?= Html::tag('h6', 'Company') ?>
    <?= $form->field($model, 'company_id', ['labelOptions' => ['style' => 'display: none']])
        ->widget(Select2::classname(), [
            'class'=>'form-control',
            'options' => [
                'placeholder'=>'Select company...',
                'value' => isset($model->company_id) ? $model->company->toString() : null,
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
        ]) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::beginTag('div', ['class'=>"d-flex justify-content-start"]) ?>
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
    <?= Html::endTag('section') ?>
    <?php ActiveForm::end(); ?>

</div>
