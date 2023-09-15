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

class SessionModels extends \common\models\SessionModels
{

    public function rules()
    {
        return [
            [['story_model_id', 'story_stage_id', 'session_id', 'model_id', 'is_unique',
//                'is_pickup', 'pre_story_model_id',
                'last_operator_id', 'session_model_status', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['snapshot'], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\SessionModels::find();
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

        if (!empty($this->session_id)) {
            $query->andFilterWhere(['session_id' => $this->session_id]);
        }

        return $dataProvider;
    }
}