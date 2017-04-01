<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel noam148\imagemanager\models\ImageManagerTagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Image Manager Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-manager-tag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Image Manager Tag'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			'id',
            'name',
			[
                'attribute' => 'used',
				'value' => function($data){return $data->tagUsedCount.'x';},
            ],
            'created:datetime',
            'modified:datetime',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
