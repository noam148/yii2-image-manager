<?php

namespace noam148\imagemanager\controllers;

use Yii;
use noam148\imagemanager\models\ImageManagerTag;
use noam148\imagemanager\models\ImageManagerTagSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TagController implements the CRUD actions for ImageManagerTag model.
 */
class TagController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ImageManagerTag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ImageManagerTagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ImageManagerTag model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ImageManagerTag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ImageManagerTag();	
		
		if ($model->load(Yii::$app->request->post())){
			if($model->save()){				
				Yii::$app->session->setFlash('success', Yii::t('imagemanager', 'Tag is created'));
				return $this->redirect(['index']);
			}else{	
				Yii::$app->session->setFlash('error', Yii::t('imagemanager', 'Tag is not created'));
			}
        }
		return $this->render('create', ['model' => $model,]);
    }

    /**
     * Updates an existing ImageManagerTag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())){
			if($model->save()){
				Yii::$app->session->setFlash('success', Yii::t('imagemanager', 'Tag is updated'));
				return $this->redirect(['index']);
			}else{
				Yii::$app->session->setFlash('error', Yii::t('imagemanager', 'Tag is not updated'));
			}
        }
		return $this->render('update', ['model' => $model,]);
    }

    /**
     * Deletes an existing ImageManagerTag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
		
		if($model->delete()){
			Yii::$app->session->setFlash('success', Yii::t('imagemanager', 'Tag is deleted'));
		}

        return $this->redirect(['index']);
    }

    /**
     * Finds the ImageManagerTag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ImageManagerTag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ImageManagerTag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
