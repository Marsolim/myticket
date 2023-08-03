<?php

namespace frontend\controllers;

use common\models\Document;
use common\models\User;
use common\models\ManagedStore;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * RegionController implements the CRUD actions for Region model.
 */
class DocumentController extends Controller
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
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Region models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Document::find();
        if (!User::isMemberOfRole([User::ROLE_SYS_ADMINISTRATOR, User::ROLE_ADMINISTRATOR]))
        {
            $query->orWhere(['owner_id' => Yii::$app->user->id]);
        }
        if (User::isMemberOfRole([User::ROLE_STORE_MANAGER]))
        {
            $store = ManagedStore::findOne(['user_id' => Yii::$app->user->id]);
            $query->orWhere(['store_id' => $store->id]);
        }

        $model = $query->all();
        return $this->render('index', [
            'model' => $model,
        ]);
    }
    
    /**
     * Updates an existing Region model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Region model.
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

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        return Yii::$app->response->sendFile('uploads/documents/'.$model->filename, $model->uploadname, ['inline' => false])->send();
    }

    /**
     * Finds the Region model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Region the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Document::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
