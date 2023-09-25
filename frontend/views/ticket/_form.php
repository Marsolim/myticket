<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\editors\Summernote;
use kartik\icons\FontAwesomeAsset;
use kartik\datecontrol\DateControl;

/** @var yii\web\View $this */
/** @var frontend\models\Ticket $model */
/** @var yii\widgets\ActiveForm $form */

FontAwesomeAsset::register($this);
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->dropDownList(ArrayHelper::map(
            $stores,
            'id',
            'name'
        ), ['prompt' => '']
    ) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'engineer_id')->dropDownList(ArrayHelper::map(
            $engineers,
            'id',
            'full_name'
        ), ['prompt' => '']
    ) ?>

    <?= $form->field($model, 'issued_at')->widget(DateControl::class, [
        'type' => 'date',
        'ajaxConversion' => true,
        'autoWidget' => true,
        'widgetClass' => '',
        'displayFormat' => 'php:d-F-Y',
        'saveFormat' => 'php:U',
        'saveTimezone' => 'UTC',
        'displayTimezone' => 'Asia/Jakarta',
        //'saveOptions' => [
        //    'label' => 'Input saved as: ',
        //    'type' => 'text',
        //    'readonly' => true,
        //    'class' => 'hint-input text-muted'
        //],
        'widgetOptions' => [
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'php:d-F-Y'
            ]
        ]
    ]) ?>

    <!-- <?= $form->field($model, 'issued_at')->widget(\yii\jui\DatePicker::class, [
        // if you are using bootstrap, the following line will set the correct style of the input field
        'options' => ['class' => 'form-control', 'value' => time()],
        // ... you can configure more DatePicker properties here
    ]) ?> -->
    
    <?= $form->field($model, 'problem')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'problem_description')->widget(Summernote::class, [
        'useKrajeePresets' => true,
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
