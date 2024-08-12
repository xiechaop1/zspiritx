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

class StoryModelSpecialEff extends \common\models\StoryModelSpecialEff
{

    public function rules()
    {
        return [
            [['story_id', 'model_id', 'level', 'eff_class', 'during_ti', 'cd', 'eff_mode', 'link_story_model_id', 'own_story_model_id', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['special_eff_name', 'special_eff_desc', 'icon', 'model_inst_u_id', 'prop', ], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryModelSpecialEff::find();
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

        if (!empty($this->model_id)) {
            $query->andFilterWhere(['model_id' => $this->model_id]);
        }

        if (!empty($this->link_story_model_id)) {
            $query->andFilterWhere(['link_story_model_id' => $this->link_story_model_id]);
        }

        if (!empty($this->own_story_model_id)) {
            $query->andFilterWhere(['own_story_model_id' => $this->own_story_model_id]);
        }

        return $dataProvider;
    }
}