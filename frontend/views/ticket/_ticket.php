<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\actors\Store;
use common\models\tickets\Ticket;
use frontend\helpers\TicketHelper;
use kartik\helpers\Enum;

/** @var yii\web\View $this */
/** @var common\models\tickets\Ticket $model */
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
                'href' => "#ts-$model->number-body",
                'aria-expanded' => $expanded ? 'true' : 'false',
                'aria-controls' => "ts-$model->number-body"
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
                        <div class="h6 position-relative text-primary">
                            <?= $this->render('_ticket_command', ['model' => $model]) ?>
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
                            <?= Html::tag('span', date('Y-m-d H:i.s', $model->created_at), ['class' => 'date date-at']) ?>
                        </small>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        </div>
        <?= $this->render('_ticket_body', ['model' => $model, 'expanded' => $expanded]) ?>
    </div>
<?= Html::endTag('div') ?>
