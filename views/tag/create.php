<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model noam148\imagemanager\models\ImageManagerTag */

$this->title = Yii::t('app', 'Create Image Manager Tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Image Manager Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-manager-tag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
