<?php

use common\models\ticket\Ticket;
use common\models\actors\Store;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UserHelper;
use frontend\widgets\TicketHeader;
use yii\grid\ActionColumn;
use kartik\export\ExportMenu;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var frontend\models\TicketSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Servis';
$this->params['breadcrumbs'][] = $this->title;

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
    $('#addQuickActionFormModel').modal('show').find('.modal-dialog').load(href);
    event.preventDefault();
});
//QUICK TICKET
$(document).on('click', '.quick-ticket', function (event) {       
    var href = $(this).attr('href');
    $('#addQuickTicketFormModel').modal('show').find('.modal-dialog').load(href);
    event.preventDefault();
});

JS;
$this->registerJs($script);

?>

<?php Pjax::begin(['id' => 'pjax_list_articles', 
                   'timeout' => false, 
                   'clientOptions' => ['method' => 'POST']]); ?> 

<?php $mapCategories = [
    [1 => 'EXCEL'],
    [2 => 'PDF']
]; ?>

<div class="filter_form">   

<?php $form = ActiveForm::begin([
    'id' => 'search_form',
    'method' => 'post',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'options' => ['class' => 'form-horizontal'], 
]); ?>

    <?= $form->field($articleSearch , 'customer_id')
            ->dropDownList($mapCategories, 
                        ['prompt'=> Yii::t('app', 'All categories'), 
                         'onchange'=>'this.form.submit()'
                        ])
            ->label(false) 
    ?>  

<?php ActiveForm::end() ?>  

</div>

<div class="listView_container position-relative mt-3">
    <div class="position-absolute top-0 end-0">
    <?= ExportMenu::widget([
        'dataProvider' => $articleDataProvider,
        'columns' => $gridColumns,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_HTML => false,
        ],
    ]) ?>
    </div>
    <?= 
        ListView::widget([
            'dataProvider' => $articleDataProvider ,
            'layout' => "{pager}<div class='row'>{items}</div>",
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_ticket',['model' => $model]);
            },
            'emptyText' => 'No Services...',
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

<?= Html::a('<i class="fas fa-plus"></i><span class="visually-hidden">Add Category</span>',
    ['ticket/create'],
    [
        'class'=>"btn btn-success position-fixed bottom-0 end-0 mb-5 me-5 rounded-circle quick-ticket",
        'title'=>'Tambah servis baru'
    ]
) ?>
<!-- POPUP MODAL QUICK TICKET -->                            
<div class="modal inmodal quick-ticket-model" id="addQuickTicketFormModel" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md "></div>
</div> 
<!-- POPUP MODAL QUICK ACTION -->                            
<div class="modal inmodal quick-action-model" id="addQuickActionFormModel" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md "></div>
</div> 
<?php Pjax::end(); ?>