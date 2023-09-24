<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var frontend\models\search\TicketSearch $model */
/** @var yii\widgets\ActiveForm $form */

$css = <<<CSS
  .text-toggle[aria-expanded=false] .text-expanded {
    display: none;
  }
  .text-toggle[aria-expanded=true] .text-collapsed {
    display: none;
  }
  .ticket.ticket-title:before {
  	content:" - "
  }
  .ticket.ticket-aho:before {
  	content:" | "
  }
  .store.store-name:before {
    content:" - "
  }
  .date.date-at:before {
  	content:" at "
  }
  .ticket-view .ticket-action-toolbar {
  	display:none
  }
  .ticket-view:hover .ticket-action-toolbar {
  	display:flex
  }
  .hover-hook~.show-on-hover {
  	display:none
  }
  .hover-hook:hover~.show-on-hover {
  	display:inline-block
  }
  .hover-hook:hover {
  	background-color:blue!important
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-primary-rgb), var(--bs-bg-opacity)) !important;
  }
  .hover-hook:hover~.show-on-hover {
  	display:inline-block
  }
  .hover-hook~.show-on-hover:hover {
  	display:inline-block
  }
CSS;

$this->registerCss($css);
if (empty($expanded)) $expanded = false;
?>

<?= Html::beginTag('div', ['class' => 'ticket-view text-toggle', 'id'=>"ts-$model->number"]) ?>
    <div class="ticket-view card position-relative text-primary mt-3 mb-2">
    	<div class="card-header">
        <?= Html::beginTag('div', ['class' => 'd-flex justify-content-between p-2',]) ?>
            <div class="d-flex flex-row align-items-center">
            <?= Html::tag('div', '<i class="text-warning fa fa-3x fa-ticket"></i>', [
                'class' => 'd-flex flex-column ml-2 me-2 text-toggle',
                'data-bs-toggle'=> 'collapse',
                'href' => "#ts-.$model->number.-body",
                'aria-expanded' => $expanded ? 'true' : 'false',
                'aria-controls' => "ts-.$model->number.-body"
            ]) ?>
                <div class="vr"></div>
                <div class="d-flex flex-column ms-2 ml-2">
                        <div class="h6 position-relative">
                            <span class="h6">
                            	<?= $this->render('_ticket_header', ['model' => $model])?>
                                <?= $this->render('_ticket_status', ['model' => TicketHelper::getStatuses($model)]) ?>
                            </span>
                        </div>
                        <div class="h6 position-relative text-primary">
                            <?= $this->render('_ticket_store_info', ['model' => $model->store]) ?>
                        </div>
                </div>
            </div>
            <div class="d-flex flex-row mt-1">
                <div class="d-flex flex-column align-items-end ml-2">
                        <small class="mr-2 text-align-right">
                            <span>Last update by</span> 
                            <?= Html::beginTag('span') ?>
                            <?= Html::a($model->issuer->full_name, ['user/view', 'id' => $model->issuer->id]) ?>
                            <?= Html::endTag('span') ?>
                            <?= Html::tag('span', Enum::timeElapsed(date(DATE_ATOM, empty($model->lastAction) ? $model->updated_at : $model->lastAction->created_at))) ?>
                        </small>
                        <small class="mr-2 text-align-right">
                            <span>Created by</span> 
                            <?= Html::beginTag('span') ?>
                            <?= Html::a($model->issuer->full_name, ['user/view', 'id' => $model->issuer->id]) ?>
                            <?= Html::endTag('span') ?>
                            <?= Html::tag('span', date('Y F d H:m.s', $model->created_at), ['class' => 'date date-at']) ?>
                        </small>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        </div>
        <?= $this->render('_ticket_body', ['model' => $model, 'expanded' => $expanded]) ?>
    </div>
<?= Html::endTag('div') ?>
<div class="visually-hidden">
<div class="container px-0 py-0 mx-0 mt-0 mb-2">
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col">
            <div class="card border-warning">
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
                            <p><?= 'Rekomendasi pekerjaan : '. (empty($model->visits) ? '' : implode("\n", ArrayHelper::getColumn($model->visits, 'summary'))) ?></p>
                            <p><?= 'Alasan tidak tercover MC : '. (empty($model->discretion)? '' : $model->discretion->summary) ?></p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-row align-items-center">
                            <small class="text-mute position-relative"><i class="fa fa-store"></i> <?= $model->store->code.'-'.$model->store->name ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark">
                                <?= Html::a($model->store->getTickets()->count(), ['/ticket/index'],
                                    [ 
                                        'data-method' => 'POST',
                                        'data-params' => ['customer_id' => $model->customer_id], 
                                    ]) ?>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            </small>
                        </div>
                        <div class="d-flex flex-row align-items-center">
                            <small class="text-primary"></small>
                        </div>
                        <div class="d-flex flex-row align-items-center">
                            <?= Html::img('uploads/profiles/thumb/'.$model->issuer->profile, ['class' => 'rounded-circle', 'width' => 15, 'height' => 15]) ?>
                            <div class="d-flex flex-column ms-1 ml-2">
                                <small class="font-weight-bold"><?= $model->issuer->full_name ?></small>
                            </div>
                        </div>
                    </div>
                    <hr class="bg-warning border-2 border-top border-warning" style="opacity:0.5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-row icons d-flex align-items-center">
                            <?= empty($model->discretion) ? Html::a('<i class="fa fa-thumbs-down text-warning"></i>', ['ticket/discretion', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Tidak tercover MC"
                            ]) : '' ?>
                            <?= Html::a('<i class="fa fa-handshake"></i>', ['ticket/visit', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Rekomendasi pengerjaan"
                            ]) ?>
                            <?= Html::a('<i class="fa fa-users-gear"></i>', ['ticket/assignment', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Teknisi"
                            ]) ?>
                            <?= Html::a('<i class="fa fa-screwdriver-wrench"></i>', ['ticket/repair', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Pekerjaan"
                            ]) ?>
                            <?= Html::a('<i class="fa fa-hourglass-half text-primary"></i>', ['ticket/close', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Selesai, menunggu IT"
                            ]) ?>
                            <?= Html::a('<i class="fa fa-circle-check text-success"></i>', ['ticket/close', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Selesai"
                            ]) ?>
                            <?= Html::a('<i class="fa fa-bug-slash text-danger"></i>', ['ticket/close', 'ticket' => $model->id], [
                                'class'=>"btn btn-link quick-action",
                                'title'=>"Double AHO"
                            ]) ?>
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

<div class="visually-hidden card position-relative text-primary mt-3 mb-2">
    	<div class="card-header">
        	<div class="d-flex justify-content-between p-2">
            	<div class="d-flex flex-row align-items-center">
                	<div class="d-flex flex-column ml-2">
                        <i class="text-warning fa fa-3x fa-ticket"></i>
                    </div>
                    <div class="d-flex flex-column ms-2 ml-2">
                        <div class="h6 position-relative">
                        <span class="h6 position-relative"><span class="ticket ticket-number">TS0001</span><span class="ticket ticket-title">Pindah titik kamera 6</span>
                        <span class="small badge rounded-pill bg-primary text-light">
                                <a class="text-decoration-none text-light" title="Status ticket" href="#" data-method="POST">Belum dikunjungi</a>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </span>
                        </div>
                        <div class="h6 position-relative">
                            <span class="h6 position-relative rounded-pill">AMD001-MIDI JAKARTA BARAT
                            <span class="small badge rounded-pill bg-success text-light">
                                <a class="text-decoration-none text-light" title="Jumlah ticket" href="#" data-method="POST">12</a>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row mt-1">
                    <div class="d-flex flex-column align-items-end ml-2">
                        <small class="mr-2 text-align-right">Last update 20 days ago</small>
                        <small class="mr-2 text-align-right">20-08-2023 12:50:24</small>
                    </div>
                </div>
        	</div>
            <div class="btn-group ms-2 small align-items-end position-absolute top-0 end-0 translate-middle-y">
            	<button class="btn btn-warning disabled" title="Tidak dicover MC"><i class="fa fa-thumbs-down"></i></button>
                <button class="btn btn-primary" title="Rekomendasi"><i class="fa fa-handshake"></i></button>
                <button class="btn btn-primary" title="Teknisi"><i class="fa fa-users-gear"></i></button>
                <button class="btn btn-primary" title="Pekerjaan"><i class="fa fa-screwdriver-wrench"></i></button>
                <button class="btn btn-primary" title="Selesai menunggu IT"><i class="fa fa-hourglass"></i></button>
                <button class="btn btn-success" title="Selesai"><i class="fa fa-circle-check"></i></button>
                <button class="btn btn-danger" title="Double AHO"><i class="fa fa-bugs"></i></button>
                <button class="btn btn-primary text-toggle" data-bs-toggle="collapse" href="#ts-ts0001-body" aria-expanded="false" aria-controls="ts-ts0001-body"><i class="text-collapsed fas fa-caret-down"></i><i class="text-expanded fas fa-caret-up"></i></button>
        	</div>
    	</div>
        <div class="card-body collapse" id="ts-ts0001-body">
        	<div class="text-justify">
            	<ul class="list-group list-group-flush">
                	<li class="list-group-item">
                    	<h6 class="card-title">Rekomendasi</h6>
                        <p class="card-text">Sebaiknya dilakukan dulu pengecekan area apakah aman untuk pemasangan kamera.</p>
                    </li>
                    <li class="list-group-item">
                    	<h6>Tidak tercover MC</h6>
                        <p>MC sudah expired sejak 2 hari sebelum pembuatan tiket servis.</p>
                    </li>
                    <li class="list-group-item">
                    	<h6>Daftar Dokumen</h6>
                        <a href="#">Invoice</a>
                        <a href="#">SPK</a>
                    </li>
                    <li class="list-group-item">
                    	<h6>Teknisi</h6>
                        <a href="#">Bambang</a>,
                        <a href="#">Herianto</a>
                    </li>
                    <li class="list-group-item">
                    	<h6>Pekerjaan</h6>
                        <div class="row small"><div class="col-2">20-08-2023 12:52.22</div><div class="col-4">Pemeriksaan lapangan</div></div>
                        <div class="row small"><div class="col-2">21-08-2023 12:52.22</div><div class="col-4">Ganti kamera</div><div class="col-4">Kamera HIKVISION</div><div class="col-2">123456789</div></div>
                    </li>
            	</ul>
        	</div>
        </div>
    </div>