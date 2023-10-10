<?php

use common\models\actors\Engineer;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\actors\User;
use common\models\Item;
use kartik\helpers\Enum;
use kartik\form\ActiveForm;
use kartik\builder\TabularForm;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */


$engineerList = User::find()->andWhere(['id' => $model->user_id])->all();
$engineerList = ArrayHelper::merge($engineerList, Engineer::find()->orderBy(['full_name' => SORT_ASC])->limit(10)->all());
$engineers = ArrayHelper::map($engineerList, 'id', 'full_name');

$itemList = Item::find()->andWhere(['id' => $model->item_id])->all();
$itemList = ArrayHelper::merge($itemList, Item::find()->orderBy(['name' => SORT_ASC])->limit(10)->all());
$itemList = ArrayHelper::map($itemList, 'id', 'name');
$items = ArrayHelper::map($itemList, 'id', 'name');


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
<div class="modal-content animated bounceInTop" >
    <?php
    $form = ActiveForm::begin([
        'id' => 'qa-form', 
        'enableAjaxValidation' => true, 
        'validationUrl' => Yii::$app->urlManager->createUrl('ticket/repair-validate'),
        'class' => 'qa-form'
    ]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Pekerjaan</h4>
    </div>
    <div class="modal-body">       
        <?= $form->field($model, 'user_id')->widget(Select2::class, [
            'data' => $engineers,
            'options' => ['placeholder' => 'Search for Engineer ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'dropdownParent' => '#qa-container',
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['user/engineer-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term, page:params.page}; }'),
                    'processResult' => new JsExpression($resultsJs),
                    'cache' => true
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(engineer) { return engineer.text; }'),
                'templateSelection' => new JsExpression('function (engineer) { return engineer.text; }'),
            ],
        ])->label('Operator') ?>
        <?= $form->field($model, 'action')->textInput() ?>
        <?= $form->field($model, 'item_id')->widget(Select2::class, [
            'data' => $items,
            'options' => ['placeholder' => 'Search for Items ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'dropdownParent' => '#qa-container',
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['item/item-list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term, page:params.page}; }'),
                    'processResult' => new JsExpression($resultsJs),
                    'cache' => true
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(item) { return item.text; }'),
                'templateSelection' => new JsExpression('function (item) { return item.text; }'),
            ],
        ]) ?>
        <?= $form->field($model, 'serial')->textInput() ?>
        <div class="view-btn mt-2 text-left">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>