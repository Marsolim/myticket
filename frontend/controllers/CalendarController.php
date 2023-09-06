<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Holiday;
use common\models\ticket\Ticket;
use frontend\helpers\CalendarHelper;

class CalendarController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup', 'upload_document'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['upload_document'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'upload-document' => ['post']
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
        {
            $this->redirect(['login']);
        }
        return $this->render('index');
    }

    public function actionNonworkingday($start, $end)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->asJSON(CalendarHelper::getWeekends($start, $end));
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actionAddHoliday()
    {
        //if (Yii::$app->request->isAjax) 
        //{
            $model = Yii::$app->request->getBodyParams();
            $holi = new Holiday();
            $holi->title = $model["title"];
            $holi->start = CalendarHelper::toTimeStamp($model["start"]);
            $holi->end = CalendarHelper::toTimeStamp($model["end"]);
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($holi->validate())
            {
                //return $this->asJSON(['holi' => $holi]);
                if ($holi->save())
                return $this->asJSON(['holi' => $holi]);
            }
            return $this->asJSON(['model' => $model,
            'post' => Yii::$app->request->post(),
            'body' => Yii::$app->request->getBodyParams()
        ]);
        //}
        //throw new NotFoundHttpException();
    }

    public function actionHoliday($start, $end)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tickets = Holiday::find()
            ->where(['>=', 'start', CalendarHelper::toTimeStamp($start)])
            ->andWhere(['<=', 'start', CalendarHelper::toTimeStamp($end)])
            ->all();
        $ticketevent = [];
        date_default_timezone_set("Asia/Jakarta");
        foreach($tickets as $key => $value)
        {
            $ticketevent[] = [
                'title' => $value->title,
                'start' => date(DATE_ATOM, $value->start),
                'end' => date(DATE_ATOM, $value->end),
                'allDay' => true
            ];
        }
        return $this->asJSON($ticketevent);
    }

    public function actionTickets($start, $end)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tickets = Ticket::find()
            ->where(['>=', 'created_at', CalendarHelper::toTimeStamp($start)])
            ->andWhere(['<=', 'created_at', CalendarHelper::toTimeStamp($end)])
            ->all();
        $ticketevent = [];
        date_default_timezone_set("Asia/Jakarta");
        foreach($tickets as $key => $value)
        {
            $ticketevent[] = [
                'title' => $value->problem,
                'start' => date(DATE_ATOM, $value->created_at),
                'end' => date(DATE_ATOM, $value->updated_at)
            ];
        }
        return $this->asJSON($ticketevent);
    }

    public function actionTicketActions($start, $end)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->asJSON(CalendarHelper::getWeekends($start, $end));
    }
}