<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\UserProfile $model */

$this->title = 'Update User Profile: ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
\yii\web\YiiAsset::register($this);
?>
<div class="user-profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
