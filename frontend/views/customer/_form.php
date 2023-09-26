<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\actors\Store $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="shop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textArea(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region_id')->dropDownList(ArrayHelper::map(
            $regions,
            'id',
            'name'
        ), ['prompt' => '']
    ) ?>

    <?= $form->field($model, 'status_id')->dropDownList(ArrayHelper::map(
            $status,
            'id',
            'name'
        ), ['prompt' => '']
    ) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
