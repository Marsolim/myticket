<?php

use common\models\actors\Engineer;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\actors\User;
use common\models\docs\Inquiry;
use common\models\docs\Invoice;
use common\models\docs\WorkOrder;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

$title = match ($model::class) {
    Invoice::class => 'Invoice',
    Inquiry::class => 'BAP',
    WorkOrder::class => 'SPK',
}

?>
<div class="modal-content animated bounceInCenter" >
    <?php
    $form = ActiveForm::begin([
        'id' => 'qa-form',
        //'enableAjaxValidation' => false, 
        //'validationUrl' => Yii::$app->urlManager->createUrl('ticket/document-validate'),
        'options' => ['enctype' => 'multipart/form-data']
    ]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left"><?= $title ?></h4>
    </div>
    <div class="modal-body">       
        <?= $form->field($model, 'number')->textInput()->label("Nomor Dokumen") ?>
        <?= $form->field($model, 'file')->fileInput()->label("File") ?>
        <div class="view-btn mt-2 text-left">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>