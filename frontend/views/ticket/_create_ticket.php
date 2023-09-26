<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use kartik\helpers\Enum;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */


$dataList = Store::find()->andWhere(['id' => $model->customer_id])->all();
$dataList = ArrayHelper::merge($dataList, Store::find()->orderBy(['name' => SORT_ASC])->limit(10)->all());
$data = ArrayHelper::map($dataList, 'id', 'name');

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
<div class="modal-content animated bounceInCenter" >
    <?php
    $form = ActiveForm::begin([
        'id' => 'qa-form', 
        'enableAjaxValidation' => true, 
        'validationUrl' => Yii::$app->urlManager->createUrl('ticket/validate-create')
    ]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Ticket Service Baru</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($model, 'external_number')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'customer_id')->widget(Select2::class, [
            'data' => $data,
            'options' => ['placeholder' => 'Search for Store ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'dropdownParent' => '#qa-container',
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['customer/store-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term, page:params.page}; }'),
                    'processResult' => new JsExpression($resultsJs),
                    'cache' => true
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(store) { return store.text; }'),
                'templateSelection' => new JsExpression('function (store) { return store.text; }'),
            ],
        ]) ?>
        <?= $form->field($model, 'problem')->textInput(['maxlength' => true]) ?>
        <div class="view-btn mt-2 text-left"> 
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-default' : 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
