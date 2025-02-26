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

class StoryModels extends \common\models\StoryModels
{

    public function rules()
    {
        return [
            [['lat', 'lng', 'show_x', 'show_y', 'show_z', 'price', 'misrange', 'trigger_misrange', 'act_misrange', 'scale' ], 'number'],
            [['is_unique', 'is_visable', 'is_placing_hint', 'use_allow', 'selected_permission', 'namecard_display',
                'is_undertake', 'undertake_alive_timeout', 'undertake_trigger_timeout',
                'story_stage_id', 'story_id', 'story_model_detail_id', 'story_model_class', 'pos_story_model_id',
                'building_id', 'poi_id', 'timebegin', 'timeend', 'is_random',
                'rate', 'scan_type', 'set_type', 'pre_story_model_id', 'model_id',
                'active_type', 'direction', 'sort_by', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['active_model_inst_u_id', 'target_model_u_id', 'scan_image_id', 'scan_image_path', 'model_inst_u_id',
                'active_next', 'story_model_name', 'story_model_desc', 'posing', 'dialog', 'dialog2', 'story_model_prop',
                'model_group', 'use_group_name', 'story_model_image', 'icon', 'story_model_html',
//                'story_model_config',
                ], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryModels::find();
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

        if (!empty($this->story_model_name)) {
            $query->andFilterWhere(['like', 'story_model_name', $this->story_model_name]);
        }

        if (!empty($this->model_inst_u_id)) {
            $query->andFilterWhere(['like', 'model_inst_u_id', $this->model_inst_u_id]);
        }

        if (!empty($this->model_id)) {
            $query->andFilterWhere(['model_id' => $this->model_id]);
        }

        if (!empty($this->story_stage_id)) {
            $query->andFilterWhere(['story_stage_id' => $this->story_stage_id]);
        }

        if (!empty($this->story_model_detail_id)) {
            $query->andFilterWhere(['story_model_detail_id' => $this->story_model_detail_id]);
        }

        if (!empty($this->model_group)) {
            $query->andFilterWhere(['like', 'model_group', $this->model_group]);
        }

        if (!empty($this->dialog)) {
            $query->andFilterWhere(['like', 'dialog', $this->dialog]);
        }

        return $dataProvider;
    }
}