<?php
namespace noam148\imagemanager\assets;
use yii\web\AssetBundle;

/**
 * ImageManagerModuleAsset.
 */
class ImageManagerModuleAsset extends AssetBundle
{
    public $sourcePath = '@vendor/noam148/yii2-image-manager/assets/source';
    public $css = [
		'css/cropper.min.css',
		'css/imagemanager.module.css',
    ];
    public $js = [
        'js/cropper.min.js',
		'js/script.imagemanager.module.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}