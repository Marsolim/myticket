<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use kartik\editors\Summernote;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>
<div class="modal-content animated bounceInCenter" >
    <?php
    $form = ActiveForm::begin([
        'id' => 'qa-form',
        'enableAjaxValidation' => true,
        'validationUrl' => Yii::$app->urlManager->createUrl('ticket/discretion-validate'),
        'class' => 'qa-form',
    ]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Alasan tidak tercover MC</h4>
    </div>
    <div class="modal-body">       
        <?= $form->field($model, 'summary')->textarea()->label("Alasan") ?>
        <div class="view-btn mt-2 text-left"> 
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
