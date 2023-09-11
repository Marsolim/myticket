<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>
<div class="modal-content animated bounceInCenter" >
    <?php
    $form = ActiveForm::begin(['id' => 'form-create-ticket', 
    'enableAjaxValidation' => true, 
    'validationUrl' => Yii::$app->urlManager->createUrl('ticket/validate-create')]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Ticket Service Baru</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        <?= $form->field($model, 'external_number')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'customer_id')->dropDownList(ArrayHelper::map(
                $stores,
                'id',
                'name'
            ), ['prompt' => '']
        ) ?>
        <?= $form->field($model, 'problem')->textInput(['maxlength' => true]) ?>
        <div class="view-btn mt-2 text-left"> 
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-default' : 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<< JS
   $(document).ready(function () {
        $("#form-create-ticket").on('beforeSubmit', function (event) {
            event.preventDefault();
            var form_data = new FormData($('#form-create-ticket')[0]);
            $.ajax({
                   url: $("#form-create-ticket").attr('action'),
                   dataType: 'JSON',
                   cache: false,
                   contentType: false,
                   processData: false,
                   data: form_data, //$(this).serialize(),
                   type: 'post',
                   beforeSend: function() {
                   },
                   success: function(response){
                       toastr.success("",response.message);
                       $('#addQuickActionFormModel').modal('hide');
                       $.pjax.reload({container: '#pjax_list_articles', async: false});
                   },
                   complete: function() {
                   },
                   error: function (data) {
                      toastr.warning("","There may a error on uploading. Try again later");
                   }
                });
            return false;
        });
    });
JS;
$this->registerJs($script);
?>