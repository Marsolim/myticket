<?php

use yii\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\helpers\RoleHelper;
use frontend\helpers\TStatusHelper;
use common\models\User;
use common\models\Document;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */
/** @var yii\widgets\ActiveForm $form */

//$steps = $model->getModels();
//ArrayHelper::multisort($steps, ['action_date'], [SORT_DESC]);
//$last = end($steps);
$addholiday = Url::toRoute('calendar/add-holiday');
$nonworkingday = Url::toRoute('calendar/nonworkingday');
$holiday = Url::toRoute('calendar/holiday');
$ticket = Url::toRoute('calendar/tickets');

$calendarinitjs = <<<JS
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.querySelector('#{$id}');
  console.log(calendarEl);
  var calendar = new FullCalendar.Calendar(calendarEl, {
      //initialView: 'dayGridMonth',
      timeZone: 'Asia/Jakarta',
      initialDate: new Date(),
      //editable: true,
      firstDay: 1, //  1(Monday) this can be changed to 0(Sunday) for the USA system
      selectable: true,
      //defaultView: 'month',
      headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    select: function(start, end, allDay) {
      var title = prompt('Event Title:');
      if (title) {
        var data = new FormData();
        data.append("json", JSON.stringify({'title': title,'start': start.start,'end': start.end}));
        fetch('{$addholiday}', {
          method: 'POST',
          headers: {
            'Accept':'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({'title': title,'start': start.start,'end': start.end}),
        }).then(response => {
          if (response.ok) {
            response.text().then(response => {
              calendar.getEventSourceById('HOLIDAY').refetch();
              calendar.render();
            });
          }
        });
      }
      calendar.unselect();
    },
    eventSources: [
      {
        url: '{$nonworkingday}',
        method: 'GET',
        failure: function() {
          alert('there was an error while fetching events!');
        },
        className:'fc-non-business',
        display:'background',
        color: 'yellow',   // a non-ajax option
        textColor: 'red' // a non-ajax option
      },
      {
        id:'HOLIDAY',
        url: '{$holiday}',
        method: 'GET',
        failure: function() {
          alert('there was an error while fetching events!');
        },
        className:'fc-non-business',
        display:'background',
        color: 'pink',   // a non-ajax option
        textColor: 'red' // a non-ajax option
      },
      {
        id:'TICKETS',
        url: '{$ticket}',
        method: 'GET',
        failure: function() {
          alert('there was an error while fetching events!');
        },
        className:'fc-business',
        //display:'background',
        color: 'blue',   // a non-ajax option
        textColor: 'black' // a non-ajax option
      }
    ]
  });
  calendar.render();
});
JS;
$this->registerJs($calendarinitjs, View::POS_END);
?>
<?= Html::beginTag('div',  ['id'=>'wrap']) ?>
    <?= Html::tag('div', '', ['id'=>$id, 'class'=>'calendar']) ?>
    <?= Html::tag('div', '', ['style'=>'clear:both']) ?>
<?= Html::endTag('div') ?>