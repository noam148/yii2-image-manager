<?php

namespace noam148\imagemanager\models;

use noam148\imagemanager\Module;
use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "ImageManager".
 *
 * @property integer $id
 * @property string $fileName
 * @property string $fileHash
 * @property string $created
 * @property string $modified
 * @property string $createdBy
 * @property string $modifiedBy
 */
class ImageManager extends \yii\db\ActiveRecord { 

	/**
	 * Set Created date to now
	 */
	public function behaviors() {
	    $aBehaviors = [];

	    // Add the time stamp behavior
        $aBehaviors[] = [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created',
            'updatedAtAttribute' => 'modified',
            'value' => new Expression('NOW()'),
        ];

        // Check if we need blamable behavior
        if (Yii::$app->getModule('imagemanager')->setBlameableBehavior) {
            $aBehaviors[] = [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdBy',
                'updatedByAttribute' => 'modifiedBy',
            ];
        }

		return $aBehaviors;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'ImageManager';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['fileName', 'fileHash'], 'required'],
			[['created', 'modified'], 'safe'],
			[['fileName'], 'string', 'max' => 128],
			[['fileHash'], 'string', 'max' => 32],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('imagemanager', 'ID'),
			'fileName' => Yii::t('imagemanager', 'File Name'),
			'fileHash' => Yii::t('imagemanager', 'File Hash'),
			'created' => Yii::t('imagemanager', 'Created'),
			'modified' => Yii::t('imagemanager', 'Modified'),
			'createdBy' => Yii::t('imagemanager', 'Created by'),
			'modifiedBy' => Yii::t('imagemanager', 'Modified by'),
		];
	}

	/**
	 * Get image path private
	 * @return string|null If image file exists the path to the image, if file does not exists null
	 */
	public function getImagePathPrivate() {
		//set default return
		$return = null;
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileExtension = pathinfo($this->fileName, PATHINFO_EXTENSION);
		//get image file path
		$sImageFilePath = $sMediaPath . '/' . $this->id . '_' . $this->fileHash . '.' . $sFileExtension;
		//check file exists
		if (file_exists($sImageFilePath)) {
			$return = $sImageFilePath;
		}
		return $return;
	}

	/**
	 * Get image data dimension/size
	 * @return array The image sizes
	 */
	public function getImageDetails() {
		//set default return
		$return = ['width' => 0, 'height' => 0, 'size' => 0];
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileExtension = pathinfo($this->fileName, PATHINFO_EXTENSION);
		//get image file path
		$sImageFilePath = $sMediaPath . '/' . $this->id . '_' . $this->fileHash . '.' . $sFileExtension;
		//check file exists
		if (file_exists($sImageFilePath)) {
			$aImageDimension = getimagesize($sImageFilePath);
			$return['width'] = isset($aImageDimension[0]) ? $aImageDimension[0] : 0;
			$return['height'] = isset($aImageDimension[1]) ? $aImageDimension[1] : 0;
			$return['size'] = Yii::$app->formatter->asShortSize(filesize($sImageFilePath), 2);
		}
		return $return;
	}

}
