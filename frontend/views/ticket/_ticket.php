<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>
<div class="ticket-view">
<div class="container mt-2 mb-2">
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col">
            <div class="card">
                <div class="d-flex justify-content-between p-2 px-3">
                    <div class="d-flex flex-row align-items-center">
                        <div class="d-flex flex-column ml-2">
                            <span class="h5 text-primary"><i class="fa fa-ticket"></i> <?= $model->number.' - '.$model->problem.(empty($model->external_number) ? '' : ' | '.$model->external_number) ?></span> 
                        </div>
                    </div>
                    <div class="d-flex flex-row mt-1 ellipsis">
                        <div class="d-flex flex-column align-items-end ml-2">
                            <small class="mr-2 text-align-right"><?= Enum::timeElapsed(date(DATE_ATOM, $model->created_at)) ?></small>
                            <small class="mr-2 text-align-right"><?= date('Y F d H:m:s', $model->created_at) ?></small>
                        </div>
                    </div>
                </div> 
                <div class="p-2 px-3">
                    <div class="text-justify">
                        <div>
                            <p></p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-row align-items-center">
                            <small class="text-mute position-relative"><i class="fa fa-store"></i> <?= $model->store->code.'-'.$model->store->name ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark">
                                99+
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            </small> 
                        </div>
                        <div class="d-flex flex-row align-items-center">
                            <?= Html::img('uploads/profiles/thumb/'.$model->issuer->profile, ['class' => 'rounded-circle', 'width' => 15, 'height' => 15]) ?>
                            <div class="d-flex flex-column ms-1 ml-2">
                                <small class="font-weight-bold"><?= $model->issuer->full_name ?></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-row icons d-flex align-items-center">
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Tidak tercover MC"><i class="fa fa-thumbs-down text-warning"></i></a>
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Rekomendasi pengerjaan"><i class="fa fa-handshake"></i></a>
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Teknisi"><i class="fa fa-users-gear"></i></a>
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Pekerjaan"><i class="fa fa-screwdriver-wrench"></i></a>
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Selesai, menunggu IT"><i class="fa fa-hourglass-half text-primary"></i></a>
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Selesai"><i class="fa fa-circle-check text-success"></i></a>
                            <a class="btn btn-link" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Double AHO"><i class="fa fa-bug-slash text-danger"></i></a>
                        </div>
                        <div class="d-flex flex-row muted-color">
                            <a class="btn btn-primary position-relative" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                Actions
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $model->getActions()->count() ?> 
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div id="collapseExample" class="collapse">
                    <hr>
                    <div class="comments">
                        <div class="d-flex flex-row mb-2"> <img src="https://i.imgur.com/9AZ2QX1.jpg" width="40" height="40" class="rounded-image rounded-circle mt-2">
                            <div class="d-flex flex-column ms-2 ml-2"> <span class="name"><strong>Daniel Frozer</strong></span> <small class="comment-text">I like this alot! thanks alot</small>
                                <div class="d-flex flex-row align-items-center status"> <small>Like</small> <small>Reply</small> <small>Translate</small> <small>18 mins</small> </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row mb-2"> <img src="https://i.imgur.com/1YrCKa1.jpg" width="40" class="rounded-image">
                            <div class="d-flex flex-column ml-2"> <span class="name">Elizabeth goodmen</span> <small class="comment-text">Thanks for sharing!</small>
                                <div class="d-flex flex-row align-items-center status"> <small>Like</small> <small>Reply</small> <small>Translate</small> <small>8 mins</small> </div>
                            </div>
                        </div>
                        <div class="comment-input"> <input type="text" class="form-control">
                            <div class="fonts"> <i class="fa fa-camera"></i> </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</div>