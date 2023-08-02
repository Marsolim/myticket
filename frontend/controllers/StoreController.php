<?php

namespace frontend\controllers;

use common\models\User;
use common\models\Store;
use common\models\Region;
use common\models\SLAStatus;
use common\models\ManagedStore;
use common\models\StoreSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class StoreController extends Controller
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
                    'class' => VerbFilter::className(),
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
        if (User::isMemberOfRole(User::ROLE_STORE_MANAGER))
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $query->andWhere(['region_id' => $user->region_id]);
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
        return $this->render('view', [
            'model' => $this->findModel($id),
            'status' => [['id'=> 1, 'name' => 'Garansi'], ['id'=> 2, 'name' => 'Non Garansi']],
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
        $regions = Region::find();
        if (User::isMemberOfRole(User::ROLE_STORE_MANAGER))
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $model->region_id = $user->region_id;
            $regions->andWhere(['region_id' => $user->region_id]);
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
            'regions' => $regions->all(),
            'status' => SLAStatus::find()->all(),
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

        $regions = Region::find();
        if (User::isMemberOfRole(User::ROLE_STORE_MANAGER))
        {
            $user = User::findOne(['id' => Yii::$app->user->id]);
            $model->region_id = $user->region_id;
            $regions->andWhere(['region_id' => $user->region_id]);
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'regions' => $regions->all(),
            'status' => SLAStatus::find()->all(),
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
     * Deletes an existing Shop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAssign($store, $mgr)
    {
        $model = $this->findModel($store);

        $mgs = ManagedStore::findOne(['store_id' => $store, 'active'=>ManagedStore::STATUS_ACTIVE]);
        $mgs->active = ManagedStore::STATUS_INACTIVE;
        $mgs->save();
        $mgs = new ManagedStore();
        $mgs->store_id = $store;
        $mgs->user_id = $mgr;
        $mgs->active = ManagedStore::STATUS_ACTIVE;
        $mgs->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'value' => $store,
            'user' => $mgr,
        ];
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
