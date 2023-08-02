<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>
        <span>Anda telah ditunjuk sebagai teknisi untuk menangani servis :</span><br/>
        <span>Nomor : <?= $ticket->number ?></span><br/>
        <span>Toko : <?= nl2br($ticket->storeDetail) ?><br/>
        Kendala : <?= $ticket->problem ?><br/>
    </p>
    <p>Rincian :</p>
    <p>
        <?= $ticket->problem_description ?>
    </p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
