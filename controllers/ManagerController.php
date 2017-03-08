<?php

namespace noam148\imagemanager\controllers;

use Yii;
use noam148\imagemanager\models\ImageManager;
use noam148\imagemanager\models\ImageManagerSearch;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use noam148\imagemanager\Module;

/**
 * Manager controller for the `imagemanager` module
 */
class ManagerController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
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
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		//disable CSRF Validation
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	/**
	 * Lists all ImageManager models.
	 * @return mixed
	 */
	public function actionIndex() {
		//get iframe parameters
		$viewMode = Yii::$app->request->get("view-mode", "page");
		$selectType = Yii::$app->request->get("select-type", "input");
		$inputFieldId = Yii::$app->request->get("input-id");
		$cropAspectRatio = Yii::$app->request->get("aspect-ratio");
		$cropViewMode = Yii::$app->request->get("crop-view-mode", 1);
		$cropDragMode = Yii::$app->request->get("crop-drag-mode", 'crop');
		$defaultImageId = Yii::$app->request->get("image-id");

		//set blank layout if viewMode = iframe
		if ($viewMode == "iframe") {
			//set layout
			$this->layout = "blank";

			//set stylesheet for modal
			$aCssFiles = \Yii::$app->controller->module->cssFiles;
			if (is_array($aCssFiles) && count($aCssFiles) > 0) {
				//if exists loop through files and add them to iframe mode
				foreach ($aCssFiles AS $cssFile) {
					//registrate file
					$this->view->registerCssFile($cssFile, ['depends' => 'yii\bootstrap\BootstrapAsset']);
				}
			}
		}

		//set baseUrl from image manager
		$sBaseUrl = Url::to(['/imagemanager/manager']);
		//set base url
		$this->view->registerJs("imageManagerModule.baseUrl = '" . $sBaseUrl . "';", 3);
		$this->view->registerJs("imageManagerModule.defaultImageId = '" . $defaultImageId . "';", 3);
		$this->view->registerJs("imageManagerModule.fieldId = '" . $inputFieldId . "';", 3);
		$this->view->registerJs("imageManagerModule.cropRatio = '" . $cropAspectRatio . "';", 3);
		$this->view->registerJs("imageManagerModule.cropViewMode = '" . $cropViewMode . "';", 3);
		$this->view->registerJs("imageManagerModule.cropDragMode = '" . $cropDragMode . "';", 3);
		$this->view->registerJs("imageManagerModule.selectType = '" . $selectType . "';", 3);
		$this->view->registerJs("imageManagerModule.message = " . Json::encode([
					'deleteMessage' => Yii::t('imagemanager', 'Are you sure you want to delete this image?'),
				]) . ";", 3);

		$searchModel = new ImageManagerSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		//render template
		return $this->render(
						'index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
					'viewMode' => $viewMode,
					'selectType' => $selectType,
		]);
	}

	/**
	 * Creates a new ImageManager model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionUpload() {
        //set response header
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        // Check if the user is allowed to upload the image
        if (Yii::$app->controller->module->canUploadImage == false) {
            // Return the response array to prevent from the action being executed any further
            return [];
        }

		//disable Csrf
		Yii::$app->controller->enableCsrfValidation = false;
		//return default
		$return = $_FILES;
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		//create the folder
		BaseFileHelper::createDirectory($sMediaPath);

		//check file isset
		if (isset($_FILES['imagemanagerFiles']['tmp_name'])) {
			//loop through each uploaded file
			foreach ($_FILES['imagemanagerFiles']['tmp_name'] AS $key => $sTempFile) {
				//collect variables
				$sFileName = $_FILES['imagemanagerFiles']['name'][$key];
				$sFileExtension = pathinfo($sFileName, PATHINFO_EXTENSION);
				$iErrorCode = $_FILES['imagemanagerFiles']['error'][$key];
				//if uploaded file has no error code  than continue;
				if ($iErrorCode == 0) { 
					//create a file record
					$model = new ImageManager();
					$model->fileName = str_replace("_", "-", $sFileName);
					$model->fileHash = Yii::$app->getSecurity()->generateRandomString(32);
					//if file is saved add record
					if ($model->save()) {
						//move file to dir
						$sSaveFileName = $model->id . "_" . $model->fileHash . "." . $sFileExtension;
						//move_uploaded_file($sTempFile, $sMediaPath."/".$sFileName);
						//save with Imagine class
						Image::getImagine()->open($sTempFile)->save($sMediaPath . "/" . $sSaveFileName);
					}
				}
			}
		}
		//echo return json encoded
		return $return;
	}

	/**
	 * Crop image and create new ImageManager model.
	 * @return mixed
	 */
	public function actionCrop() {
		//return 
		$return = null;
		//disable Csrf
		Yii::$app->controller->enableCsrfValidation = false;
		//set response header
		Yii::$app->getResponse()->format = Response::FORMAT_JSON;
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		//get post
		$ImageManager_id = Yii::$app->request->post("ImageManager_id");
		$aCropData = Yii::$app->request->post("CropData");
		//get details
		$modelOriginal = $this->findModel($ImageManager_id);
		//check if path is not null
		if ($modelOriginal->imagePathPrivate !== null && $aCropData !== null) {
			//dimension
			$iDimensionWidth = round($aCropData['width']);
			$iDimensionHeight = round($aCropData['height']);
			//collect variables
			$sFileNameReplace = preg_replace("/_crop_\d+x\d+/", "", $modelOriginal->fileName);
			$sFileName = pathinfo($sFileNameReplace, PATHINFO_FILENAME);
			$sFileExtension = pathinfo($sFileNameReplace, PATHINFO_EXTENSION);
			$sDisplayFileName = $sFileName . "_crop_" . $iDimensionWidth . "x" . $iDimensionHeight . "." . $sFileExtension;

			//create a file record
			$model = new ImageManager();
			$model->fileName = $sDisplayFileName;
			$model->fileHash = Yii::$app->getSecurity()->generateRandomString(32);
			//if file is saved add record
			if ($model->save()) {
				//create file to dir
				$sSaveFileName = $model->id . "_" . $model->fileHash . "." . $sFileExtension;

				if ($aCropData['x'] < 0 || $aCropData['y'] < 0) {
					//get position
					$posX = $aCropData['x'];
					$posY = $aCropData['y'];
					//get image size
					$sizeImage = getimagesize($modelOriginal->imagePathPrivate);
					$sizeImageW = $sizeImage[0]; // natural width
					$sizeImageH = $sizeImage[1]; // natural height
					//get orignal image and crop it
					$iCropWidth = $sizeImageW;
					if ($sizeImageW > $aCropData['width']) {
						$iCropWidth = $aCropData['width'] - abs($posX);
					}
					$iCropHeight = $sizeImageH;
					if ($sizeImageH > $aCropData['height']) {
						$iCropHeight = $aCropData['height'] - abs($posY);
					}

					//crop image
					$image = Image::getImagine()->open($modelOriginal->imagePathPrivate)
							->crop(new Point(0, 0), new Box($iCropWidth, $iCropHeight));

					//create image
					$size = new Box($aCropData['width'], $aCropData['height']);
					$color = new Color('#FFF', 100);
					Image::getImagine()->create($size, $color)
							->paste($image, new Point(($posX < 0 ? abs($posX) : $posX), ($posY < 0 ? abs($posY) : $posY)))
							->crop(new Point(($posX < 0 ? 0 : $posX), ($posY < 0 ? 0 : $posY)), new Box($aCropData['width'], $aCropData['height']))
							->save($sMediaPath . "/" . $sSaveFileName);
				} else {
					//save new image
					Image::getImagine()
							->open($modelOriginal->imagePathPrivate)
							->crop(new Point($aCropData['x'], $aCropData['y']), new Box($aCropData['width'], $aCropData['height']))
							->save($sMediaPath . "/" . $sSaveFileName);
				}

				//set return id
				$return = $model->id;
			}
		}
		//echo return json encoded
		return $return;
	}

	/**
	 * Get view details
	 * @return mixed
	 */
	public function actionView() {
		//disable Csrf
		Yii::$app->controller->enableCsrfValidation = false;
		//return default
		$return = [];
		//set response header
		Yii::$app->getResponse()->format = Response::FORMAT_JSON;
		//get post
		$ImageManager_id = Yii::$app->request->post("ImageManager_id");
		//get details
		$model = $this->findModel($ImageManager_id);
		//set return details
		$return['id'] = $model->id;
		$return['fileName'] = $model->fileName;
		$return['created'] = Yii::$app->formatter->asDate($model->created);
		$return['fileSize'] = $model->imageDetails['size'];
		$return['dimensionWidth'] = $model->imageDetails['width'];
		$return['dimensionHeight'] = $model->imageDetails['height'];
		$return['image'] = \Yii::$app->imagemanager->getImagePath($model->id, 400, 400, "inset") . "?t=" . time();

		//return json encoded
		return $return;
	}

	/**
	 * Get full image
	 * @return mixed
	 */
	public function actionGetOriginalImage() {
		//disable Csrf
		Yii::$app->controller->enableCsrfValidation = false;
		//set response header
		Yii::$app->getResponse()->format = Response::FORMAT_JSON;
		//get post
		$ImageManager_id = Yii::$app->request->post("ImageManager_id");
		//get details
		$model = $this->findModel($ImageManager_id);
		//set return
		$return = \Yii::$app->imagemanager->getImagePath($model->id, $model->imageDetails['width'], $model->imageDetails['height'], "inset");
		//return json encoded
		return $return;
	}

	/**
	 * Deletes an existing ImageManager model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @return mixed
	 */
	public function actionDelete() {
		//return 
		$return = ['delete' => false];
		//set response header
		Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        // Check if the user is allowed to delete the image
        if (Yii::$app->controller->module->canRemoveImage == false) {
            // Return the response array to prevent from the action being executed any further
            return $return;
        }

		//get post
		$ImageManager_id = Yii::$app->request->post("ImageManager_id");
		//get details
		$model = $this->findModel($ImageManager_id);

		//set some data
		$sFileExtension = pathinfo($model->fileName, PATHINFO_EXTENSION);
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileName = $model->id . "_" . $model->fileHash . "." . $sFileExtension;
		//delete record
		if ($model->delete()) {
			//check if file exists? if it is delete file
			if (file_exists($sMediaPath . "/" . $sFileName)) {
				unlink($sMediaPath . "/" . $sFileName);
			}
			$return['delete'] = true;
		}
		return $return;
	}

	/**
	 * Finds the ImageManager model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return ImageManager the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = ImageManager::findOne($id)) !== null) {
		    /* @var $model ImageManager */
            // Get the module instance
            $module = Module::getInstance();

            // Check if the model belongs to this user
            if ($module->setBlameableBehavior) {
                // Check if the user and record ID match
                if (Yii::$app->user->id != $model->createdBy) {
                    throw new NotFoundHttpException(Yii::t('imagemanager', 'The requested image does not exist.'));
                }
            }

			return $model;
		} else {
            throw new NotFoundHttpException(Yii::t('imagemanager', 'The requested image does not exist.'));
		}
	}

}
