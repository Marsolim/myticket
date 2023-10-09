<?php

use common\models\actors\Company;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\actors\Depot $model */
/** @var yii\widgets\ActiveForm $form */

$data = ArrayHelper::map(Company::find()->all(), 'id', 
    function ($d) {
        return "$d->code - $d->name";
    });

?>

<div class="region-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textarea(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'parent_id')->widget(Select2::class, [
            'data' => $data,
            'options' => ['placeholder' => 'Search for Company ...'],
            'addon' => [
                'append' => [
                    'content' => Html::button('<i class="fas fa-file-circle-plus"></i>', [
                            'class' => 'btn btn-primary',
                            'title' => 'Add new Distribution Center',
                            'data-toggle' => 'tooltip'
                        ]),
                    'asButton' => true
                ]
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
            ],
        ])->label("Company") ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
