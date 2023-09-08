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
    $form = ActiveForm::begin(['id' => 'form-add-visit', 
    'enableAjaxValidation' => true, 
    'validationUrl' => Yii::$app->urlManager->createUrl('ticket/visit-validate')]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Alasan tidak tercover MC</h4>
    </div>
    <div class="modal-body">       
        <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'summary')->textInput(['maxlength' => true])->label("Alasan") ?>
        <div class="view-btn mt-2 text-left"> 
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-default' : 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<< JS
   $(document).ready(function () {
        $("#form-add-visit").on('beforeSubmit', function (event) {
            event.preventDefault();
            var form_data = new FormData($('#form-add-visit')[0]);
            $.ajax({
                   url: $("#form-add-visit").attr('action'),
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