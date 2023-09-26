<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\docs\Document $model */
/** @var yii\widgets\ActiveForm $form */
?>
<?= Html::a($model->fileIcon.' '.$model->uploadname, 
    ['document/download/', 'id' => $model->id], 
    ['class' => 'btn btn-link text-decoration-none']) 
?>