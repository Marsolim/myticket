<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Holiday;
use common\models\Ticket;
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

    public function actionAddHoliday($start, $end, $title)
    {
        $model => new Holiday();
        $model->title = $title;
        $model->start = CalendarHelper::toTimeStamp($start);
        $model->end = CalendarHelper::toTimeStamp($end);
        $model->save();
    }

    public function actionHoliday($start, $end)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->asJSON(CalendarHelper::getWeekends($start, $end));
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
                'start' => date(DATE_ISO8601, $value->created_at),
                'end' => date(DATE_ISO8601, $value->updated_at)
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