<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
?>
<?= Html::beginTag('div',  ['id'=>'wrap']) ?>
    <?= Html::tag('div', '', ['id'=>$id, 'class'=>'calendar']) ?>
    <?= Html::tag('div', '', ['style'=>'clear:both']) ?>
<?= Html::endTag('div') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.querySelector('#<?= $id ?>');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    //initialView: 'dayGridMonth',
    initialDate: '2023-07-07',
    //editable: true,
      firstDay: 1, //  1(Monday) this can be changed to 0(Sunday) for the USA system
      selectable: true,
      //defaultView: 'month',
      headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    //selectHelper: true,
      select: function(start, end, allDay) {
        var title = prompt('Event Title:');
        if (title) {
          calendar.renderEvent({
              title: title,
              start: start,
              end: end,
              allDay: allDay
            },
            true // make the event "stick"
          );
        }
        calendar.unselect();
      },
      eventSources: [

// your event source
{
  url: '<?= Url::toRoute('calendar/nonworkingday') ?>',
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
  url: '<?= Url::toRoute('calendar/holiday') ?>',
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
  url: '<?= Url::toRoute('calendar/tickets') ?>',
  method: 'GET',
  failure: function() {
    alert('there was an error while fetching events!');
  },
  className:'fc-business',
  //display:'background',
  color: 'blue',   // a non-ajax option
  textColor: 'black' // a non-ajax option
}
// any other sources...

]
    
  });

  calendar.render();
});
</script>