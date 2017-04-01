<?php

namespace noam148\imagemanager\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ImageManagerTag".
 *
 * @property integer $id
 * @property string $name
 * @property string $created
 * @property string $modified
 *
 * @property ImageManagerImageManagerTag[] $imageManagerImageManagerTags
 * @property ImageManager[] $imageManagers
 */
class ImageManagerTag extends \yii\db\ActiveRecord
{
	public $used;
	
	/**
	 * Set Created date to now
	 */
	public function behaviors() {
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
        return 'ImageManagerTag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('imagemanager', 'ID'),
            'name' => Yii::t('imagemanager', 'Tag name'),
			'used' => Yii::t('imagemanager', 'Used'),
            'created' => Yii::t('imagemanager', 'Created'),
            'modified' => Yii::t('imagemanager', 'Modified'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImageManagerImageManagerTags()
    {
        return $this->hasMany(ImageManagerImageManagerTag::className(), ['ImageManagerTag_id' => 'id']);
    }
	
	 /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagUsedCount()
    {
        return $this->hasMany(ImageManagerImageManagerTag::className(), ['ImageManagerTag_id' => 'id'])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImageManagers()
    {
        return $this->hasMany(ImageManager::className(), ['id' => 'ImageManager_id'])->viaTable('ImageManager_ImageManagerTag', ['ImageManagerTag_id' => 'id']);
    }
}
