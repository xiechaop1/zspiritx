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

class StoryStages extends \common\models\StoryStages
{

    public function rules()
    {
        return [
            [['lat', 'lng', 'show_x', 'show_y', 'show_z' ], 'number'],
            [['story_id', 'timebegin', 'timeend', 'rate', 'scan_type', 'pre_stage_id', 'misrange', 'sort_by', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['scan_image_id', 'stage_u_id', 'stage_name'], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryStages::find();
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