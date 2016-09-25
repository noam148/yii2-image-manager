<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use kartik\file\FileInput;

$this->title = "Image manager";

?>
<div id="module-imagemanager" class="container-fluid">
	<div class="row">
		<div class="col-xs-6 col-sm-10 col-image-editor">
			<div class="image-cropper">
				<div class="image-wrapper">
					<img id="image-cropper" />
				</div>
				<div class="action-buttons">
					<a href="#" class="btn btn-primary apply-crop">
						<i class="fa fa-crop"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop')?></span>
					</a>
					<a href="#" class="btn btn-default cancel-crop">
						<i class="fa fa-undo"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Cancel')?></span>
					</a>
				</div>
			</div> 
		</div>
		<div class="col-xs-6 col-sm-10 col-overview">
			<?php Pjax::begin([
				'id'=>'pjax-mediamanager',
				'timeout'=>'5000'
			]); ?>    
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemOptions' => ['class' => 'item img-thumbnail'],
				'layout' => "<div class='item-overview'>{items}</div> {pager}",
				'itemView' => function ($model, $key, $index, $widget) {
					return $this->render("_item", ['model' => $model]);
				},
			]) ?>
			<?php Pjax::end(); ?>
		</div>
		<div class="col-xs-6 col-sm-2 col-options">
			<div class="form-group">
				<?=Html::textInput('input-mediamanager-search', null, ['id'=>'input-mediamanager-search', 'class'=>'form-control', 'placeholder'=>Yii::t('imagemanager','Search').'...'])?>
			</div>
			
			<?=FileInput::widget([
				'name' => 'imagemanagerFiles[]',
				'id' => 'imagemanager-files',
				'options' => [
					'multiple' => true,
					'accept' => 'image/*'
				],
				'pluginOptions' => [
					'uploadUrl' => Url::to(['manager/upload']),
					'allowedFileExtensions' => \Yii::$app->controller->module->allowedFileExtensions, 
					'uploadAsync' => false,
					'showPreview' => false,
					'showRemove' => false,
					'showUpload' => false,
					'showCancel' => false,
					'browseClass' => 'btn btn-primary btn-block',
					'browseIcon' => '<i class="fa fa-upload"></i> ',
					'browseLabel' => 'Upload'
				],
				'pluginEvents' => [
					"filebatchselected" => "function(event, files){  $('.msg-invalid-file-extension').addClass('hide'); $(this).fileinput('upload'); }",
					"filebatchuploadsuccess" => "function(event, data, previewId, index) {
						imageManagerModule.uploadSuccess(data.jqXHR.responseJSON.imagemanagerFiles);
					}",
					"fileuploaderror" => "function(event, data) { $('.msg-invalid-file-extension').removeClass('hide'); }",
				],
			])?>
			
		
			<div class="image-info hide">
				<div class="thumbnail">
					<img src="#">
				</div>
				<div class="edit-buttons">
					<a href="#" class="btn btn-primary btn-block crop-image-item">
						<i class="fa fa-crop"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop')?></span>
					</a>
				</div>
				<div class="details">
					<div class="fileName"></div>
					<div class="created"></div>
					<div class="fileSize"></div>
					<div class="dimensions"><span class="dimension-width"></span> × <span class="dimension-height"></span></div>
					<a href="#" class="text-danger delete-image-item" ><?=Yii::t('imagemanager','Delete')?></a>
				</div>
				<?php if($viewMode === "iframe"): ?>
				<a href="#" class="btn btn-primary btn-block pick-image-item"><?=Yii::t('imagemanager','Select')?></a> 
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>