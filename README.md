Image manager for Yii2
========================

A Yii2 module/widget for upload, manage and cropping images

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require "noam148/yii2-image-manager" "*" 
```
or add

```json
"noam148/yii2-image-manager" : "*"
```

to the require section of your application's `composer.json` file.

* Run the migrate to create the ImageManager table
```
yii migrate --migrationPath=@noam148/imagemanager/migrations
```

* Add a new component in `components` section of your application's configuration file, for example:

```php
'components' => [
    'imagemanager' => [
		'class' => 'noam148\imagemanager\components\ImageManagerGetPath',
		//set media path (outside the web folder is possible)
		'mediaPath' => '/path/where/to/store/images/media/imagemanager',
        //path relative web folder. In case of multiple environments (frontend, backend) add more paths 
        'cachePath' =>  ['assets/images', '../../frontend/web/assets/images'],
		//use filename (seo friendly) for resized images else use a hash
		'useFilename' => true,
		//show full url (for example in case of a API)
		'absoluteUrl' => false,
		'databaseComponent' => 'db' // The used database component by the image manager, this defaults to the Yii::$app->db component
	],
],
```

and in `modules` section, for example:

```php
'modules' => [
	'imagemanager' => [
		'class' => 'noam148\imagemanager\Module',
		//set accces rules ()
		'canUploadImage' => true,
		'canRemoveImage' => function(){
			return true;
		},
		'deleteOriginalAfterEdit' => false, // false: keep original image after edit. true: delete original image after edit
		// Set if blameable behavior is used, if it is, callable function can also be used
		'setBlameableBehavior' => false,
		//add css files (to use in media manage selector iframe)
		'cssFiles' => [
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css',
		],
	],
],
```

Usage
-----
To reach the imagemanager module go to:
```
http://www.example.com/imagemanager
```
![Image manager module](/docs/images/img_doc-image-manager.jpg)
![Image manager module cropper](/docs/images/img_doc-image-manager-crop.jpg)

To load the image picker see below (make sure you have a field in you table where the module can store 'id' of the ImageManager table):

```php
echo $form->field($model, 'ImageManager_id_avatar')->widget(\noam148\imagemanager\components\ImageManagerInputWidget::className(), [
	'aspectRatio' => (16/9), //set the aspect ratio
    'cropViewMode' => 1, //crop mode, option info: https://github.com/fengyuanchen/cropper/#viewmode
	'showPreview' => true, //false to hide the preview
	'showDeletePickedImageConfirm' => false, //on true show warning before detach image
]);
```
![Image widget](/docs/images/img_doc-image-widget.jpg)
![Image widget popup](/docs/images/img_doc-image-widget-popup.jpg)

If you want to use a image:

```php
/*
 * $ImageManager_id (id that is store in the ImageManager table)
 * $width/$height width height of the image
 * $thumbnailMode: "outbound", "inset" or "{horz}:{vert}" where {horz} is one from "left", "center", "right" and {vert} is one from "top", "center", "bottom"
 */
\Yii::$app->imagemanager->getImagePath($ImageManager_id, $width, $height,$thumbnailMode)
```

Support CKEditor & TinyMce
-----
For using the filebrowser in CKEditor add the filebrowserImageBrowseUrl to the clientOptions of the CKEditor widget. I test it only for the CKEditor from 2amigOS but it need to work on other CKEditor widgets.

```php
use dosamigos\ckeditor\CKEditor;

 echo $form->field($model, 'text')->widget(CKEditor::className(), [
	'options' => ['rows' => 6],
	'preset' => 'basic',
	'clientOptions' => [
		'filebrowserImageBrowseUrl' => yii\helpers\Url::to(['imagemanager/manager', 'view-mode'=>'iframe', 'select-type'=>'ckeditor']),
	]
]);
```

For using the filebrowser in TinyMce add the file_browser_callback to the clientOptions of the TinyMce widget. I test it only for the TinyMce from 2amigOS but it need to work on other TinyMce widgets. (don't forget add 'image' to your 'plugins' array)

```php
use dosamigos\tinymce\TinyMce;

echo $form->field($model, 'text')->widget(TinyMce::className(), [
	'options' => ['rows' => 6],
	'language' => 'nl',
	'clientOptions' => [
		'file_browser_callback' => new yii\web\JsExpression("function(field_name, url, type, win) {
			window.open('".yii\helpers\Url::to(['imagemanager/manager', 'view-mode'=>'iframe', 'select-type'=>'tinymce'])."&tag_name='+field_name,'','width=800,height=540 ,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no');
		}"),
		'plugins' => [
			"advlist autolink lists link charmap print preview anchor",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu paste image"
		],
		'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
	]
]);
```	

**If you got questions, tips or feedback? Please, let me know!**