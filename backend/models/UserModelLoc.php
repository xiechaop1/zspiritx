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

class UserModelLoc extends \common\models\UserModelLoc
{

    public function rules()
    {
        return [
            [['story_id', 'user_id', 'session_id',
                'user_model_id', 'story_model_id', 'location_id', ], 'integer'],
            [['is_delete', 'user_model_loc_status', 'created_at', 'updated_at',], 'integer'],
            [['user_model_prop', 'amap_poi_id', ], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\UserModelLoc::find();
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

        if (!empty($this->session_id)) {
            $query->andFilterWhere(['session_id' => $this->session_id]);
        }

        if (!empty($this->user_id)) {
            $query->andFilterWhere(['user_id' => $this->user_id]);
        }

        if (!empty($this->story_model_id))  {
            $query->andFilterWhere(['story_model_id' => $this->story_model_id]);
        }

        if (!empty($this->location_id))  {
            $query->andFilterWhere(['location_id' => $this->location_id]);
        }

        return $dataProvider;
    }
}