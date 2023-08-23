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
    //selectHelper: true,
      select: function(start, end, allDay) {
        var title = prompt('Event Title:');
        if (title) {
var data = new FormData();
data.append( "json", JSON.stringify( {
            'title': title,
            'start': start.start,
            'end': start.end
} ) );

fetch("<?= Url::toRoute("calendar/add-holiday") ?>", {
  method: 'POST',
  headers: {
    'Accept':'application/json',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify( {
            'title': title,
            'start': start.start,
            'end': start.end
} ),
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
  id:'HOLIDAY',
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
  id:'TICKETS',
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