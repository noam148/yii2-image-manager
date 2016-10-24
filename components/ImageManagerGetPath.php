<?php

namespace noam148\imagemanager\components;

use yii\base\Component;
use himiklab\thumbnail\EasyThumbnailImage;
use noam148\imagemanager\models\ImageManager;

class ImageManagerGetPath extends Component {
    /**
     * @var null|string Folder path in which the images are stored
     */
	public $mediaPath = null;

    /**
     * @var null|string Base of the URL that is added to the image path
     */
    public $baseUrlPublishedMedia = null;

    /**
     * Get the path for the given ImageManager_id record
     * @param int $ImageManager_id ImageManager record for which the path needs to be generated
     * @param int $width Thumbnail image width
     * @param int $height Thumbnail image height
     * @param string $thumbnailMode Thumbnail mode
     * @return null|string Full path is returned when image is found, null if no image could be found
     */
	public function getImagePath($ImageManager_id, $width = 400, $height = 400, $thumbnailMode = "outbound"){
		//default return
		$return = null;
		$mImageManager = ImageManager::findOne($ImageManager_id);
		
		//check if not empty
		if($mImageManager !== null){
			//set crop mode
			if($thumbnailMode == "outbound"){
				$mode = EasyThumbnailImage::THUMBNAIL_OUTBOUND;
			}else if($thumbnailMode == "inset"){
				$mode = EasyThumbnailImage::THUMBNAIL_INSET;
			}		

			//set default properties
			$baseUrlPublishedMedia = null;
			if($this->baseUrlPublishedMedia !== null){
				$baseUrlPublishedMedia = $this->baseUrlPublishedMedia;
			}

			$sMediaPath = null;
			if($this->mediaPath !== null){
				$sMediaPath = $this->mediaPath;
			}

			$sFileExtension = pathinfo($mImageManager->fileName, PATHINFO_EXTENSION);
			//get image file path
			$sImageFilePath = $sMediaPath.'/'.$mImageManager->id.'_'.$mImageManager->fileHash.'.'.$sFileExtension;
			//check file exists
			if(file_exists($sImageFilePath)){
				$return = $baseUrlPublishedMedia . EasyThumbnailImage::thumbnailFileUrl($sImageFilePath, $width, $height, $mode);
			}else{
				$return = null; //isset(\Yii::$app->controller->module->assetPublishedUrl) ? \Yii::$app->controller->module->assetPublishedUrl. "/img/img_no-image.png" : null;
			}
		}
    	return $return;
    }
}
