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

$dataProvider = new ArrayDataProvider([
    'allModels'=>[
        ['id'=>1, 'name'=>'Book Number 1', 'publish_date'=>'25-Dec-2014'],
        ['id'=>2, 'name'=>'Book Number 2', 'publish_date'=>'02-Jan-2014'],
        ['id'=>3, 'name'=>'Book Number 3', 'publish_date'=>'11-May-2014'],
        ['id'=>4, 'name'=>'Book Number 4', 'publish_date'=>'16-Apr-2014'],
        ['id'=>5, 'name'=>'Book Number 5', 'publish_date'=>'16-Apr-2014']
    ]
]);

$attribs = [
    'user_id'=>[
        'label'=> 'Engineer',
        'type'=>TabularForm::INPUT_DROPDOWN_LIST, 
        'items'=>ArrayHelper::map(Engineer::find()->orderBy('full_name')->asArray()->all(), 'id', 'full_name'),
        'columnOptions'=>['width'=>'185px']
    ],
    'action'=>['label'=>'Perbaikan'],
    'item_id'=>[
        'label'=>'Item',
        'type'=>TabularForm::INPUT_DROPDOWN_LIST, 
        'items'=>ArrayHelper::map(Item::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
        'columnOptions'=>['width'=>'185px']
    ],
    'serial'=>['label'=>'S/N'],
];

//$attribs = $model->formAttribs;
//unset($attribs['attributes']['color']);
//$attribs['attributes']['status'] = [
//    'type'=>TabularForm::INPUT_WIDGET, 
//    'widgetClass'=>\kartik\widgets\SwitchInput::class
//];
 
/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>
<div class="modal-content animated bounceInTop" >
    <?php
    $form = ActiveForm::begin(['id' => 'form-add-repair', 
    'enableAjaxValidation' => true, 
    'validationUrl' => Yii::$app->urlManager->createUrl('ticket/repair-validate')]);
    ?>
    <div class="modal-header">
        <h4 class="modal-title text-left">Pekerjaan</h4>
    </div>
    <div class="modal-body">       
        <?= TabularForm::widget([
            'dataProvider'=>$dataProvider,
            'form'=>$form,
            'attributes'=>$attribs,
            'gridSettings'=>[
                'condensed'=>true,
                'floatHeader'=>true,
                'panel'=>[
                    'heading' => '<i class="fas fa-book"></i> Perbaikan',
                    'before' => false,
                    'type' => GridView::TYPE_PRIMARY,
                    'after'=> Html::a('<i class="fas fa-plus"></i> Add New', '#', ['class'=>'btn btn-success', 'id' => 'form-add-repair-new']) . ' ' . 
                            //Html::a('<i class="fas fa-times"></i> Delete', '#', ['class'=>'btn btn-danger']) . ' ' .
                            Html::submitButton('<i class="fas fa-save"></i> Save', ['class'=>'btn btn-primary'])
                ]
            ]   
        ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<< JS
    $(document).ready(function () {
        $("#form-add-repair #form-add-repair-new").on('click', function(event){
            event.preventDefault();
        });
        $("#form-add-repair").on('beforeSubmit', function (event) {
            event.preventDefault();
            var form_data = new FormData($('#form-add-repair')[0]);
            $.ajax({
                   url: $("#form-add-repair").attr('action'),
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

<table class="kv-grid-table table table-hover table-sm kv-table-wrap"><colgroup><col>
<col>
<col>
<col>
<col>
<col class="skip-export">
<col class="skip-export"></colgroup>
<thead class="kv-table-header w0 kv-float-header">
<tr><th class="kv-align-center kv-align-middle" style="width: 4.53%;" data-col-seq="0">#</th><th class="kv-align-top" style="width: 16.76%;" data-col-seq="1">Engineer</th><th class="kv-align-top" data-col-seq="2" style="width: 25.81%;">Perbaikan</th><th class="kv-align-top" style="width: 16.76%;" data-col-seq="3">Item</th><th class="kv-align-top" data-col-seq="4" style="width: 25.8%;">S/N</th><th class="kv-align-center kv-align-middle skip-export" style="width: 5.82%;" data-col-seq="5">Actions</th><th class="kv-all-select kv-align-center kv-align-middle skip-export" style="width: 4.52%;" data-col-seq="6"><input type="checkbox" class="select-on-check-all" name="selection_all" value="1"></th></tr>
</thead>
<tbody>
<tr class="kv-tabform-row w0" data-key="0"><td class="kv-align-center kv-align-middle w0" style="width:50px;" data-col-seq="0">1</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="1">
<select id="-0-user_id" class="form-control" name="[0][user_id]">
<option value="7">Ariana Grande</option>
<option value="4">Bambang Susilo</option>
<option value="2">Marsolim Lin</option>
<option value="6">Terios Alphard</option>
</select>
</td><td class="kv-align-top w0" data-col-seq="2">
<input type="text" id="-0-action" class="form-control" name="[0][action]">
</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="3">
<select id="-0-item_id" class="form-control" name="[0][item_id]">

</select>
</td><td class="kv-align-top w0" data-col-seq="4">
<input type="text" id="-0-serial" class="form-control" name="[0][serial]">
</td><td class="skip-export kv-align-center kv-align-middle w0" style="width:60px;" data-col-seq="5"><a href="/frontend/web/index.php?r=ticket%2Fview&amp;id=0" title="View" aria-label="View" data-pjax="0"><span class="fas fa-eye" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fupdate&amp;id=0" title="Update" aria-label="Update" data-pjax="0" style="display:none"><span class="fas fa-pencil-alt" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fdelete&amp;id=0" title="Delete" aria-label="Delete" data-pjax="0" data-method="post" data-confirm="Are you sure to delete this item?"><span class="fas fa-trash-alt" aria-hidden="true"></span></a></td><td class="skip-export kv-align-center kv-align-middle w0 kv-row-select" style="width:50px;" data-col-seq="6"><input type="checkbox" class="kv-row-checkbox" name="selection[]" value="0"></td></tr>
<tr class="kv-tabform-row w0" data-key="1"><td class="kv-align-center kv-align-middle w0" style="width:50px;" data-col-seq="0">2</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="1">
<select id="-1-user_id" class="form-control" name="[1][user_id]">
<option value="7">Ariana Grande</option>
<option value="4">Bambang Susilo</option>
<option value="2">Marsolim Lin</option>
<option value="6">Terios Alphard</option>
</select>
</td><td class="kv-align-top w0" data-col-seq="2">
<input type="text" id="-1-action" class="form-control" name="[1][action]">
</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="3">
<select id="-1-item_id" class="form-control" name="[1][item_id]">

</select>
</td><td class="kv-align-top w0" data-col-seq="4">
<input type="text" id="-1-serial" class="form-control" name="[1][serial]">
</td><td class="skip-export kv-align-center kv-align-middle w0" style="width:60px;" data-col-seq="5"><a href="/frontend/web/index.php?r=ticket%2Fview&amp;id=1" title="View" aria-label="View" data-pjax="0"><span class="fas fa-eye" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fupdate&amp;id=1" title="Update" aria-label="Update" data-pjax="0" style="display:none"><span class="fas fa-pencil-alt" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fdelete&amp;id=1" title="Delete" aria-label="Delete" data-pjax="0" data-method="post" data-confirm="Are you sure to delete this item?"><span class="fas fa-trash-alt" aria-hidden="true"></span></a></td><td class="skip-export kv-align-center kv-align-middle w0 kv-row-select" style="width:50px;" data-col-seq="6"><input type="checkbox" class="kv-row-checkbox" name="selection[]" value="1"></td></tr>
<tr class="kv-tabform-row w0" data-key="2"><td class="kv-align-center kv-align-middle w0" style="width:50px;" data-col-seq="0">3</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="1">
<select id="-2-user_id" class="form-control" name="[2][user_id]">
<option value="7">Ariana Grande</option>
<option value="4">Bambang Susilo</option>
<option value="2">Marsolim Lin</option>
<option value="6">Terios Alphard</option>
</select>
</td><td class="kv-align-top w0" data-col-seq="2">
<input type="text" id="-2-action" class="form-control" name="[2][action]">
</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="3">
<select id="-2-item_id" class="form-control" name="[2][item_id]">

</select>
</td><td class="kv-align-top w0" data-col-seq="4">
<input type="text" id="-2-serial" class="form-control" name="[2][serial]">
</td><td class="skip-export kv-align-center kv-align-middle w0" style="width:60px;" data-col-seq="5"><a href="/frontend/web/index.php?r=ticket%2Fview&amp;id=2" title="View" aria-label="View" data-pjax="0"><span class="fas fa-eye" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fupdate&amp;id=2" title="Update" aria-label="Update" data-pjax="0" style="display:none"><span class="fas fa-pencil-alt" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fdelete&amp;id=2" title="Delete" aria-label="Delete" data-pjax="0" data-method="post" data-confirm="Are you sure to delete this item?"><span class="fas fa-trash-alt" aria-hidden="true"></span></a></td><td class="skip-export kv-align-center kv-align-middle w0 kv-row-select" style="width:50px;" data-col-seq="6"><input type="checkbox" class="kv-row-checkbox" name="selection[]" value="2"></td></tr>
<tr class="kv-tabform-row w0" data-key="3"><td class="kv-align-center kv-align-middle w0" style="width:50px;" data-col-seq="0">4</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="1">
<select id="-3-user_id" class="form-control" name="[3][user_id]">
<option value="7">Ariana Grande</option>
<option value="4">Bambang Susilo</option>
<option value="2">Marsolim Lin</option>
<option value="6">Terios Alphard</option>
</select>
</td><td class="kv-align-top w0" data-col-seq="2">
<input type="text" id="-3-action" class="form-control" name="[3][action]">
</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="3">
<select id="-3-item_id" class="form-control" name="[3][item_id]">

</select>
</td><td class="kv-align-top w0" data-col-seq="4">
<input type="text" id="-3-serial" class="form-control" name="[3][serial]">
</td><td class="skip-export kv-align-center kv-align-middle w0" style="width:60px;" data-col-seq="5"><a href="/frontend/web/index.php?r=ticket%2Fview&amp;id=3" title="View" aria-label="View" data-pjax="0"><span class="fas fa-eye" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fupdate&amp;id=3" title="Update" aria-label="Update" data-pjax="0" style="display:none"><span class="fas fa-pencil-alt" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fdelete&amp;id=3" title="Delete" aria-label="Delete" data-pjax="0" data-method="post" data-confirm="Are you sure to delete this item?"><span class="fas fa-trash-alt" aria-hidden="true"></span></a></td><td class="skip-export kv-align-center kv-align-middle w0 kv-row-select" style="width:50px;" data-col-seq="6"><input type="checkbox" class="kv-row-checkbox" name="selection[]" value="3"></td></tr>
<tr class="kv-tabform-row w0" data-key="4"><td class="kv-align-center kv-align-middle w0" style="width:50px;" data-col-seq="0">5</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="1">
<select id="-4-user_id" class="form-control" name="[4][user_id]">
<option value="7">Ariana Grande</option>
<option value="4">Bambang Susilo</option>
<option value="2">Marsolim Lin</option>
<option value="6">Terios Alphard</option>
</select>
</td><td class="kv-align-top w0" data-col-seq="2">
<input type="text" id="-4-action" class="form-control" name="[4][action]">
</td><td class="kv-align-top w0" style="width:185px;" data-col-seq="3">
<select id="-4-item_id" class="form-control" name="[4][item_id]">

</select>
</td><td class="kv-align-top w0" data-col-seq="4">
<input type="text" id="-4-serial" class="form-control" name="[4][serial]">
</td><td class="skip-export kv-align-center kv-align-middle w0" style="width:60px;" data-col-seq="5"><a href="/frontend/web/index.php?r=ticket%2Fview&amp;id=4" title="View" aria-label="View" data-pjax="0"><span class="fas fa-eye" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fupdate&amp;id=4" title="Update" aria-label="Update" data-pjax="0" style="display:none"><span class="fas fa-pencil-alt" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fdelete&amp;id=4" title="Delete" aria-label="Delete" data-pjax="0" data-method="post" data-confirm="Are you sure to delete this item?"><span class="fas fa-trash-alt" aria-hidden="true"></span></a></td><td class="skip-export kv-align-center kv-align-middle w0 kv-row-select" style="width:50px;" data-col-seq="6"><input type="checkbox" class="kv-row-checkbox" name="selection[]" value="4"></td></tr>
</tbody></table>

<tr class="kv-tabform-row w0" data-key="0">
    <td class="kv-align-center kv-align-middle w0" style="width:50px;" data-col-seq="0">1</td>
    <td class="kv-align-top w0" style="width:185px;" data-col-seq="1">
        <select id="-0-user_id" class="form-control" name="[0][user_id]">
            <option value="7">Ariana Grande</option>
            <option value="4">Bambang Susilo</option>
            <option value="2">Marsolim Lin</option>
            <option value="6">Terios Alphard</option>
        </select>
    </td>
    <td class="kv-align-top w0" data-col-seq="2">
        <input type="text" id="-0-action" class="form-control" name="[0][action]">
    </td>
    <td class="kv-align-top w0" style="width:185px;" data-col-seq="3">
        <select id="-0-item_id" class="form-control" name="[0][item_id]">
        </select>
    </td>
    <td class="kv-align-top w0" data-col-seq="4">
        <input type="text" id="-0-serial" class="form-control" name="[0][serial]">
    </td>
    <td class="skip-export kv-align-center kv-align-middle w0" style="width:60px;" data-col-seq="5">
        <a href="/frontend/web/index.php?r=ticket%2Fview&amp;id=0" title="View" aria-label="View" data-pjax="0"><span class="fas fa-eye" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fupdate&amp;id=0" title="Update" aria-label="Update" data-pjax="0" style="display:none"><span class="fas fa-pencil-alt" aria-hidden="true"></span></a> <a href="/frontend/web/index.php?r=ticket%2Fdelete&amp;id=0" title="Delete" aria-label="Delete" data-pjax="0" data-method="post" data-confirm="Are you sure to delete this item?"><span class="fas fa-trash-alt" aria-hidden="true"></span></a></td><td class="skip-export kv-align-center kv-align-middle w0 kv-row-select" style="width:50px;" data-col-seq="6"><input type="checkbox" class="kv-row-checkbox" name="selection[]" value="0">
    </td>
</tr>