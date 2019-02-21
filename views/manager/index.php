<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use kartik\file\FileInput;

$this->title = Yii::t('imagemanager','Image manager');

?>
<div id="module-imagemanager" class="container <?=$selectType?>">

	<div class="row">

		<div class="col-6 col-sm-3 col-md-3 col-image-editor">

			<div class="image-cropper">

				<div class="image-wrapper">

					<img id="image-cropper" />

				</div>

				<div class="action-buttons">

					<a href="#" class="btn btn-primary apply-crop">

						<i class="fa fa-crop"></i>

						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop')?></span>

					</a>

					<?php if($viewMode === "iframe"): ?>

					<a href="#" class="btn btn-primary apply-crop-select">

						<i class="fa fa-crop"></i>

						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop and select')?></span>

					</a>

					<?php endif; ?>

					<a href="#" class="btn btn-default cancel-crop">

						<i class="fa fa-undo"></i>

						<span class="hidden-xs"><?=Yii::t('imagemanager','Cancel')?></span>

					</a>

				</div>

			</div>

		</div>

		<div class="col-6 col-sm-9 col-md-9 col-overview">

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

		<div class="col-6 col-sm-3 col-options">

			<div class="form-group">

				<?=Html::textInput('input-mediamanager-search', null, ['id'=>'input-mediamanager-search', 'class'=>'form-control', 'placeholder'=>Yii::t('imagemanager','Search').'...'])?>

            </div>

			<?php
				if (Yii::$app->controller->module->canUploadImage):
			?>

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
					'browseLabel' => Yii::t('imagemanager','Upload')
				],
				'pluginEvents' => [
					"filebatchselected" => "function(event, files){  $('.msg-invalid-file-extension').addClass('hide'); $(this).fileinput('upload'); }",
					"filebatchuploadsuccess" => "function(event, data, previewId, index) {
						imageManagerModule.uploadSuccess(data.jqXHR.responseJSON.imagemanagerFiles);
					}",
					"fileuploaderror" => "function(event, data) { $('.msg-invalid-file-extension').removeClass('hide'); }",
				],
			]) ?>

			<?php
				endif;
			?>

			<div class="image-info hide">

				<div class="thumbnail">

					<img src="#">

				</div>

				<div class="edit-buttons">

					<a href="#" class="btn btn-primary btn-block crop-image-item">

						<i class="fa fa-crop"></i>

						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop')?></span>

					</a>

                    <?php if (Yii::$app->controller->module->canRemoveImage):  ?>
                        <a href="#" class="btn btn-xs btn-danger delete-image-item" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?=Yii::t('imagemanager','Delete')?></a>
                    <?php
                         endif;
                    ?>

				</div>

				<div class="details">

					<div class="fileName"></div>

					<div class="created"></div>

					<div class="fileSize"></div>

					<!--<div class="dimensions"><span class="dimension-width"></span> &times; <span class="dimension-height"></span></div>-->

				</div>

				<?php if($viewMode === "iframe"): ?>

				<a href="#" class="btn btn-primary btn-block pick-image-item"><?=Yii::t('imagemanager','Select')?></a>

				<?php endif; ?>
			</div>

		</div>

	</div>

</div>