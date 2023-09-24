<?php

namespace frontend\controllers;

use common\models\actors\Engineer;
use Yii;
use common\models\tickets\Ticket;
use common\models\actors\User;
use common\models\actors\Store;
use common\models\tickets\actions\Action;
use common\models\tickets\actions\Assignment;
use common\models\tickets\actions\closings\NoProblem;
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
        //$ticketSearch->customer_id = Yii::$app->request->post('customer_id', null);
        //$query = $ticketSearch->searchQuery(Yii::$app->request->post());
        $user = User::findOne(['id' => Yii::$app->user->id]);
        if (!(ArrayHelper::isIn($user::class, [User::class, Engineer::class])))
        {
            throw new UnauthorizedHttpException();
        }
        if ($user::class === Engineer::class)
        {
            //$query->andWhere(['engineer_id' => Yii::$app->user->id]);
        }
        if (ArrayHelper::isIn($user::class, [StoreManager::class, GeneralManager::class]))
        {
            $stores = ArrayHelper::getColumn($user->stores, 'id');
            //$query->andWhere(['customer_id' => $stores]);
        }

        Url::remember();
        $articleDataProvider = $ticketSearch->search(Yii::$app->request->post());
        $options = [
            'articleSearch' => $ticketSearch,
            'articleDataProvider' => $articleDataProvider
        ];
        if (Yii::$app->request->isAjax) 
            return $this->renderAjax('list', $options);
        else
            return $this->render('list', $options);
    }

    public function actionDiscretion($ticket)
    {
        $ticket = Ticket::findOne(['id' => $ticket]);
        $model = $ticket->discretion;
        if (empty($model)){
            $model = new Discretion();
            $model->ticket_id = $ticket->id;
        } 
        //$model = empty($ticket->discretion) ? new Discretion(['ticket_id' => $ticket->id]) : $ticket->discretion;
        $model->user_id = Yii::$app->user->id;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate() && $model->save(false)) {
                    $transaction->commit();                      
                    return Json::encode(['target' => "#ts-$ticket->number", 'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode'=>'list-item'])]);
                }
                $transaction->rollBack();
                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Discretion not created.'));
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Discretion not created.'));
            }
        }
    
        return $this->renderAjax('_action_discretion', ['model' => $model,]);
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
                if ($model->validate() && $model->save(false)) {
                    $transaction->commit();
                    return Json::encode(['target' => "#ts-$ticket->number", 'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode'=>'list-item'])]);
                }
                $transaction->rollBack();
                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Recommendation not created.'));
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => 'Recommendation not created.'));
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
        $model = new Repair();
        $model->ticket_id = $ticket;
        $ticket = Ticket::findOne(['id' => $ticket]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate() && $model->save(false)) {
                        $transaction->commit();                      
                        return Json::encode([
                            'target' => "#ts-$ticket->number",
                            'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                        ]);
                }
                $transaction->rollBack();
                return Json::encode(array('status' => 'error', 'type' => 'error', 'message' => 'Repair not created.'));
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'error', 'type' => 'error', 'message' => 'Repair not created.'));
            }
        }
        return $this->renderAjax('_action_repair', ['model' => $model,]);
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
    
    public function actionAssignment($ticket) {
        $model = new Assignment();
        $model->ticket_id = $ticket;
        $ticket = Ticket::findOne(['id' => $ticket]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate() && $model->save(false)) {
                        $transaction->commit();                      
                        return Json::encode([
                            'target' => "#ts-$ticket->number",
                            'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                        ]);
                }
                $transaction->rollBack();
                return Json::encode(array('status' => 'error', 'type' => 'error', 'message' => 'Repair not created.'));
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'error', 'type' => 'error', 'message' => 'Repair not created.'));
            }
        }
        return $this->renderAjax('_action_assignment', ['model' => $model,]);
    }
    
    public function actionAssignmentValidate() {
        $model = new Assignment();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            //$model->user_id = Yii::$app->user->id;
            $model->created_at = time();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionWaiting($ticket) {

    }

    public function actionUploadInvoice($ticket){

    }

    /**
     * Displays a single Ticket model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $mode)
    {
        Url::remember();
        if (Yii::$app->request->isAjax && $mode === "list-item")
            return $this->renderAjax('_ticket', ['model' => $this->findModel($id), 'expanded' => true]);
        else $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCloseNoProblem($ticket)
    {
        $model = new NoProblem();
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

        if (Yii::$app->request->isAjax) {
            $model->issuer_id = Yii::$app->user->id;

            if ($model->load($this->request->post()) && $model->save())
            {
                //$model->notify();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->renderAjax('create', [
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
