<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
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
<div id="<?= $id ?>">
<?php foreach($steps as $index => $step) {
  $sto = TStatusHelper::getLabel($step); 
  $date = date('d F Y H:i.s', $step->action_date);
  $documents = Document::findAll(['action_id' => $step->id]);
?>
<div class="d-flex p-3 border-bottom">
  <?= Html::img('uploads/profiles/thumb/'.$step->engineer->profile, ['class' => 'rounded-circle', 'loading' => 'lazy', 'alt'=>'profile', 'width' => '50','height'=>'50']) ?></span>
  <div class="d-flex w-100 ps-3">
    <div>
      <?= Html::a("
      <h6 class='text-body'>
      {$step->engineer->full_name}
        <span class='small text-muted font-weight-normal'>{$step->engineer->email}</span>
        <span class='small text-muted font-weight-normal'> • </span>
        <span class='small text-muted font-weight-normal'>{$date}</span>
      </h6>
      ", ['user/view/', 'id' => $step->engineer_id]) ?>
      <h5><?= $step->action ?></h5>
      <p style="line-height: 1.2;">
        <?= $step->summary ?>
      </p>
      <ul class="list-unstyled d-flex justify-content-between mb-0 pe-xl-5">
        <?php if (isset($documents)) { ?> 
        <?php foreach($documents as $doc) { ?> 
        <?= Html::tag('li', Html::a($doc->fileIcon.' '.$doc->uploadname, ['document/download/', 'id' => $doc->id], ['class' => 'btn btn-link text-decoration-none'])) ?>
        <?php } ?>
        <?php } ?>
      </ul>
      <ul class="list-unstyled d-flex justify-content-between mb-0 pe-xl-5">
        <li><?= $sto ?></li>
      </ul>
    </div>
  </div>
</div>
<?php } ?>
