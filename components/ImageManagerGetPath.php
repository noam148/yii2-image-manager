<?php

namespace noam148\imagemanager\components;

use yii\base\Component;
use noam148\imagemanager\models\ImageManager;

class ImageManagerGetPath extends Component { 

	/**
	 * @var null|string $mediaPath Folder path in which the images are stored
	 */
	public $mediaPath = null;

	/**
	 * @var string $cachePath cache path where store the resized images (relative from webroot (index.php))
	 */
	public $cachePath = "assets/imagemanager";

	/**
	 * @var boolean $useFilename use original filename in generated cache file
	 */
	public $useFilename = true;

	/**
	 * @var boolean $useFilename use original filename in generated cache file
	 */
	public $absoluteUrl = false;

	/*
	 * Init set config
	 */

	public function init() {
		parent::init();
		// initialize the compontent with the configuration loaded from config.php
		\Yii::$app->set('imageresize', [
			'class' => 'noam148\imageresize\ImageResize',
			'cachePath' => $this->cachePath,
			'useFilename' => $this->useFilename,
			'absoluteUrl' => $this->absoluteUrl,
		]);
	}

	/**
	 * Get the path for the given ImageManager_id record
	 * @param int $ImageManager_id ImageManager record for which the path needs to be generated
	 * @param int $width Thumbnail image width
	 * @param int $height Thumbnail image height
	 * @param string $thumbnailMode Thumbnail mode
	 * @return null|string Full path is returned when image is found, null if no image could be found
	 */
	public function getImagePath($ImageManager_id, $width = 400, $height = 400, $thumbnailMode = "outbound") {
		//default return
		$return = null;
		$mImageManager = ImageManager::findOne($ImageManager_id);

		//check if not empty
		if ($mImageManager !== null) {
			//set crop mode
			$mode = $thumbnailMode == "outbound" ? "outbound" : "inset";

			$sMediaPath = null;
			if ($this->mediaPath !== null) {
				$sMediaPath = $this->mediaPath;
			}

			$sFileExtension = pathinfo($mImageManager->fileName, PATHINFO_EXTENSION);
			//get image file path
			$sImageFilePath = $sMediaPath . '/' . $mImageManager->id . '_' . $mImageManager->fileHash . '.' . $sFileExtension;
			//check file exists
			if (file_exists($sImageFilePath)) {
				$return = \Yii::$app->imageresize->getUrl($sImageFilePath, $width, $height, $mode, null, $mImageManager->fileName);
			} else {
				$return = null; //isset(\Yii::$app->controller->module->assetPublishedUrl) ? \Yii::$app->controller->module->assetPublishedUrl. "/img/img_no-image.png" : null;
			}
		}
		return $return;
	}

}
