<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/09/02
 * Time: 下午5:30
 */

namespace backend\models;


use common\definitions\Common;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StoryModelsLink extends \common\models\StoryModelsLink
{

    public function rules()
    {
        return [
            [['story_model_id', 'story_model_id2', 'story_id'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryModelsLink::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => false
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->story_id)) {
            $query->andFilterWhere(['story_id' => $this->story_id]);
        }

        if (!empty($this->story_model_id)) {
            $query->andFilterWhere(['story_model_id' => $this->story_model_id]);
        }

        if (!empty($this->story_model_id2)) {
            $query->andFilterWhere(['story_model_id2' => $this->story_model_id2]);
        }

        return $dataProvider;
    }
}