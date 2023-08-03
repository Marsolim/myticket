<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\helpers\RoleHelper;
use common\models\User;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

if (Yii::$app->user->can('manageUser') && ($model->id != Yii::$app->user->id))
{
?>
    <?php Pjax::begin([
        'id'=>$id, 
        'class' => 'text-mute', 
        'clientOptions' => ['method' => 'POST'],
        'enablePushState' => false,
        'enableReplaceState' => false]); ?>
        <?= $togglelink ?>
        <ul class="dropdown-menu">
            <?php foreach($dropdownlabels as $role => $ddlabel) { ?>
            <li>
            <?= Html::a(
                RoleHelper::getLabel($role, true),
                ['assign-role', 'user' => $model->id, 'role' => $role],
                ['class' => 'dropdown-item pjax-link', 
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            'user' => $model->id,
                            'role' => $role,
                        ],
                    ], 
                    'data-pjax' => '#'.$id
                ]) ?>
            </li>
            <?php } ?>
        </ul>
    <?php Pjax::end(); ?>
<?php
}
else
{
?>
    <?= RoleHelper::getLabel($model->role, $showtext) ?>
<?php
}
?>
