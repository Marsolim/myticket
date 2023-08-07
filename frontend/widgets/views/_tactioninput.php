<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\editors\Summernote;
use frontend\helpers\RoleHelper;
use frontend\helpers\TStatusHelper;
use common\models\User;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

$user = User::findOne(['id' => Yii::$app->user->id]);
date_default_timezone_set('Asia/Jakarta');
?>
<div class="card shadow-0">
    <div class="card-body border-bottom pb-2">
        <?php $form = ActiveForm::begin(['fieldConfig' => ['enableLabel'=>false]]); ?>
        <div class="d-flex">
            <?= Html::img('uploads/profiles/thumb/'.$user->profile, ['class' => 'rounded-circle', 'loading' => 'lazy', 'alt'=>'profile', 'style' => 'width:50px;height:50px']) ?>
            <div class="d-flex align-items-center w-100 ps-3">
                <div class="w-100">
                    <?= Html::beginTag('a', ['href' => Url::toRoute(['user/view/', 'id' => $user->id])]) ?>
                        <?= Html::beginTag('h6', ['class' => 'text-body']) ?>
                            <?= $user->full_name ?>
                            <?= Html::tag('span', $user->email, ['class' => 'small text-muted font-weight-normal']) ?>
                            <?= Html::tag('span', ' • ', ['class' => 'small text-muted font-weight-normal']) ?>
                            <?= Html::tag('span', date('d F Y H:i.s'), ['class' => 'small text-muted font-weight-normal']) ?> 
                        <?= Html::endTag('h6') ?> 
                    <?= Html::endTag('a') ?>
                    <input type="text" id="form143" class="form-control form-status border-0 py-1 px-0" placeholder="Short description" />
                    <?= $form->field($model, 'problem_description')->widget(Summernote::class, 
                        ['pluginOptions' => [
                            'height' => 50,
                            'dialogsFade' => true,
                            'toolbar' => [
                                ['style1', ['style']],
                                ['style2', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript']],
                                ['font', ['fontname', 'fontsize', 'color', 'clear']],
                                ['para', ['ul', 'ol', 'paragraph', 'height']],
                                ['insert', ['link', 'table', 'hr']],
                            ],
                            'fontSizes' => ['8', '9', '10', '11', '12', '13', '14', '16', '18', '20'],
                        ],]) ?>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled d-flex flex-row ps-3 pt-3" style="margin-left: 50px;">
                <li>
                    <a href="" class="btn btn-link text-decoration-none"><i class="fas fa-file-invoice pe-2"></i>Invoice</a>
                </li>
                <li>
                    <a href="" class="btn btn-link text-decoration-none"><i class="fas fa-file-lines px-2"></i>SPK/BAP</a>
                </li>
                <li>
                    <a href="" class="btn btn-link text-decoration-none"><i class="fas fa-file px-2"></i>Lain-lain</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <ul class="list-unstyled d-flex flex-row ps-3 pt-3" style="margin-left: 50px;">
                    <li>
                        <button type="button" class="btn btn-link"><i class="fa-solid fa-door-open fa-fw"></i></button>
                    </li>
                    <li>
                        <a href=""><i class="fas fa-photo-video px-2"></i></a>
                    </li>
                    <li>
                        <a href=""><i class="fas fa-chart-bar px-2"></i></a>
                    </li>
                    <li>
                        <a href=""><i class="far fa-smile px-2"></i></a>
                    </li>
                    <li>
                        <a href=""><i class="far fa-calendar-check px-2"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>