<?php
namespace noam148\imagemanager;

use Yii;
use yii\base\UnknownClassException;
use yii\base\InvalidConfigException;
use yii\web\AssetManager;
use noam148\imagemanager\assets\ImageManagerModuleAsset;

/**
 * imagemanager module definition class
 */
class Module extends \yii\base\Module
{
	
	public $defaultRoute = 'manager';
	
	//stylesheet for modal iframe
	public $cssFiles = [];
	//allowed Extensions for upload
	public $allowedFileExtensions = ['jpg', 'jpeg', 'gif', 'png'];
	//set assetPublishedUrl
	public $assetPublishedUrl;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
		//set language
		if (!isset(Yii::$app->i18n->translations['imagemanager'])) {
            Yii::$app->i18n->translations['imagemanager'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@noam148/imagemanager/messages'
            ];
        }
		//check extensions
        $this->_checkExtensionsExists();
		//check mediaPath isset
		if(Yii::$app->imagemanager->mediaPath === null){
			throw new InvalidConfigException("Component param 'mediaPath' need to be set to a location");
		}
		//set asset path
		$this->assetPublishedUrl = (new AssetManager)->getPublishedUrl("@vendor/noam148/yii2-image-manager/assets/source");
    }
	
	/**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
			//set view
			$view = $action->controller->getView();
	
			//set asset
	        ImageManagerModuleAsset::register($view);

			//get parameters
			$viewMode = Yii::$app->request->get("view-mode","page");
			
			/* @var $action \yii\base\Action */
			if($viewMode == "iframe"){
				//set stylesheet for modal
				if (is_array($this->cssFiles) && count($this->cssFiles) > 0) {
					//if exists loop through files and add them to iframe mode
					foreach($this->cssFiles AS $cssFile){
						//registrate file
						$view->registerCssFile($cssFile, ['depends'=>'yii\bootstrap\BootstrapAsset']);
					}
				}
			}
           return true;
        }
        return false;
    }
	
	
	/*
	 * Check if extensions exists
	 */
	private function _checkExtensionsExists(){
		//kartik file uploaded is installed
		if (!class_exists('kartik\file\FileInput')) {
            throw new UnknownClassException("Can't find: kartik\\file\FileInput. Install \"kartik-v/yii2-widget-fileinput\": \"@dev\"");
        }
		//check Yii imagine is installed
		if (!class_exists('yii\imagine\Image')) {
            throw new UnknownClassException("Can't find: yii\imagine\Image. Install \"yiisoft/yii2-imagine\": \"~2.0.0\"");
        }
		
	}
}
