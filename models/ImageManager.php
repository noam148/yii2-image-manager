<?php

namespace noam148\imagemanager\models;

use Yii;
use yii\db\Expression;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ImageManager".
 *
 * @property integer $id
 * @property string $fileName
 * @property string $fileHash
 * @property string $created
 * @property string $modified
 */
class ImageManager extends \yii\db\ActiveRecord
{
	
	/*
     * Set Created date to now
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'modified',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ImageManager';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('imagemanager', 'ID'),
            'fileName' => Yii::t('imagemanager', 'File Name'),
            'fileHash' => Yii::t('imagemanager', 'File Hash'),
            'created' => Yii::t('imagemanager', 'Created'),
            'modified' => Yii::t('imagemanager', 'Modified'),
        ];
    }
	
	/*
	 * Get image path private
	 */
	public function getImagePathPrivate(){	
    	//set default return
    	$return = null;
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileExtension = pathinfo($this->fileName, PATHINFO_EXTENSION);
    	//get image file path
    	$sImageFilePath = $sMediaPath.'/'.$this->id.'_'.$this->fileHash.'.'.$sFileExtension;
    	//check file exists
    	if(file_exists($sImageFilePath)){
    		$return = $sImageFilePath;
    	}
    	return $return;
    }
	
	/*
	 * Get image data dimension/size
	 */
	public function getImageDetails(){	
    	//set default return
    	$return = ['width'=>0, 'height'=>0, 'size'=> 0];
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileExtension = pathinfo($this->fileName, PATHINFO_EXTENSION);
    	//get image file path
    	$sImageFilePath = $sMediaPath.'/'.$this->id.'_'.$this->fileHash.'.'.$sFileExtension;
    	//check file exists
    	if(file_exists($sImageFilePath)){
    		$aImageDimension = getimagesize($sImageFilePath);
			$return['width'] = isset($aImageDimension[0]) ? $aImageDimension[0] : 0;
			$return['height'] = isset($aImageDimension[1]) ? $aImageDimension[1] : 0;
			$return['size'] = Yii::$app->formatter->asShortSize(filesize($sImageFilePath), 2);
    	}
    	return $return;
    }
	
}