<?php

use common\models\actors\Depot;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\actors\Store $model */
/** @var yii\widgets\ActiveForm $form */

$data = ArrayHelper::map(Depot::find()->andWhere(['id' => $model->parent_id])->all(), 'id', 
    function ($d) {
        return "$d->code - $d->name";
    });

$resultsJs = <<< JS
function (data, params) {
    params.page = params.page || 1;
    return {
        results: data.results,
        pagination: {
            more: (params.page * 20) < data.total_count
        }
    };
}
JS;

?>

<div class="shop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textArea(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->widget(Select2::class, [
            'data' => $data,
            'options' => ['placeholder' => 'Search for Distribution Center ...'],
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
                //'dropdownParent' => '#qa-container',
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['customer/depot-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term, page:params.page}; }'),
                    'processResult' => new JsExpression($resultsJs),
                    'cache' => true
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
            ],
        ])->label("Distribution Center") ?>
    <div class="form-group">
        <label class="control-label" for="store-contract-sla">SLA</label>
        <input type="text" id="store-contract-sla" class="form-control" name="Store[contract][sla]" value="<?= empty($model->contract) ? '14' : $model->contract->sla ?>">
        <div class="help-block"></div>
    </div>    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
