<?php

namespace frontend\controllers;

use Yii;
use common\models\actors\Administrator;
use common\models\actors\Engineer;
use common\models\actors\User;
use common\models\tickets\Ticket;
use common\models\actors\Store;
use common\models\docs\Inquiry;
use common\models\docs\Invoice;
use common\models\docs\WorkOrder;
use common\models\tickets\actions\Action;
use common\models\tickets\actions\Assignment;
use common\models\tickets\actions\closings\Awaiting;
use common\models\tickets\actions\closings\Duplicate;
use common\models\tickets\actions\closings\NoProblem;
use common\models\tickets\actions\closings\Normal;
use common\models\tickets\actions\Repair;
use common\models\tickets\actions\Discretion;
use common\models\tickets\actions\Open;
use common\models\tickets\actions\Recommendation;
use Exception;
use frontend\models\search\TicketSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use frontend\models\GeneralManager;
use frontend\models\StoreManager;
use mdm\autonumber\AutoNumber;
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

    public function actionIndex()
    {
        $ticketSearch = new TicketSearch();
        //$ticketSearch->customer_id = Yii::$app->request->post('customer_id', null);
        //$query = $ticketSearch->searchQuery(Yii::$app->request->post());
        $user = User::findOne(['id' => Yii::$app->user->id]);
        if (!($user instanceof Administrator || $user instanceof Engineer))
        {
            throw new UnauthorizedHttpException();
        }
        if ($user instanceof Engineer)
        {
            //$query->andWhere(['engineer_id' => Yii::$app->user->id]);
        }
        if ($user instanceof StoreManager || $user instanceof GeneralManager)
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
                    return Json::encode([
                        'pjax_refresh' => false,
                        'target' => "#ts-$ticket->number",
                        'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode'=>'list-item'])
                    ]);
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
                    return Json::encode([
                        'pjax_refresh' => false,
                        'target' => "#ts-$ticket->number",
                        'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode'=>'list-item'])
                    ]);
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
                    $ticket->updateAttributes(['last_action_id' => $model->getPrimaryKey()]);
                    $transaction->commit();
                    return Json::encode([
                        'pjax_refresh' => false,
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
                        'pjax_refresh' => false,
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
        $model = new Invoice();
        $model->ticket_id = $ticket;
        $model->owner_id = Yii::$app->user->id;
        $ticket = Ticket::findOne(['id' => $ticket]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                $model->file = UploadedFile::getInstanceByName('file');
                $model->store_id = $ticket->customer_id;
                if ($model->validate() && $model->upload(false)) {
                    $transaction->commit();                      
                    return Json::encode([
                        'pjax_refresh' => false,
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
        return $this->renderAjax('_action_upload_document', ['model' => $model,]);
    }

    public function actionUploadInquiry($ticket){
        $model = new Inquiry();
        $model->ticket_id = $ticket;
        $model->owner_id = Yii::$app->user->id;
        $ticket = Ticket::findOne(['id' => $ticket]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                $model->file = UploadedFile::getInstanceByName('file');
                $model->store_id = $ticket->customer_id;
                if ($model->validate() && $model->upload(false)) {
                    $transaction->commit();                      
                    return Json::encode([
                        'pjax_refresh' => false,
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
        return $this->renderAjax('_action_upload_document', ['model' => $model,]);
    }

    public function actionUploadWorkOrder($ticket){
        $model = new WorkOrder();
        $model->ticket_id = $ticket;
        $model->owner_id = Yii::$app->user->id;
        $ticket = Ticket::findOne(['id' => $ticket]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                $model->file = UploadedFile::getInstance($model, 'file');
                $model->store_id = $ticket->customer_id;
                if ($model->validate() && $model->upload(false)) {
                    $transaction->commit();                      
                    return Json::encode([
                        'pjax_refresh' => false,
                        'target' => "#ts-$ticket->number",
                        'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                    ]);
                }
                $transaction->rollBack();
                return Json::encode(array('status' => 'error', 'type' => 'error', 'message' => Json::encode($model->getErrors())));
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'error', 'type' => 'error', 'message' => $ex->getMessage()));
            }
        }
        return $this->renderAjax('_action_upload_document', ['model' => $model,]);
    }

    public function actionDocumentValidate() {
        $model = new Invoice();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->owner_id = Yii::$app->user->id;
            //$model->ticket_id = $ticket;
            //$model->created_at = time();
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
            $ticket = $this->findModel($ticket);
            $model->user_id = Yii::$app->user->id;
            
            $transaction = \Yii::$app->db->beginTransaction();          
            try {
                if ($model->validate() && $model->save(false)){
                    $ticket->updateAttributes(['last_action_id' => $model->getPrimaryKey()]);
                    $transaction->commit();
                    return Json::encode([
                        'pjax_refresh' => false,
                        'target' => "#ts-$ticket->number",
                        'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                    ]);
                } 
                $transaction->rollBack();    
                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
            } catch (Exception $ex) {
                $transaction->rollBack();
                return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
            }
        }
        throw new NotFoundHttpException("Should not try to call this.");
    }

    public function actionCloseNormal($ticket)
    {
        $model = new Normal();
        if (Yii::$app->request->isAjax)
        {
            $model->ticket_id = $ticket;
            $ticket = $this->findModel($ticket);
            $model->user_id = Yii::$app->user->id;
            //if ($model->load(Yii::$app->request->post())) {
                $transaction = \Yii::$app->db->beginTransaction();          
                try {
                    if ($model->validate() && $model->save(false)){
                        $ticket->updateAttributes(['last_action_id' => $model->getPrimaryKey()]);
                    $transaction->commit();
                        return Json::encode([
                            'pjax_refresh' => false,
                            'target' => "#ts-$ticket->number",
                            'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                        ]);
                    } 
                    $transaction->rollBack();    
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
                }
            //}
        }
        throw new NotFoundHttpException("Should not try to call this.");
    }

    public function actionCloseWaiting($ticket)
    {
        $model = new Awaiting();
        if (Yii::$app->request->isAjax)
        {
            $model->ticket_id = $ticket;
            $ticket = $this->findModel($ticket);
            $model->user_id = Yii::$app->user->id;
            //if ($model->load(Yii::$app->request->post())) {
                $transaction = \Yii::$app->db->beginTransaction();          
                try {
                    if ($model->validate() && $model->save(false)){
                        $ticket->updateAttributes(['last_action_id' => $model->getPrimaryKey()]);
                    $transaction->commit();
                        return Json::encode([
                            'pjax_refresh' => false,
                            'target' => "#ts-$ticket->number",
                            'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                        ]);
                    } 
                    $transaction->rollBack();    
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
                }
            //}
        }
        throw new NotFoundHttpException("Should not try to call this.");
    }

    public function actionCloseDuplicate($ticket)
    {
        $model = new Duplicate();
        if (Yii::$app->request->isAjax)
        {
            $model->ticket_id = $ticket;
            $ticket = $this->findModel($ticket);
            $model->user_id = Yii::$app->user->id;
            //if ($model->load(Yii::$app->request->post())) {
                $transaction = \Yii::$app->db->beginTransaction();          
                try {
                    if ($model->validate() && $model->save(false)) {
                        $ticket->updateAttributes(['last_action_id' => $model->getPrimaryKey()]);
                    $transaction->commit();
                        return Json::encode([
                            'pjax_refresh' => false,
                            'target' => "#ts-$ticket->number",
                            'refresh_link' => Url::to(['ticket/view', 'id' => $ticket->id, 'mode' => 'list-item']),
                        ]);
                    } 
                    $transaction->rollBack();    
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
                }
            //}
        }
        throw new NotFoundHttpException("Should not try to call this.");
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Ticket();

        if (Yii::$app->request->isAjax) {
            $model->issuer_id = Yii::$app->user->id;
            if ($model->load(Yii::$app->request->post())) {
                if (is_null($model->number))
                    $model->number = AutoNumber::generate('TS.{Y.m}.????');
                
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save(false))
                    {
                        $pk = $model->getPrimaryKey();
                        $open = Open::findOne(['ticket_id' => $pk]);
                        if (is_null($open)){
                            $open = new Open();
                            $open->ticket_id = $pk;
                            $open->user_id = Yii::$app->user->id;
                            $open->save(false);
                            $model->updateAttributes(['last_action_id' => $open->getPrimaryKey()]);
                        }
                        $transaction->commit();
                        return Json::encode([
                            'pjax_refresh' => true,
                            'target' => "#ts-$model->number",
                            'refresh_link' => Url::to(['ticket/view', 'id' => $model->id, 'mode' => 'list-item']),
                        ]);
                        $transaction->rollBack();    
                        return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $model->getErrors()));
                    }
                }
                catch (Exception $ex)
                {
                    $transaction->rollBack();
                    return Json::encode(array('status' => 'warning', 'type' => 'warning', 'message' => $ex->getMessage()));
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        if (Yii::$app->request->isAjax)
            return $this->renderAjax('_create_ticket', ['model' => $model,]);
        else
            return $this->render('create', ['model' => $model,]);
    }

    public function actionValidateCreate() {
        $model = new Ticket();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            //$model->owner_id = Yii::$app->user->id;
            //$model->ticket_id = $ticket;
            //$model->created_at = time();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
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
