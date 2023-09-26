<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\actors\Store $model */

$this->title = 'Create Store';
$this->params['breadcrumbs'][] = ['label' => 'Stores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions,
        'status' => $status,
    ]) ?>

</div>
