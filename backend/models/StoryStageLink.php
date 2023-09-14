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

class StoryStageLink extends \common\models\StoryStageLink
{

    public function rules()
    {
        return [
            [['story_stage_id', 'pre_story_stage_id', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryStageLink::find();
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

        if (!empty($this->story_stage_id)) {
            $query->andFilterWhere(['story_stage_id' => $this->story_stage_id]);
        }

        if (!empty($this->pre_story_stage_id)) {
            $query->andFilterWhere(['pre_story_stage_id' => $this->pre_story_stage_id]);
        }

        return $dataProvider;
    }
}