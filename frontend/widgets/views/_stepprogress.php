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
$last = end($steps);
?>
<?= Html::beginTag('div', ['id' => $id]) ?>
<?php foreach($steps as $index => $step) {
  $sto = TStatusHelper::getLabel($step); 
  $date = date('d F Y H:i.s', $step->action_date);
  $documents = Document::findAll(['action_id' => $step->id]);
?>
<div class="d-flex p-3 border-bottom">
  <?= Html::img('uploads/profiles/thumb/'.$step->engineer->profile, ['class' => 'rounded-circle', 'loading' => 'lazy', 'alt'=>'profile', 'style' => 'width:50px;height:50px']) ?></span>
  <div class="d-flex w-100 ps-3">
    <div>
      <?= Html::beginTag('a', ['href' => Url::toRoute(['user/view/', 'id' => $step->engineer_id])]) ?>
      <?= Html::beginTag('h6', ['class' => 'text-body']) ?>
      <?= $step->engineer->full_name ?>
      <?= Html::tag('span', $step->engineer->email, ['class' => 'small text-muted font-weight-normal']) ?>
      <?= Html::tag('span', ' • ', ['class' => 'small text-muted font-weight-normal']) ?>
      <?= Html::tag('span', $date, ['class' => 'small text-muted font-weight-normal']) ?> 
      <?= Html::endTag('h6') ?> 
      <?= Html::endTag('a') ?>
      <?= Html::tag('h6', $step->action) ?>
      <?= Html::tag('p', $step->summary, ['style'=>'line-height: 1.2;']) ?>
      <?php if (isset($documents)) {
        $collid = Yii::$app->security->generateRandomString();
      ?> 
        <?= Html::button('Attachments', ['class'=>'btn btn-link text-decoration-none', 'data-bs-toggle' =>'collapse', 'data-bs-target' => '#'.$collid]) ?>
        <?= Html::beginTag('div', ['id'=>$collid, 'class'=>'collapse']) ?>
        a
        <?= Html::ul($documents, ['class' => 'list-unstyled d-flex justify-content-between mb-0 pe-xl-5',
              'item'=> function($item, $index){
                  return Html::tag('li', 
                  Html::a($doc->fileIcon.' '.$doc->uploadname, ['document/download/', 'id' => $doc->id], 
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
