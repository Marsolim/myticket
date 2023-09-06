<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\helpers\RoleHelper;
use common\models\actors\User;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php Pjax::begin([
        'id'=>$id, 
        'class' => 'text-mute', 
        'clientOptions' => ['method' => 'POST'],
        'enablePushState' => false,
        'enableReplaceState' => false]); ?>
    <?= Html::a(RoleHelper::getLabel(User::getUserRoleName($model->id)), ['#'], ['class' => 'dropdown-toggle', 'data-bs-toggle' => 'dropdown']) ?>
    <ul class="dropdown-menu">
        <?php foreach(RoleHelper::getRoleLabelOptions() as $role => $ddlabel) { ?>
        <li>
        <?= Html::a(
            $ddlabel,
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