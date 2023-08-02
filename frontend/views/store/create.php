<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Store $model */

$this->title = 'Create Shop';
$this->params['breadcrumbs'][] = ['label' => 'Shops', 'url' => ['index']];
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
