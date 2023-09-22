<?php

namespace frontend\controllers;

use common\models\actors\Engineer;
use Yii;
use common\models\tickets\Ticket;
use common\models\actors\User;
use common\models\actors\Store;
use common\models\tickets\actions\Action;
use common\models\tickets\actions\Repair;
use common\models\tickets\actions\Discretion;
use common\models\tickets\actions\Recommendation;
use Exception;
use frontend\models\search\ActionSearch;
use frontend\models\search\TicketSearch;
use frontend\models\forms\DocumentUploadForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use frontend\helpers\UserHelper;
use frontend\models\GeneralManager;
use frontend\models\search\RepairActionSearch;
use frontend\models\StoreManager;
use mdm\autonumber\AutoNumber;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'assign-engineer' => ['POST'],
                        //'close' => ['POST'],
                    ],
                ],
            ]
        );
    }

    // /**
    //  * Lists all Ticket models.
    //  *
    //  * @return string
    //  */
    // public function actionIndex()
    // {
    //     $searchModel = new TicketSearch();
    //     $query = $searchModel->searchQuery($this->request->queryParams);
    //     if (!(UserHelper::isAdministrator() || UserHelper::isEngineer() || UserHelper::isGeneralManager()))
    //     {
    //         throw new UnauthorizedHttpException();
    //     }
    //     if (UserHelper::isEngineer())
    //     {
    //         $query->andWhere(['engineer_id' => Yii::$app->user->id]);
    //     }
    //     if (UserHelper::isGeneralManager())
    //     {
    //         $user = User::findOne(['id' => Yii::$app->user->id]);
    //         $stores = Store::findAll(['region_id' => $user->region_id]);
    //         $stores = ArrayHelper::getColumn($stores, 'id');
    //         $query->andWhere(['store_id' => $stores]);
    //     }

    //     $dataProvider = $searchModel->search($query);

    //     Url::remember();

    //     return $this->render('index', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //     ]);
    // }

    public function actionIndex()
    {
        $ticketSearch = new TicketSearch();
        $ticketSearch->customer_id = Yii::$app->request->post('customer_id', null);
        $query = $ticketSearch->searchQuery(Yii::$app->request->post());
        $user = User::findOne(['id' => Yii::$app->user->id]);
        if (!(ArrayHelper::isIn($user::class, [User::class, Engineer::class])))
        {
            throw new UnauthorizedHttpException();
        }
        if ($user::class === Engineer::class)
        {
            $query->andWhere(['engineer_id' => Yii::$app->user->id]);
        }
        if (ArrayHelper::isIn($user::class, [StoreManager::class, GeneralManager::class]))
        {
            $stores = ArrayHelper::getColumn($user->stores, 'id');
            $query->andWhere(['customer_id' => $stores]);
        }

        Url::remember();
        $articleDataProvider = $ticketSearch->search($query);

        return $this->render('list', ['articleSearch' => $ticketSearch ,
                                        'articleDataProvider' => $articleDataProvider ]);
    }

    public function actionDiscretion($ticket)
    {
        $model = new Discretion();
        $model->ticket_id = $ticket;
        $model->user_id = Yii::$app->user->id;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate()) {
                    $flag = $model->save(false);
                    if ($flag == true) {
                        $transaction->commit();                      
                        return Json::encode(array('status' => 'success', 'type' => 'success', 'message' => 'Contact created successfully.'));
                    } else {
                        $transaction->rollBack();
                    }
                } else {
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Contact can not created.'));
                }
            } catch (Exception $ex) {
                $transaction->rollBack();
            }
        }
    
        return $this->renderAjax('_action_discretion', [
                    'model' => $model,
            ]);
    }

    public function actionDiscretionValidate() {
        $model = new Discretion();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->user->id;
            $model->created_at = time();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionVisit($ticket)
    {
        $model = new Recommendation();
        $model->ticket_id = $ticket;
        $model->user_id = Yii::$app->user->id;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate()) {
                    $flag = $model->save(false);
                    if ($flag == true) {
                        $transaction->commit();
                        return Json::encode(array('status' => 'success', 'type' => 'success', 'message' => 'Recommendation created successfully.'));
                    } else {
                        $transaction->rollBack();
                        return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Model error.'));
                    }
                } else {
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
                }
            } catch (Exception $ex) {
                $transaction->rollBack();

                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
            }
        }
        return $this->renderAjax('_action_visit', ['model' => $model,]);
    }

    public function actionVisitValidate() {
        $model = new Recommendation();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            //$model->user_id = Yii::$app->user->id;
            $model->created_at = time();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionRepair($ticket)
    {
        $searchModel = new RepairActionSearch();
        $searchModel->ticket_id = $ticket;
        $query = $searchModel->searchQuery(Yii::$app->request->getQueryParams());
        $query->andWhere(['ticket_id' => $ticket]);
        $dataProvider = $searchModel->search($query);
        $models = $dataProvider->getModels();
        if (Yii::$app->request->isAjax && Model::loadMultiple($models, Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if (Model::validateMultiple($models))
                {
                    $flag = false;
                    foreach ($models as $index => $model) {
                        // populate and save records for each model
                        $flag &= $model->save(false);
                    }
                    if ($flag == true) {
                        $transaction->commit();                      
                        return Json::encode(array('status' => 'success', 'type' => 'success', 'message' => 'Contact created successfully.'));
                    } else {
                        $transaction->rollBack();
                        return Json::encode(array('status' => 'success', 'type' => 'success', 'message' => 'Contact created successfully.'));
                    }
                }
                else
                {
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Contact can not created.'));
                }
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'success', 'type' => 'success', 'message' => 'Contact created successfully.'));
            }
        }
    
        return $this->renderAjax('_action_repair', [
                    'dataProvider' => $dataProvider,
            ]);
    }

    public function actionRepairValidate() {
        $model = new Repair();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            //$model->user_id = Yii::$app->user->id;
            $model->created_at = time();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Displays a single Ticket model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new ActionSearch();
        $query = $searchModel->searchQuery(['ticket_id' => $id]);
        $query->where(['ticket_id' => $id]);
        $dataProvider = $searchModel->search($query);
        
        Url::remember();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCloseNoProblem($ticket)
    {
        $model = new Recommendation();
        if (Yii::$app->request->isAjax)
        {
            $model->ticket_id = $ticket;
        $model->user_id = Yii::$app->user->id;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate()) {
                    $flag = $model->save(false);
                    if ($flag == true) {
                        $transaction->commit();
                        return Json::encode(array('status' => 'success', 'type' => 'success', 'message' => 'Recommendation created successfully.'));
                    } else {
                        $transaction->rollBack();
                        return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Model error.'));
                    }
                } else {
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
                }
            } catch (Exception $ex) {
                $transaction->rollBack();

                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
            }
        }
        return $this->renderAjax('_action_visit', ['model' => $model,]);
    }
    throw new NotFoundHttpException("Should not try to call this.");
    
    }

    public function actionCloseNormal($ticket)
    {

    }

    public function actionCloseWaiting($ticket)
    {

    }

    public function actionCloseDuplicate($ticket)
    {

    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Ticket();
        
        $engineers = UserHelper::findEngineers()->all();
        $stores = Store::find();

        $model->number = AutoNumber::generate('TS.{Y.m}.????');

        if (UserHelper::isStoreManager())
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $stores->andWhere(['region_id' => $user->region_id]);
        }

        if ($this->request->isPost) {
            $model->issuer_id = Yii::$app->user->id;

            if ($model->load($this->request->post()) && $model->save())
            {
                //$model->notify();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'stores' => $stores->all(),
            'engineers' => $engineers
        ]);
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $engineers = User::find()->all();
        $stores = Store::find()->all();

        if ($this->request->isPost && $model->load($this->request->post()))
        {
            if ($model->save()) 
            {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'engineers' => $engineers,
            'stores' => $stores
        ]);
    }

    public function actionNotify($id)
    {
        $model = Action::findOne(['id' => $id]);

        $model->notify();

        return $this->redirect(Url::previous());
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
