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

?>
<div class="card shadow-0">
    <div class="card-body border-bottom pb-2">
        <div class="d-flex">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/img (31).webp" class="rounded-circle" height="50" alt="Avatar" loading="lazy" />
            <div class="d-flex align-items-center w-100 ps-3">
                <div class="w-100">
                    <input type="text" id="form143" class="form-control form-status border-0 py-1 px-0" placeholder="What's happening" />
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled d-flex flex-row ps-3 pt-3" style="margin-left: 50px;">
                <li>
                    <a href=""><i class="far fa-image pe-2"></i></a>
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
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary btn-rounded">Tweet</button>
            </div>
        </div>
    </div>
</div>