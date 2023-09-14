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
            [['lat', 'lng', 'show_x', 'show_y', 'show_z' ], 'number'],
            [['is_unique', 'story_stage_id', 'story_id',
                'building_id', 'poi_id', 'timebegin', 'timeend',
                'rate', 'scan_type', 'pre_story_model_id', 'model_id',
                'misrange', 'act_misrange', 'direction', 'sort_by', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['scan_image_id', 'model_inst_u_id'], 'string'],
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

        return $dataProvider;
    }
}