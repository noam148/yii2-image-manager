<?php
namespace noam148\imagemanager\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use noam148\imagemanager\models\ImageManager;
use noam148\imagemanager\assets\ImageManagerInputAsset;

class ImageManagerInputWidget extends InputWidget{
    /**
     * @var null|integer The aspect ratio the image needs to be cropped in (optional)
     */
    public $aspectRatio = null; //option info: https://github.com/fengyuanchen/cropper/#aspectratio

    /**
     * @var int Define the viewMode of the cropper
     */
    public $cropViewMode = 1; //option info: https://github.com/fengyuanchen/cropper/#viewmode

    /**
     * @var string Define the drag mode for the cropper
     * See the option info: https://github.com/fengyuanchen/cropper/#dragmode
     */
    public $cropDragMode = 'crop';

    /**
     * @var bool Show a preview of the image under the input
     */
    public $showPreview = true;

    /**
     * @var bool Show a confirmation message when de-linking a image from the input
     */
    public $showDeletePickedImageConfirm = false;

    /**
     * @var array The available crop drag modes
     */
    private $_cropDragModes = ['crop', 'move', 'none'];

    /**
     * @inheritdoc
     */
    public function init(){
        parent::init();
        //set language
        if (!isset(Yii::$app->i18n->translations['imagemanager'])) {
            Yii::$app->i18n->translations['imagemanager'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@noam148/imagemanager/messages'
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // Validate the user input
        $this->_validateVariables();

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
            $aExploded = explode(']', $this->attribute);
            $iLast = (count($aExploded) - 1);
            $sField = $aExploded[$iLast];

            $ImageManager_id = $this->model->{$sField};
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
        $field .= "<a href='#' class='input-group-addon btn btn-primary delete-selected-image ".$sHideClass."' data-input-id='".$sFieldId."' data-show-delete-confirm='".($this->showDeletePickedImageConfirm ? "true" : "false")."'><i class='glyphicon glyphicon-remove' aria-hidden='true'></i></a>";
        $field .= Html::a('<i class=\'glyphicon glyphicon-folder-open\' aria-hidden=\'true\'></i>', '#', [
            'class' => 'input-group-addon btn btn-primary open-modal-imagemanager',
            'data-aspect-ratio' => $this->aspectRatio,
            'data-crop-view-mode' => $this->cropViewMode,
            'data-input-id' => $sFieldId,
            'data-crop-drag-mode' => $this->cropDragMode,
        ]);
        $field .= "</div>";

        //show preview if is true
        if($this->showPreview == true){
            $sHideClass = ($mImageManager == null) ? "hide" : "";
            $sImageSource = isset($mImageManager->id) ? \Yii::$app->imagemanager->getImagePath($mImageManager->id, 500, 500, 'inset') : "";

            $field .= '<div class="image-wrapper '.$sHideClass.'">'
                . '<img id="'.$sFieldId.'_image" alt="Thumbnail" class="img-responsive img-preview" src="'.$sImageSource.'">'
                . '</div>';
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
        $sBaseUrl =  Url::to(['/imagemanager/manager']);
        //set base url
        $view->registerJs("imageManagerInput.baseUrl = '".$sBaseUrl."';");
        $view->registerJs("imageManagerInput.message = ".Json::encode([
                'detachWarningMessage' => Yii::t('imagemanager','Are you sure you want to detach the image?'),
            ]).";");
    }

    private function _validateVariables() {
        // Check if the selected string is in the crop modes array
        if (! in_array($this->cropDragMode, $this->_cropDragModes))
            throw new InvalidConfigException("Image Manager Input: Crop drag mode '{$this->cropDragMode}' is not support ");
    }
}
