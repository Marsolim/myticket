<?php

use common\models\actors\Engineer;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\actors\User;
use kartik\helpers\Enum;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

$data = ArrayHelper::map(User::find()->andWhere(['id' => $model->user_id])->all(), 'id', 'full_name');

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
        'validationUrl' => Yii::$app->urlManager->createUrl('ticket/visit-validate'),
        'class' => 'qa-form'
    ]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Rekomendasi</h4>
    </div>
    <div class="modal-body">       
        <?= $form->field($model, 'user_id')->widget(Select2::class, [
            'data' => $data,
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
                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
            ],
        ]) ?>
        <?= $form->field($model, 'summary')->textarea()->label("Rekomendasi") ?>
        <div class="view-btn mt-2 text-left">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>