<?php

use common\models\ticket\Ticket;
use common\models\actors\Store;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UserHelper;
use frontend\widgets\TicketHeader;
use kartik\daterange\DateRangePicker;
use kartik\editors\assets\SummernoteBs5Asset;
use yii\grid\ActionColumn;
use kartik\export\ExportMenu;
use kartik\select2\Select2Asset;
use kartik\select2\Select2KrajeeAsset;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var frontend\models\TicketSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Servis';
$this->params['breadcrumbs'][] = $this->title;

Select2Asset::register($this);
Select2KrajeeAsset::register($this);
SummernoteBs5Asset::register($this);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    'id',
    'name',
    [
        'attribute'=>'author_id',
        'label'=>'Author',
        'vAlign'=>'middle',
        'width'=>'190px',
        'value'=>function ($model, $key, $index, $widget) { 
            return Html::a($model->author->name, '#', []);
        },
        'format'=>'raw'
    ],
    'color',
    'publish_date',
    'status',
    ['attribute'=>'buy_amount','format'=>['decimal',2], 'hAlign'=>'right', 'width'=>'110px'],
    ['attribute'=>'sell_amount','format'=>['decimal',2], 'hAlign'=>'right', 'width'=>'110px'],
    ['class' => 'kartik\grid\ActionColumn', 'urlCreator'=>function(){return '#';}]
];

$script = <<< JS
//QUICK ACTION
$(document).on('click', '.quick-action', function (event) {
    var href = $(this).attr('href');
    event.preventDefault();
    if ($(this).hasClass('quick-action-link'))
    {
        $.ajax({
            url: href,
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            //data: form_data, //$(this).serialize(),
            type: 'post',
            success: function(response){
                $(response.target).load(response.refresh_link);
            },
            error: function (data) {
                toastr.warning("","There may a error on uploading. Try again later");
            }
        });
    }
    if ($(this).hasClass('quick-action-form'))
    {
        var container = $('#qa-container');
        var content = $('#qa-container .modal-dialog')
        content.load(href, function (r, s, x) {
            var form = content.find("#qa-form");
            form.on('beforeSubmit', function (event) {
                event.preventDefault();
                console.log(form.attr('action'));
                var form_data = new FormData(form[0]);
                $.ajax({
                    url: form.attr('action'),
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data, //$(this).serialize(),
                    type: 'post',
                    success: function(response){
                        console.log(response);
                        if (response.pjax_refresh){
                            container.modal('hide');
                            content.empty();
                            $.pjax.reload({container:"#pjax_list_articles", async:false});
                        }
                        else {
                            $(response.target).load(response.refresh_link);
                        }
                    },
                    complete: function() {
                        console.log('cleaning up');
                        container.modal('hide');
                        content.empty();
                    },
                    error: function (data) {
                        toastr.warning("","There may a error on uploading. Try again later");
                    }
                });
                return false;
            });
        });
        container.modal('show');
    }
});
JS;
$this->registerJs($script);

$exportwidget = ExportMenu::widget([
    'dataProvider' => $articleDataProvider,
    'columns' => $gridColumns,
    'exportConfig' => [
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_HTML => false,
    ],
    'options' => [
        'class' => 'py-2 item-aligment-end'
    ]
]);

?>

<?php Pjax::begin(['id' => 'pjax_list_articles', 
                   'timeout' => false, 
                   'clientOptions' => ['method' => 'POST']]); ?> 

<div class="filter_form">   

<?php $form = ActiveForm::begin([
    'id' => 'search_form',
    'method' => 'post',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'options' => ['class' => 'form-horizontal'], 
]); ?>
    <div class='mb-2'>
    <?= $form->field($articleSearch , 'searchstring')
            ->textInput([
                'placeholder'=> 'Search tickets...',
                'onchange'=>'this.form.submit()'
                ])
            ->label(false);
    ?>
    </div>
    <div>
    <?= $form->field($articleSearch, 'date_range', [
            //'addon'=>['prepend'=>['content'=>'<i class="fas fa-calendar-alt"></i>']],
            'options'=>['class'=>'drp-container mb-2']
        ])->widget(DateRangePicker::class, [
            'presetDropdown'=>true,
            'convertFormat'=>true,
            'includeMonthsFilter'=>true,
            'pluginOptions' => ['locale' => ['format' => 'd-M-y']],
            'options' => ['placeholder' => 'Select range...', 'onchange' => 'this.form.submit()']
        ])->label(false) ?>
    </div>
<?php ActiveForm::end() ?>
</div>

<div class="listView_container position-relative mt-3">
    <div class="position-absolute top-0 end-0">
    </div>
    <?= 
        ListView::widget([
            'dataProvider' => $articleDataProvider ,
            'layout' => "<div class='d-flex flex-row'>{pager}<div class='ms-auto'>$exportwidget</div></div><div class='row'>{items}</div>",
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_ticket',['model' => $model]);
            },
            'emptyText' => "<div class='d-flex flex-row'><div></div><div class='ms-auto'>$exportwidget</div></div>No Services...",
            'pager' => [
                'linkOptions' => ['class'=>'page-link'],
                'linkContainerOptions' => ['class' =>'page-item'],
                'disabledListItemSubTagOptions' => ['tag' => 'div', 'class' => 'page-link disabled-div'],
                'nextPageLabel' => 'Next',
                'prevPageLabel' => 'Prev',
            ],
        ]);
    ?>
</div>

<?= Html::a('<i class="fas fa-plus"></i><span class="visually-hidden">Add Ticket</span>',
    ['ticket/create'],
    [
        'class'=>"btn btn-success btn-floating rounded-circle position-fixed bottom-0 end-0 mb-5 me-5 quick-action quick-action-form",
        'title'=>'Tambah servis baru'
    ]
) ?>
<!-- POPUP MODAL QUICK ACTION -->                            
<div class="modal inmodal quick-action-modal" id="qa-container" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl"></div>
</div>
<?php Pjax::end(); ?>