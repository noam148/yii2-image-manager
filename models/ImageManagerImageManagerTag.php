<?php

namespace noam148\imagemanager\models;

use Yii;

/**
 * This is the model class for table "ImageManager_ImageManagerTag".
 *
 * @property integer $ImageManager_id
 * @property integer $ImageManagerTag_id
 *
 * @property ImageManager $imageManager
 * @property ImageManagerTag $imageManagerTag
 */
class ImageManagerImageManagerTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ImageManager_ImageManagerTag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ImageManager_id', 'ImageManagerTag_id'], 'required'],
            [['ImageManager_id', 'ImageManagerTag_id'], 'integer'],
            [['ImageManager_id'], 'exist', 'skipOnError' => true, 'targetClass' => ImageManager::className(), 'targetAttribute' => ['ImageManager_id' => 'id']],
            [['ImageManagerTag_id'], 'exist', 'skipOnError' => true, 'targetClass' => ImageManagerTag::className(), 'targetAttribute' => ['ImageManagerTag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ImageManager_id' => Yii::t('imagemanager', 'Image Manager ID'),
            'ImageManagerTag_id' => Yii::t('imagemanager', 'Image Manager Tag ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImageManager()
    {
        return $this->hasOne(ImageManager::className(), ['id' => 'ImageManager_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImageManagerTag()
    {
        return $this->hasOne(ImageManagerTag::className(), ['id' => 'ImageManagerTag_id']);
    }
}
