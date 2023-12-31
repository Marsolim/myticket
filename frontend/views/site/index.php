<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\TicketSummary;
use frontend\helpers\TicketHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

$this->title = 'Rekap Servis Nasional';

// An Array Data Provider
$dataProvider = new ArrayDataProvider([
    'allModels' => TicketHelper::summary()->all(),
]);
//$dataProvider = new ActiveDataProvider([
//    'query' => TicketHelper::summary(),
//]);
/* 
$dataProvider = new ArrayDataProvider(['allModels' => [    
    ['id' => 1, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-1', 'region' => 'IT-5', 'amount' => 1400],
    ['id' => 2, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-1', 'region' => 'IT-5', 'amount' => 1300],
    ['id' => 3, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-1', 'region' => 'IT-6', 'amount' => 2400],
    ['id' => 4, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-1', 'region' => 'IT-6', 'amount' => 4900],
    ['id' => 5, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-2', 'region' => 'IT-7', 'amount' => 7340],
    ['id' => 6, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-2', 'region' => 'IT-7', 'amount' => 4560],
    ['id' => 7, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-2', 'region' => 'IT-8', 'amount' => 3550],
    ['id' => 8, 'year' => 2017, 'month' => 'JAN', 'cat' => 'CAT-2', 'region' => 'IT-8', 'amount' => 9450],
    ['id' => 9, 'year' => 2017, 'month' => 'FEB', 'cat' => 'CAT-2', 'region' => 'IT-6', 'amount' => 3900],
    ['id' => 10, 'year' => 2017, 'month' => 'FEB', 'cat' => 'CAT-2', 'region' => 'IT-6', 'amount' => 52200],
    ['id' => 11, 'year' => 2018, 'month' => 'JAN', 'cat' => 'CAT-3', 'region' => 'IT-5', 'amount' => 4700],
    ['id' => 12, 'year' => 2018, 'month' => 'JAN', 'cat' => 'CAT-3', 'region' => 'IT-5', 'amount' => 11900],
]]); 
*/
// Group Footer Settings
$gfstore = function ($model, $key, $index, $widget) {
    return [
        'mergeColumns' => [[1, 2]], 
        'content' => [              // content to show in each summary cell
            1 => 'Total per Toko (' . $model['customer_name'] . ')',
            3 => GridView::F_SUM,
            4 => GridView::F_SUM,
            5 => GridView::F_SUM,
            6 => GridView::F_SUM,
            7 => GridView::F_SUM,
            8 => GridView::F_SUM,
            9 => GridView::F_SUM,
        ],
        'contentFormats' => [      // content reformatting for each summary cell
            3 => ['format' => 'number', 'decimals' => 0],
            4 => ['format' => 'number', 'decimals' => 0],
            5 => ['format' => 'number', 'decimals' => 0],
            6 => ['format' => 'number', 'decimals' => 0],
            7 => ['format' => 'number', 'decimals' => 0],
            8 => ['format' => 'number', 'decimals' => 0],
            9 => ['format' => 'number', 'decimals' => 0],
        ],
        'contentOptions' => [      // content html attributes for each summary cell
            3 => ['class' => 'text-center text-end'],
            4 => ['class' => 'text-center text-end'],
            5 => ['class' => 'text-center text-end'],
            6 => ['class' => 'text-center text-end'],
            7 => ['class' => 'text-center text-end'],
            8 => ['class' => 'text-center text-end'],
            9 => ['class' => 'text-center text-end'],
        ],
        'options' => ['class' => 'active table-active h6']
    ];
};
$gfengineer = function ($model, $key, $index, $widget) {
    return [
        'mergeColumns' => [[2, 2]], 
        'content' => [              // content to show in each summary cell
            2 => "Total per Teknisi ({$model->engineer->full_name}→{$model->store->name})",
            3 => GridView::F_SUM,
            4 => GridView::F_SUM,
            5 => GridView::F_SUM,
            6 => GridView::F_SUM,
            7 => GridView::F_SUM,
            8 => GridView::F_SUM,
            9 => GridView::F_SUM,
        ],
        'contentFormats' => [      // content reformatting for each summary cell
            3 => ['format' => 'number', 'decimals' => 0],
            4 => ['format' => 'number', 'decimals' => 0],
            5 => ['format' => 'number', 'decimals' => 0],
            6 => ['format' => 'number', 'decimals' => 0],
            7 => ['format' => 'number', 'decimals' => 0],
            8 => ['format' => 'number', 'decimals' => 0],
            9 => ['format' => 'number', 'decimals' => 0],
        ],
        'contentOptions' => [      // content html attributes for each summary cell
            3 => ['class' => 'text-center'],
            4 => ['class' => 'text-center'],
            5 => ['class' => 'text-center'],
            6 => ['class' => 'text-center'],
            7 => ['class' => 'text-center'],
            8 => ['class' => 'text-center'],
            9 => ['class' => 'text-center'],
        ],
        'options' => ['class' => 'success table-success h6']
    ];
};

/* 
$gfCategory = function ($model, $key, $index, $widget) {
    return [
        'mergeColumns' => [[3, 5]], 
        'content' => [              // content to show in each summary cell
            3 => "Category Total ({$model['cat']}→{$model['month']}→{$model['year']})",
            6 => GridView::F_SUM,
        ],
        'contentFormats' => [      // content reformatting for each summary cell
            6 => ['format' => 'number', 'decimals' => 2],
        ],
        'contentOptions' => [      // content html attributes for each summary cell
            6 => ['class' => 'text-right text-end'],
        ],
        'options' => ['class' => 'danger table-danger h6'],
    ];
};
$gfRegion = function ($model, $key, $index, $widget) {
    return [
        'mergeColumns' => [[4, 5]], 
        'content' => [              // content to show in each summary cell
            4 => "Region Total ({$model['region']}→{$model['cat']}→{$model['month']}→{$model['year']})", 
            6 => GridView::F_SUM,
        ],
        'contentFormats' => [      // content reformatting for each summary cell
            6 => ['format' => 'number', 'decimals' => 2],
        ],
        'contentOptions' => [      // content html attributes for each summary cell
            6 => ['class' => 'text-right text-end'],
        ],
        'options' => ['class' => 'info table-info h6']
    ];
}; 
*/
$valuerenderer = function ($model, $key, $index, $widget) { 
    return $model[$widget->attribute] > 0 ? 
        Html::a($model[$widget->attribute], ['ticket/index'], [ 'data-method' => 'POST', '
            data-params' => [
                'status' => $widget->attribute, 
                'cust' => $model['customer_id'], 
            ],
        ]) : 
        '-';
};

$summaryrenderer = function ($summary, $data, $widget) {
    $domdata = implode('', $data);
    $dom = new DOMDocument();
    $dom->loadHTML($domdata);
    $xpath = new DOMXPath($dom);
    $tags = $xpath->query('//a');
    $post_option = ['data-method' => 'POST'];
    if (!empty($widget->attribute)) $post_option['data-params'] = ['status' => $widget->attribute];
    return Html::a(array_sum(ArrayHelper::getColumn($tags, 'nodeValue')), ['ticket/index'], $post_option);
};

$totalrenderer = function ($model, $key, $index, $widget) { 
    $total = $model['b'] + $model['p']+ $model['s'] + $model['r'] + $model['n'] + $model['d'];
    return $total > 0 ? 
        Html::a($total, ['ticket/index'], [ 'data-method' => 'POST', 
            'data-params' => [
                'cust' => $model['customer_id'], 
            ],
        ]) : 
        '-';
}

?>
<div class="ticket-index">

<h1><?= Html::encode($this->title) ?></h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'showPageSummary' => true,
    'pjax' => true,
    'hover' => true,
    'panel' => [
        'type' => 'primary',
        'heading' => 'Rekap Servis Nasional per Cabang',
        'footer' => nl2br(TicketSummary::renderLegends()),
    ],
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    'columns' => [
        // note that you MUST NOT have the first column as a grid group
        // to achieve that add a dummy hidden column like shown below
        ['class' => 'kartik\grid\SerialColumn'], 
        ['attribute' => 'customer_name', 'label' => 'Cabang', 'pageSummary' => 'Total Keseluruhan'],
        [
            'attribute' => 'b', 'label' => 'B', 'format' => 'raw',
            'value' => $valuerenderer, 'vAlign' => 'middle',
            'hAlign' => 'center', 'width' => '80px',
            'pageSummary' => $summaryrenderer
        ],
        [
            'attribute' => 'p', 'label' => 'P', 'format' => 'raw',
            'value' => $valuerenderer, 'vAlign' => 'middle',
            'hAlign' => 'center', 'width' => '80px',
            'pageSummary' => $summaryrenderer
        ],
        [
            'attribute' => 's', 'label' => 'S', 'format' => 'raw',
            'value' => $valuerenderer, 'vAlign' => 'middle',
            'hAlign' => 'center', 'width' => '80px',
            'pageSummary' => $summaryrenderer
        ],
        [
            'attribute' => 'r', 'label' => 'R', 'format' => 'raw',
            'value' => $valuerenderer, 'vAlign' => 'middle',
            'hAlign' => 'center', 'width' => '80px',
            'pageSummary' => $summaryrenderer
        ],
        [
            'attribute' => 'n', 'label' => 'N', 'format' => 'raw',
            'value' => $valuerenderer, 'vAlign' => 'middle',
            'hAlign' => 'center', 'width' => '80px',
            'pageSummary' => $summaryrenderer
        ],
        [
            'attribute' => 'd', 'label' => 'D', 'format' => 'raw',
            'value' => $valuerenderer, 'vAlign' => 'middle',
            'hAlign' => 'center', 'width' => '80px',
            'pageSummary' => $summaryrenderer
        ],
        [
            'class' => 'kartik\grid\FormulaColumn',
            'header' => 'Total', 'format' => 'raw',
            'value' => $totalrenderer,
            'mergeHeader' => true,
            'width' => '80px',
            'hAlign' => 'center',
            'pageSummary' => $summaryrenderer
        ],
    ],

]) ?>

</div>

