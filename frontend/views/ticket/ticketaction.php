<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\editors\Summernote;
use kartik\icons\FontAwesomeAsset;
use kartik\datecontrol\DateControl;

/** @var yii\web\View $this */
/** @var common\models\TicketAction $model */
/** @var yii\widgets\ActiveForm $form */

FontAwesomeAsset::register($this);

$this->title = 'Laporan tindakan: ' . $ticket->number . ' - ' . $ticket->problem;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $ticket->number, 'url' => ['view', 'id' => $ticket->id]];
$this->params['breadcrumbs'][] = $command;
?>
<div class="ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="ticket-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'status_override')->dropDownList(ArrayHelper::map(
                $statuses,
                'id',
                'name'
            ), ['prompt' => '', 'disabled' => in_array($command, ['Open', 'Suspend'])]
        ) ?>

        <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'action_date')->widget(DateControl::classname(), [
            'type' => 'date',
            'ajaxConversion' => true,
            'autoWidget' => true,
            'widgetClass' => '',
            'displayFormat' => 'php:d-F-Y',
            'saveFormat' => 'php:U',
            'saveTimezone' => 'UTC',
            'displayTimezone' => 'Asia/Jakarta',
            //'saveOptions' => [
            //    'label' => 'Input saved as: ',
            //    'type' => 'text',
            //    'readonly' => true,
            //    'class' => 'hint-input text-muted'
            //],
            'widgetOptions' => [
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'php:d-F-Y'
                ]
            ]
        ]) ?>

        <?= $form->field($model, 'summary')->widget(Summernote::class, [
            'useKrajeePresets' => true,
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
