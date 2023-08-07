<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\helpers\RoleHelper;
use frontend\helpers\TStatusHelper;
use common\models\User;
use common\models\Document;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

$steps = $model->getModels();
ArrayHelper::multisort($steps, ['action_date'], [SORT_DESC]);
//$last = end($steps);
?>
<?= Html::beginTag('div', ['id' => $id]) ?>
<?php foreach($steps as $index => $step) {
  $sto = TStatusHelper::getLabel($step); 
  $date = new DateTime('@'.$step->action_date);
  $diff = $date->diff(new DateTime('now'));
  $documents = Document::findAll(['action_id' => $step->id]);
?>
<div class="d-flex p-3 border-bottom">
  <?= Html::img('uploads/profiles/thumb/'.$step->engineer->profile, ['class' => 'rounded-circle', 'loading' => 'lazy', 'alt'=>'profile', 'style' => 'width:50px;height:50px']) ?></span>
  <div class="d-flex w-100 ps-3">
    <div class="w-100">
      <?= Html::beginTag('h6', ['class' => 'd-flex bd-highlight text-body']) ?>
      <?= Html::a(implode(' ', [
          $step->engineer->full_name, 
          Html::tag('span', $step->engineer->email, ['class' => 'small text-muted font-weight-normal'])
        ]),
        ['user/view/', 'id' => $step->engineer_id],
        [
          'class' => 'bd-highlight text-body align-self-baseline'
        ]) ?>
      <?= Html::tag('span', ' • ', ['class' => 'bd-highlight align-self-baseline small text-muted font-weight-normal']) ?>
      <?= Html::tag('span', $diff->format('%R%a'), ['class' => 'align-self-baseline bd-highlight small text-muted font-weight-normal']) ?>
      <?= Html::tag('span', $date->format('d F Y H:i.s'), ['class' => 'align-self-baseline ms-auto bd-highlight small text-muted font-weight-normal']) ?> 
      <?= Html::endTag('h6') ?> 
      <?= Html::tag('h6', $step->action) ?>
      <?= Html::tag('p', $step->summary, ['style'=>'line-height: 1.2;']) ?>
      <?php if (isset($documents) && $documents) {
        $collid = Yii::$app->security->generateRandomString();
      ?> 
        <?= Html::button('<i class="fa-solid fa-paperclip fa-fw"></i> Attachments', ['class'=>'btn btn-link text-decoration-none', 'data-bs-toggle' =>'collapse', 'data-bs-target' => '#'.$collid]) ?>
        <?= Html::beginTag('div', ['id'=>$collid, 'class'=>'collapse']) ?>
        <?= Html::ul($documents, ['class' => 'list-unstyled d-flex justify-content-between mb-0 pe-xl-5',
              'item'=> function($item, $index){
                  return Html::tag('li', 
                  Html::a($item->fileIcon.' '.$item->uploadname, ['document/download/', 'id' => $item->id], 
                  ['class' => 'btn btn-link text-decoration-none']));
              }]) ?> 
        <?= Html::endTag('div') ?>
      <?php } ?>
      <ul class="list-unstyled d-flex justify-content-between mb-0 pe-xl-5">
        <li><?= $sto ?></li>
      </ul>
    </div>
  </div>
<?= Html::endTag('div') ?>
<?php } ?>
