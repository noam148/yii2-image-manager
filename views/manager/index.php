<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use kartik\file\FileInput;

$this->title = "Image manager";

?>
<div id="module-imagemanager" class="container-fluid <?=$selectType?>">
	<div class="row">
		<div class="col-xs-6 col-sm-10 col-image-editor">
			<div class="image-editor">
				<div class="image-wrapper">
					<img id="image-cropper" />
				</div>
				<div class="action-buttons">
					<div class="btn-group">
						<button type="button" class="btn btn-primary apply-rotate" data-rotate-direction="left" title="<?=Yii::t('imagemanager','Rotate left')?>">
							<i class="fa fa-rotate-left"></i>
						</button>
						<button type="button" class="btn btn-primary apply-rotate" data-rotate-direction="right" title="<?=Yii::t('imagemanager','Rotate right')?>">
							<i class="fa fa-rotate-right"></i>
						</button>
					</div>
					<a href="#" class="btn btn-primary apply-crop" title="<?=Yii::t('imagemanager','Edit image')?>">
						<i class="fa fa-floppy-o"></i>
					</a>
					<?php if($viewMode === "iframe"): ?>
					<a href="#" class="btn btn-primary apply-crop-select" title="<?=Yii::t('imagemanager','Edit and select')?>">
						<i class="fa fa-floppy-o"></i> <i class="fa fa-check"></i>
					</a>
					<?php endif; ?>
					<a href="#" class="btn btn-default cancel-crop" title="<?=Yii::t('imagemanager','Cancel')?>">
						<i class="fa fa-times"></i>
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
			
			<?php if (Yii::$app->controller->module->canUploadImage): ?>
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
			]) ?>
			<?php endif; ?>

			<div class="image-info hide">
				<div class="thumbnail">
					<img src="#">
				</div>
				<div class="edit-buttons">
					<a href="#" class="btn btn-primary btn-block crop-image-item" title="<?=Yii::t('imagemanager','Edit')?>">
						<i class="fa fa-crop"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Edit')?></span>
					</a>
				</div>
				<div class="details">
					<div class="fileName"></div>
					<div class="created"></div>
					<div class="fileSize"></div>
					<div class="dimensions"><span class="dimension-width"></span> &times; <span class="dimension-height"></span></div>
					<?php if (Yii::$app->controller->module->canRemoveImage): ?>
					<a href="#" class="btn btn-xs btn-danger delete-image-item" title="<?=Yii::t('imagemanager','Delete')?>" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?=Yii::t('imagemanager','Delete')?></a>
					<?php endif; ?>
				</div>
				<?php if($viewMode === "iframe"): ?>
				<a href="#" class="btn btn-primary btn-block pick-image-item" title="<?=Yii::t('imagemanager','Select')?>"><?=Yii::t('imagemanager','Select')?></a> 
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>