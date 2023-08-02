<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
//use kartik\widgets\FileInput;
use kartik\file\FileInput;
use kartik\icons\FontAwesomeAsset;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

FontAwesomeAsset::register($this);

?>

<div class="user-profile-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?= $form->field($model, 'username')->textInput(['class' => 'form-control class-content-title_series', 'placeholder' => 'Generated', 'disabled' => true]) ?>
    
    <?= $form->field($model, 'email')->textInput(['class' => 'form-control class-content-title_series', 'placeholder' => 'Generated']) ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatar')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'initialPreview'=>[
                "uploads/profiles/thumb/".$model->profile,
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
            'initialPreviewDownloadUrl' => "uploads/profiles/thumb/".$model->profile,
            //'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false,
            'showDownload' => true,
            'browseIcon' => '<i class="fas fa-camera"></i> ',
        ]
    ]); ?>

    <?php ActiveForm::end(); ?>

</div>
