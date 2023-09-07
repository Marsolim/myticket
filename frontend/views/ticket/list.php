<?php

use common\models\ticket\Ticket;
use common\models\actors\Store;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UserHelper;
use frontend\widgets\TicketHeader;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var frontend\models\TicketSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Servis';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin(['id' => 'pjax_list_articles', 
                   'timeout' => false, 
                   'clientOptions' => ['method' => 'POST']]); ?> 

<?php $mapCategories = [
    ['cID' => 1, 'cName' => 'EXCEL'],
    ['cID' => 2, 'cName' => 'PDF']
]; ?>

<div class="filter_form">   

<?php $form = ActiveForm::begin([
    'id' => 'search_form',
    'method' => 'post',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'options' => ['class' => 'form-horizontal'], 
]); ?>

    <?= $form->field($articleSearch , 'store_id')
            ->dropDownList($mapCategories, 
                        ['prompt'=> Yii::t('app', 'All categories'), 
                         'onchange'=>'this.form.submit()'
                        ])
            ->label(false) 
    ?>  

<?php ActiveForm::end() ?>  

</div>


<div class="listView_container">

    <?= 
        ListView::widget([

            'dataProvider' => $articleDataProvider ,

            'layout' => "{items}\n{pager}",

            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_ticket',['model' => $model]);
            },
            'emptyText' => 'No elements....',
        ]);
    ?>

</div>

<?php Pjax::end(); ?>