<?php

namespace frontend\controllers;

use common\models\actors\Depot;
use common\models\actors\User;
use common\models\actors\Store;
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
        if (UserHelper::isGeneralManager())
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $query->andWhere(['region_id' => $user->region_id]);
        }
        else if (!(UserHelper::isAdministrator() || UserHelper::isGeneralManager())) 
        {
            throw new UnauthorizedHttpException();
        }
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
        $searchModel = new TicketSearch();
        $query = $searchModel->searchQuery($this->request->queryParams);
        $query->andWhere(['store_id' => $id]);

        $ticketProvider = $searchModel->search($query);

        $docquery = Document::find();

        $docquery->andWhere(['store_id' => $id]);
        $documentProvider = new ActiveDataProvider([
            'query' => $docquery,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        Url::remember();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'status' => [['id'=> 1, 'name' => 'Garansi'], ['id'=> 2, 'name' => 'Non Garansi']],
            'searchModel' => $searchModel,
            'ticketProvider' => $ticketProvider,
            'documentProvider' => $documentProvider,
        ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Store();

        if (UserHelper::isMemberOfRole(User::ROLE_STORE_MANAGER))
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $model->region_id = $user->region_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
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
        
        if (UserHelper::isMemberOfRole(User::ROLE_STORE_MANAGER))
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $model->region_id = $user->region_id;
        }
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
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
