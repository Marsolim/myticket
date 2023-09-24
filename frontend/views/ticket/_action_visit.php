<?php

use common\models\actors\Engineer;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\actors\User;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */
$engineers = $model->ticket->engineers;
$engineers = ArrayHelper::merge(User::findAll(['status' => User::STATUS_ACTIVE]), $engineers);
$engineers = ArrayHelper::map($engineers, 'id', 'full_name');
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
        <?= $form->field($model->ticket, 'number')->hiddenInput(['maxlength' => true])->label(false) ?>
        <?= $form->field($model, 'user_id')->dropDownList($engineers) ?>
        <?= $form->field($model, 'summary')->textarea()->label("Rekomendasi") ?>
        <div class="view-btn mt-2 text-left">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>