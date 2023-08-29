<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

$stores = Store::find()
    ->join('LEFT JOIN', ['u'=>'user'], ['isnull(u.region_id, store.region_id) = store.region_id'])
    ->where('u.id' => Yii::$app->user->id);
;

?>

<div class="ticket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'store_id')->dropDownList(ArrayHelper::map(
            $stores,
            'id',
            'name'
        ), ['prompt' => '']
    ) ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'problem_description') ?>

    <?= $form->field($model, 'engineer_id') ?>

    <?= $form->field($model, 'issuer_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
