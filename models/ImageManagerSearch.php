<?php

namespace noam148\imagemanager\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use noam148\imagemanager\models\ImageManager;
use noam148\imagemanager\Module;

/**
 * ImageManagerSearch represents the model behind the search form about `common\modules\imagemanager\models\ImageManager`.
 */
class ImageManagerSearch extends ImageManager
{
	public $globalSearch;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['globalSearch'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ImageManager::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pagesize' => 100,
			],
			'sort'=> ['defaultOrder' => ['created'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Get the module instance
        $module = Module::getInstance();

        if ($module->setBlameableBehavior) {
            $query->andWhere(['createdBy' => Yii::$app->user->id]);
        }

        $query->andFilterWhere([
            'or',
            ['like', 'fileName', $this->globalSearch],
            ['like', 'DATE_FORMAT(created, "%Y-%m-%d")', $this->globalSearch],
            ['like', 'DATE_FORMAT(modified, "%Y-%m-%d")', $this->globalSearch]
        ]);

        return $dataProvider;
    }
}
