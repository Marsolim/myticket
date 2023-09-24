<?php

use common\models\actors\Engineer;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\actors\Store;
use common\models\Item;
use kartik\helpers\Enum;
use kartik\form\ActiveForm;
use kartik\builder\TabularForm;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
 
/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */
$engineers = ArrayHelper::map(Engineer::find()->all(), 'id', 'full_name');
$items = ArrayHelper::map(Item::find()->all(), 'id', 'name');
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
        <?= $form->field($model, 'user_id')->dropDownList($engineers) ?>
        <?= $form->field($model, 'action')->textInput() ?>
        <?= $form->field($model, 'item_id')->dropDownList($items) ?>
        <?= $form->field($model, 'serial')->textInput() ?>
        <div class="view-btn mt-2 text-left">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>