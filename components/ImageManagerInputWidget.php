<?php

namespace gromovfjodor\imagemanager\components;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use gromovfjodor\imagemanager\models\ImageManager;
use gromovfjodor\imagemanager\assets\ImageManagerInputAsset;

class ImageManagerInputWidget extends InputWidget
{

    /**
     * @var null|integer The aspect ratio the image needs to be cropped in (optional)
     */
    public $aspectRatio = null; //option info: https://github.com/fengyuanchen/cropper/#aspectratio

    /**
     * @var int Define the viewMode of the cropper
     */
    public $cropViewMode = 1; //option info: https://github.com/fengyuanchen/cropper/#viewmode

    /**
     * @var bool Show a preview of the image under the input
     */
    public $showPreview = true;

    /**
     * @var bool Show a confirmation message when de-linking a image from the input
     */
    public $showDeletePickedImageConfirm = false;

    /**
     * @var bool Скрыть кнопку очистки изображения
     */
    public $hideDeleteButton = false;

    /**
     * @var bool Автоматически показывать выбор изображения при отображении страницы
     */
    public $autoOpenModal = false;

    /**
     * @var string|null $id Идентификатор элемента
     */
    public $id = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        //set language
        if (!isset(Yii::$app->i18n->translations['imagemanager'])) {
            Yii::$app->i18n->translations['imagemanager'] = [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath'       => '@gromovfjodor/imagemanager/messages',
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        //default
        $ImageManager_id = null;
        $mImageManager   = null;
        $sFieldId        = $this->id;
        //start input group
        $field = "<div class='image-manager-input'>";
        $field .= "<div class='input-group'>";
        //set input fields
        if ($this->hasModel()) {
            //get field id
            $sFieldId     = Html::getInputId($this->model, $this->attribute);
            $sFieldNameId = $sFieldId . "_name";
            //get attribute name
            $sFieldAttributeName = Html::getAttributeName($this->attribute);
            //get filename from selected file
            $ImageManager_id       = $this->model->{$sFieldAttributeName};
            $ImageManager_fileName = null;
            $mImageManager         = ImageManager::findOne($ImageManager_id);
            if ($mImageManager !== null) {
                $ImageManager_fileName = $mImageManager->fileName;
            }

            //show preview if is true
            if ($this->showPreview == true) {
                $sHideClass   = ($mImageManager == null) ? "hide" : "";
                $sImageSource = isset($mImageManager->id) ? \Yii::$app->imagemanager->getImagePath($mImageManager->id, 500, 500, 'inset') : "";

                $field .= '<div class="image-wrapper col-12 ' . $sHideClass . '">'
                    . '<img id="' . $sFieldId . '_image" alt="Thumbnail" class="img-responsive img-preview" src="' . $sImageSource . '">'
                    . '</div>';
            }

            //create field
            $field .= Html::textInput($this->attribute, $ImageManager_fileName, ['class' => 'form-control', 'id' => $sFieldNameId, 'readonly' => true]);
            $field .= Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $field .= Html::textInput($this->name . "_name", null, ['readonly' => true]);
            $field .= Html::hiddenInput($this->name, $this->value, $this->options);
        }
        //end input group
        $sHideClass = $ImageManager_id === null ? 'hide' : '';
        if (!$this->hideDeleteButton) {
            $field .= "<span class='input-group-addon btn btn-clear hide btn-danger delete-selected-image" . $sHideClass . "' data-input-id='" . $sFieldId . "' data-show-delete-confirm='" . ($this->showDeletePickedImageConfirm ? "true" : "false") . "'></span>";
        }
        // auto open class
        $autoOpen = $this->autoOpenModal ? 'auto-open-modal-imagemanager' : '';
        $field .= "<span class='input-group-addon btn btn-open btn-primary open-modal-imagemanager $autoOpen' data-aspect-ratio='" . $this->aspectRatio . "' data-crop-view-mode='" . $this->cropViewMode . "' data-input-id='" . $sFieldId . "'>";
        $field .= "<i class='glyphicon glyphicon-folder-open' aria-hidden='true'></i>";
        $field .= "</span></div>";

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
        $sBaseUrl = Url::to(['/imagemanager/manager']);
        //set base url
        $view->registerJs("imageManagerInput.baseUrl = '" . $sBaseUrl . "';");
        $view->registerJs("imageManagerInput.message = " . Json::encode([
                'imageManager'         => Yii::t('imagemanager', 'Image manager'),
                'detachWarningMessage' => Yii::t('imagemanager', 'Are you sure you want to detach the image?'),
            ]) . ";");

        if ($this->autoOpenModal) {
            $view->registerJs('
            //open media manager modal
	        $(".auto-open-modal-imagemanager").each(function () {
		        var aspectRatio = $(this).data("aspect-ratio");
		        var cropViewMode = $(this).data("crop-view-mode");
		        var inputId = $(this).data("input-id");
		        //open selector id
		        imageManagerInput.openModal(inputId, aspectRatio, cropViewMode);
	        });
            ');
        }
    }

}
