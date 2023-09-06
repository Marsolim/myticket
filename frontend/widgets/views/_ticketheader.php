<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\helpers\Enum;
use frontend\helpers\RoleHelper;
use common\models\ticket\Ticket;
use common\models\actors\Store;
use common\models\doc\Document;

/** @var yii\web\View $this */
/** @var common\models\Ticket $model */
/** @var yii\widgets\ActiveForm $form */

$store = $model->getStore();

?>
<?= Html::beginTag('div', ['id' => $id]) ?>
<?php foreach($steps as $index => $step) {
  //$sto = TStatusHelper::getLabel($step); 
  //$date = new DateTime('@'.$step->action_date);
  //$diff = $date->diff(new DateTime('now'));
  $documents = Document::findAll(['action_id' => $step->id]);
?>
<div class="d-flex p-3 border-bottom">
  <?= Html::img('uploads/profiles/thumb/'.$model->issuer->profile, ['class' => 'rounded-circle', 'loading' => 'lazy', 'alt'=>'profile', 'style' => 'width:50px;height:50px']) ?></span>
  <?= Html::a($model->issuer->username, ['user/view/', 'id'=>$model->issuer->id]) ?>
  <div class="d-flex w-100 ps-3">
    <div class="w-100">
      <?= Html::beginTag('h6', ['class' => 'd-flex bd-highlight text-body']) ?>
      <?= Html::a(implode(' ', [
          implode('-', [$store->code, $store->name]),
          Html::tag('span', implode('-', [$model->number]), ['class' => 'small text-muted font-weight-normal'])
        ]),
        ['ticket/view/', 'id' => $model->id],
        [
          'class' => 'bd-highlight text-body align-self-baseline'
        ]) ?>
      <?= Html::tag('span', ' • ', ['class' => 'bd-highlight align-self-baseline small text-muted font-weight-normal']) ?>
      <?= Html::tag('span', Enum::timeElapsed(date('Y-F-d H:i:s', $model->created_at)), ['class' => 'align-self-baseline bd-highlight small text-muted font-weight-normal']) ?>
      <?= Html::tag('span', date('d F Y H:i.s', $model->created_at), ['class' => 'align-self-baseline ms-auto bd-highlight small text-muted font-weight-normal']) ?> 
      <?= Html::endTag('h6') ?> 
      <?= Html::tag('h6', $model->problem) ?>
      <?= Html::tag('p', $model->status, ['style'=>'line-height: 1.2;']) ?>
      <ul class="list-unstyled d-flex justify-content-between mb-0 pe-xl-5">
        <li><?= $sto ?></li>
      </ul>
    </div>
  </div>
<?= Html::endTag('div') ?>
<?php } ?>
