<?php

namespace frontend\controllers;

use common\models\User;
use frontend\models\SignupForm;
use common\models\Store;
use common\models\ManagedStore;
use common\models\Region;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use Yii;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class UserController extends Controller
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
                        'assign-role' => ['POST'],
                        'assign-store' => ['POST'],
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
        if (!Yii::$app->user->can('manageUser'))
        {
            $model = $this->findModel(Yii::$app->user->id);
            return $this->render('view', [
                'model' => $model,
            ]);
        }
        else 
        {
            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Displays a single Shop model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = Yii::$app->user->can('manageUser') ? $this->findModel($id) : $this->findModel(Yii::$app->user->id);
        if (isset($_POST['hasEditable'])) 
        {
            // use Yii's response format to encode output as JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            foreach ($_POST['User'] as $key => $value);

            // store old value of the attribute
            $oldValue = $model->$key;
            if ($key == 'region_id')
            {
                $region = Region::findOne(['id' => $model->$key]);
                if (isset($region)) $oldValue = $region->toString();
            }
            // read your posted model attributes
            if ($model->load($_POST)) 
            {
                if ($key == 'role')
                {
                    UserHelper::setRole($value, $model);
                }
                else 
                {
                    // read or convert your posted information
                    $value = $model->$key;
                }
                
                if ($key == 'region_id')
                {
                    //$model->$key = $value + 0;
                    $region = Region::findOne(['id' => $model->$key]);
                    if (isset($region)) $value = $region->toString();
                }
                if ($key == 'company_id')
                {
                    //$model->$key = $value + 0;
                    $company = Company::findOne(['id' => $model->$key]);
                    if (isset($company)) $value = $company->toString();
                }
                // validate if any errors
                if ($model->validate() && $model->save()) {
                    // return JSON encoded output in the below format on success with an empty `message`
                    return ['output' => $value, 'message' => ''];
                } else {
                    // alternatively you can return a validation error (by entering an error message in `message` key)
                    return ['output' => $oldValue, 'message' => 'Incorrect Value! Please reenter.'];
                }
            }
            // else if nothing to do always return an empty JSON encoded output
            else {
                return ['output'=>'', 'message'=>''];
            }
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Shop model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAssignRole($user, $role)
    {
        if (Yii::$app->request->isAjax) {
            $auth = Yii::$app->authManager;
            $user = User::findOne(['id' => $user]);
            $prevrole = $user->role;
            $role = $auth->getRole($role);
            
            if (isset($prevrole) && $prevrole != $role)
            {
                $prevrole = $auth->getRole($prevrole);
                $auth->revoke($prevrole, $user->id);
            }
            
            if ($prevrole != $role)
            {
                $auth->assign($role, $user->id);
            }
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->renderAjax('_assignrole', [
                'model' => $user,
                'showtext' => true
            ]);
        }
    }

    /**
     * Displays a single Shop model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAssignStore($user, $store)
    {
        if (Yii::$app->request->isAjax){
            $model = Store::findOne(['id' => $store]);

            $mgs = ManagedStore::findOne(['store_id' => $store, 'active'=>ManagedStore::STATUS_ACTIVE]);
            $smg = ManagedStore::findOne(['user_id' => $user, 'active'=>ManagedStore::STATUS_ACTIVE]);
            if (isset($mgs) && $mgs->user_id != $user)
            {
                $mgs->active = ManagedStore::STATUS_INACTIVE;
                $mgs->save();

                if (isset($smg) && $mgs->id != $smg->id && $mgs->store_id != $store){
                    $smg->active = ManagedStore::STATUS_INACTIVE;
                    $smg->save();
                }
            }
            
            if (!(isset($mgs) && $mgs->active == ManagedStore::STATUS_ACTIVE) &&
                !(isset($smg) && $smg->active == ManagedStore::STATUS_ACTIVE))
            {
                $nmgs = new ManagedStore();
                $nmgs->store_id = $store;
                $nmgs->user_id = $user;
                $nmgs->active = ManagedStore::STATUS_ACTIVE;
                $nmgs->save();
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'action' => 'reload',
                'label' => $model->name,
                'value' => $store,
                'user' => $user,
            ];
        }
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SignupForm();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->signup()) {
                $user = User::findOne(['username' => $model->username]);
                return $this->redirect(['view', 'id' => $user->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
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

        $filename = isset($model->profile) ? $model->profile : 'default_profile.jpg';
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->avatar = UploadedFile::getInstance($model, 'avatar');

            if ($model->avatar && $model->validate()) {
                $filename = Yii::$app->security->generateRandomString() . '.' . $model->avatar->extension;
                $filepath = 'uploads/profiles/' . $filename;
                $model->avatar->saveAs($filepath);
                Image::thumbnail($filepath, 100, 100)->save('uploads/profiles/thumb/'.$filename, ['quality' => 80]);
            }
            $model->profile = $filename;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    // /**
    //  * Deletes an existing Shop model.
    //  * If deletion is successful, the browser will be redirected to the 'index' page.
    //  * @param int $id ID
    //  * @return \yii\web\Response
    //  * @throws NotFoundHttpException if the model cannot be found
    //  */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id, 'status' => User::STATUS_ACTIVE])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
