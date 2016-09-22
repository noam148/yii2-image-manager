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
		'class' => '\noam148\imagemanager\components\ImageManagerGetPath',
		//set media path (outside the web folder is possible)
		'mediaPath' => '/path/where/to/store/images/media/imagemanager', 
		//if run the component from the frontend and you wan't to reach the file from the backend. Set the path (optional)
		'baseUrlPublishedMedia' => 'http://www.example.com/backend',
	],
],
```

and in `modules` section, for example:

```php
'modules' => [
	'imagemanager' => [
		'class' => '\noam148\imagemanager\Module',
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
	'showPreview' => true, //false to hide the preview
]);
```
![Image widget](/docs/images/img_doc-image-widget.jpg)
![Image widget popup](/docs/images/img_doc-image-widget-popup.jpg)

If you want to use a image:

```php
/*
 * $ImageManager_id (id that is store in the ImageManager table)
 * $width/$height width height of the image
 * $thumbnailMode = "outbound" or "inset"
 */
\Yii::$app->imagemanager->getImagePath($ImageManager_id, $width, $height,$thumbnailMode)
```


**If you got questions, tips or feedback? Please, let me know!**