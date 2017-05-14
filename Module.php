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
class Module extends \yii\base\Module {

	public $defaultRoute = 'manager';
	//stylesheet for modal iframe
	public $cssFiles = [];
	//allowed Extensions for upload
	public $allowedFileExtensions = ['jpg', 'jpeg', 'gif', 'png'];
	//set assetPublishedUrl
	public $assetPublishedUrl;

	/**
	 * @var bool|callable Variable that defines if the upload action will be available
	 * This variable defaults to true, to enable uploading by default
	 * It is also possible to give a callable function, in which case the function will be executed
	 */
	public $canUploadImage = true;

	/**
	 * @var bool|callable Variable that defines if the delete action will be available
	 * This variable default to true, to enable removal if image
	 * It is also possible to give a callable function, in which case the function will be executed
	 */
	public $canRemoveImage = true;

    /**
     * @var bool|callable Variable that defines if blameable behavior is used.
     * This can be a boolean, or a callable function that returns a boolean
     */
	public $setBlameableBehavior = false;

    /**
     * @var bool|callable Variable that defines if the original image that was used to make the crop will be deleted after the cropped image has been saved
     * By default the original and the cropped image will both be saved, this function can also be a callable.
     */
	public $deleteOriginalAfterEdit = false;

	/**
	 * @inheritdoc
	 */
	public function init() {
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
		if (Yii::$app->imagemanager->mediaPath === null) {
			throw new InvalidConfigException("Component param 'mediaPath' need to be set to a location");
		}
		//set asset path
		$this->assetPublishedUrl = (new AssetManager)->getPublishedUrl("@vendor/noam148/yii2-image-manager/assets/source");

		// Check if the canRemoveImage variable is callable
		if (is_callable($this->canRemoveImage)) {
			$this->canRemoveImage = call_user_func($this->canRemoveImage);
		}

		// Check if the canUploadImage variable is callable
		if (is_callable($this->canUploadImage)) {
			$this->canUploadImage = call_user_func($this->canUploadImage);
		}

		// Check if blameable behavior is callable
        if (is_callable($this->setBlameableBehavior))
            $this->setBlameableBehavior = call_user_func($this->setBlameableBehavior);

		// Check if the Delete original after crop variable is callable
        if (is_callable($this->deleteOriginalAfterEdit))
            $this->deleteOriginalAfterEdit = call_user_func($this->deleteOriginalAfterEdit);

		// Check if the variable configuration is correct in order for the module to function
		$this->_checkVariableConfiguration();
	}

	/**
	 * Check if extensions exists
	 * @throws UnknownClassException Throw error if extension is not found
	 */
	private function _checkExtensionsExists() {
		//kartik file uploaded is installed
		if (!class_exists('kartik\file\FileInput')) {
			throw new UnknownClassException("Can't find: kartik\\file\FileInput. Install \"kartik-v/yii2-widget-fileinput\": \"@dev\"");
		}
		//check Yii imagine is installed
		if (!class_exists('yii\imagine\Image')) {
			throw new UnknownClassException("Can't find: yii\imagine\Image. Install \"yiisoft/yii2-imagine\": \"~2.0.0\"");
		}
	}

	/**
	 * Check if the module variables have the content that is expected
	 * @throws InvalidConfigException
	 */
	private function _checkVariableConfiguration() {
		// Check if the canUploadImage is boolean
		if (!is_bool($this->canUploadImage)) {
			throw new InvalidConfigException('$canUploadImage variable only supports a boolean value, if you have a custom function you must return a boolean.');
		}
		// Check if the canRemoveImage is boolean
		if (!is_bool($this->canRemoveImage)) {
			throw new InvalidConfigException('$removeImageAllowed variable only supports a boolean value, if you have a custom function you must return a boolean.');
		}
		// Check if the setBlamableBehavior is boolean
        if (! is_bool($this->setBlameableBehavior))
            throw new InvalidConfigException('$setBlameableBehavior only supports a boolean value, if you have a custom function make sure that you return a boolean.');
        // Check if the deleteOriginalAfterEdit is boolean
        if (! is_bool($this->deleteOriginalAfterEdit))
            throw new InvalidConfigException('$deleteOriginalAfterEdit only supports boolean value, if you have a custom function make sure that your return a boolean.');

		// Check if the blameable behavior is set to true
        if ($this->setBlameableBehavior) {
            // Get the migration record
            $mRecordMigrationRun = Yii::$app->db->createCommand('SELECT * FROM {{%migration}} WHERE `version` = \'m170223_113221_addBlameableBehavior\'')->queryOne();
            if ($mRecordMigrationRun === false) {
                throw new InvalidConfigException('Image Manager: You have not run the latest migration, see the documentation how to do this.');
            }
        }
	}

}
