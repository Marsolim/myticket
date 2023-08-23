<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = 'Create User Profile';
$this->params['breadcrumbs'][] = ['label' => 'User Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
