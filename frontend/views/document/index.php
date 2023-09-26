<?php

use common\models\docs\Document;
use common\models\docs\Inquiry;
use common\models\docs\Invoice;
use common\models\docs\WorkOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\RegionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Documents';
$this->params['breadcrumbs'][] = $this->title;
$invoicefilter = function($doc){
    return $doc instanceof Invoice;
};
$invoices = array_filter($model, $invoicefilter);
$spkfilter = function($doc){
    return $doc instanceof Inquiry || $doc instanceof WorkOrder;
};
$spks = array_filter($model, $spkfilter);
$othersfilter = function($doc){
    return !$doc instanceof Invoice && !$doc instanceof Inquiry && !$doc instanceof WorkOrder;
};
$others = array_filter($model, $othersfilter);
?>
<div class="region-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    if (isset($invoices) && $invoices)
    {
    ?>
    <h2><?= Html::encode('DAFTAR DOKUMEN INVOICE') ?></h2>

    <?php
        foreach($invoices as $invoice)
        {
            echo Html::a($invoice->fileIcon.' '.$invoice->uploadname, ['document/download/', 'id' => $invoice->id], ['class' => 'btn btn-link text-decoration-none']);
            echo '<br/>';
        }
    }
    ?>
    <?php
    if (isset($spks))
    {
    ?>
    <h2><?= Html::encode('DAFTAR DOKUMEN SPK/BAP') ?></h2>

    <?php
        foreach($spks as $spk)
        {
            echo Html::a($spk->fileIcon.' '.$spk->uploadname, ['document/download/', 'id' => $spk->id], ['class' => 'btn btn-link text-decoration-none']);
            echo '<br/>';
        }
    }
    ?>
    <?php
    if (isset($others))
    {
    ?>
    <h2><?= Html::encode('DAFTAR DOKUMEN LAIN-LAIN') ?></h2>

    <?php
        foreach($others as $other)
        {
            echo Html::a($other->fileIcon.' '.$other->uploadname, ['document/download/', 'id' => $other->id], ['class' => 'btn btn-link text-decoration-none']);
            echo '<br/>';
        }
    }
    ?>
</div>
