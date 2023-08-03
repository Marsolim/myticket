<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\helpers\RoleHelper;
use frontend\helpers\TStatusHelper;
use common\models\User;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

$steps = $model->getModels();
$last = end($steps);
?>

<?php foreach($steps as $index => $step) {
  $sto = TStatusHelper::createStep($step); 
    $class = ["step"];
    $i = $index + 1;
    if ($index == (count($steps) - 1)) $class[] = 'step-active';
    else $class[] = 'step-done';
    if ($i < count($steps)) $i = '<i class="fa fa-check"></i>';
?>
<div class="<?= implode(' ', $class) ?>">
  <div>
    <div class="circle"><?= $i ?></div>
  </div>
  <div>
    <div class="title"><?= $sto['title'] ?></div>
    <div class="caption"><?= $sto['caption']['content'] ?></div>
  </div>
</div>
<?php } ?>

<div class="step">
  <div>
    <div class="circle"><?= count($steps) + 1 ?></div>
  </div>
  <div>
    <div class="title">Progress</div>
    <div class="caption">This is description of second step.</div>
  </div>
</div>
<div class="step">
  <div>
    <div class="circle">3</div>
  </div>
  <div>
    <div class="title">Resolved</div>
    <div class="caption">Some text about Third step. </div>
  </div>
</div>              
<div class="step">
  <div>
    <div class="circle">4</div>
  </div>
  <div>
    <div class="title">Closed</div>
  </div>
</div>