<?php
namespace noam148\imagemanager\components;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use noam148\imagemanager\models\ImageManager;
use noam148\imagemanager\assets\ImageManagerInputAsset;

class ImageManagerInputWidget extends InputWidget{
	//default ratio
	public $aspectRatio = null; //option info: https://github.com/fengyuanchen/cropper/#aspectratio
	public $cropViewMode = 1; //option info: https://github.com/fengyuanchen/cropper/#viewmode
	public $showPreview = true; 
	
	public function init(){
		
	}
	
	/**
     * {@inheritdoc}
     */
    public function run()
    {
		//default
		$ImageManager_id = null;
		$mImageManager = null;
		$sFieldId = null;
		//start input group
		$field = "<div class='image-manager-input'>";
		$field .= "<div class='input-group'>";
		//set input fields
		if ($this->hasModel()) {
			//get field id
			$sFieldId = Html::getInputId($this->model, $this->attribute);
			$sFieldNameId = $sFieldId."_name";
			//get filename from selected file
			$ImageManager_id = $this->model->{$this->attribute};
			$ImageManager_fileName = null;
			$mImageManager = ImageManager::findOne($ImageManager_id);
			if($mImageManager !== null){
				$ImageManager_fileName = $mImageManager->fileName;
			}			
			//create field
			$field .= Html::textInput($this->attribute, $ImageManager_fileName, ['class'=>'form-control', 'id'=>$sFieldNameId, 'readonly'=>true]);
            $field .= Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
			$field .= Html::textInput($this->name."_name", null, ['readonly'=>true]);
            $field .= Html::hiddenInput($this->name, $this->value, $this->options);
        }
		//end input group
		$sHideClass = $ImageManager_id === null ? 'hide' : '';
		$field .= "<a href='#' class='input-group-addon btn btn-primary delete-selected-image ".$sHideClass."' data-input-id='".$sFieldId."'><i class='glyphicon glyphicon-remove' aria-hidden='true'></i></a>";
		$field .= "<a href='#' class='input-group-addon btn btn-primary open-modal-imagemanager' data-aspect-ratio='".$this->aspectRatio."' data-crop-view-mode='".$this->cropViewMode."' data-input-id='".$sFieldId."'>";
		$field .= "<i class='glyphicon glyphicon-folder-open' aria-hidden='true'></i>";
		$field .= "</a></div>";
		
		//show preview if is true
		if($this->showPreview == true && $mImageManager != null){
			$field .= '<img id="'.$sFieldId.'_image" alt="Thumbnail" class="img-responsive img-thumbnail img-preview" src="'.\Yii::$app->imagemanager->getImagePath($mImageManager->id, 500, 500, 'inset').'">';
		}
		
		//close image-manager-input div
		$field .= "</div>";
		
		echo $field;
		
        $this->registerClientScript();
    }
	
	/**
     * Registers js Input
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        ImageManagerInputAsset::register($view);
		
		//set baseUrl from image manager
		$sBaseUrl =  Url::to(['imagemanager/manager']);
		//set base url
		$view->registerJs("imageManagerInput.baseUrl = '".$sBaseUrl."';");
    }
}