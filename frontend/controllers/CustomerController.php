<?php

namespace frontend\controllers;

use common\models\actors\Company;
use common\models\actors\Depot;
use common\models\actors\User;
use common\models\actors\Store;
use common\models\Contract;
use common\models\docs\Document;
use frontend\models\search\StoreSearch;
use frontend\models\search\TicketSearch;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use frontend\helpers\UserHelper;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class CustomerController extends Controller
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
                        'assign' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Shop models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StoreSearch();
        $query = $searchModel->searchQuery($this->request->queryParams);
        // if (UserHelper::isGeneralManager())
        // {
        //     $user = User::findOne(['id' => Yii::$app->user->id]);
        //     $query->andWhere(['region_id' => $user->region_id]);
        // }
        // else if (!(UserHelper::isAdministrator() || UserHelper::isGeneralManager())) 
        // {
        //     throw new UnauthorizedHttpException();
        // }
        $dataProvider = $searchModel->search($query);

        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
    }

    /**
     * Displays a single Shop model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        Url::remember();

        return $this->render('view', [ 'model' => $this->findModel($id), ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Store();

        if (UserHelper::isStoreManager())
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $model->parent_id = $user->associate_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $contract = $model->contract;
                if (empty($contract)) {
                    $contract = new Contract();
                    $contract->customer_id = $model->id;
                }
                $contract->sla = ArrayHelper::getValue($this->request->post(), "Store.contract.sla");
                $contract->status = Contract::STATUS_ACTIVE;
                $contract->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'regions' => Depot::find()->all(),
        ]);
    }

    /**
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $contract = $model->contract;
            if (empty($contract)) {
                $contract = new Contract();
                $contract->customer_id = $model->id;
            }
            $contract->sla = ArrayHelper::getValue($this->request->post(), "Store.contract.sla");
            $contract->status = Contract::STATUS_ACTIVE;
            $contract->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'regions' => Depot::find()->all(),
        ]);
    }

    /**
     * Deletes an existing Shop model.
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

    public function actionStoreList($q = null, $id = null, $page = 1) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => 0, 'text' => ''], 'total_count' => 1];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(['id', 'CONCAT(r.code, "-", r.name) AS text'])
                ->from(['r' => 'customer'])
                ->where(['or', ['like', 'r.name', $q], ['like', 'r.code', $q]])
                ->andWhere(['type' => Store::class]);
            $out['total_count'] = $query->count();
            $command = $query->offset(($page-1) * 20)->limit(20)->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Store::find($id)->toString()];
        }
        return $out;
    }

    public function actionDepotList($q = null, $id = null, $page = 1) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => 0, 'text' => ''], 'total_count' => 1];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(['id', 'CONCAT(r.code, "-", r.name) AS text'])
                ->from(['r' => 'customer'])
                ->where(['or', ['like', 'r.name', $q], ['like', 'r.code', $q]])
                ->andWhere(['type' => Depot::class]);
            $out['total_count'] = $query->count();
            $command = $query->offset(($page-1) * 20)->limit(20)->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Depot::find($id)->toString()];
        }
        return $out;
    }

    public function actionCompanyList($q = null, $id = null, $page = 1) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => 0, 'text' => ''], 'total_count' => 1];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(['id', 'CONCAT(r.code, "-", r.name) AS text'])
                ->from(['r' => 'customer'])
                ->where(['or', ['like', 'r.name', $q], ['like', 'r.code', $q]])
                ->andWhere(['type' => Company::class]);
            $out['total_count'] = $query->count();
            $command = $query->offset(($page-1) * 20)->limit(20)->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Company::find($id)->toString()];
        }
        return $out;
    }

    /**
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Store::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
