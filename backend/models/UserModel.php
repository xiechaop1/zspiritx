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

class UserModel extends \common\models\UserModels
{

    public function rules()
    {
        return [
            [['user_id', 'story_model_id', 'story_model_detail_id', 'session_model_id',
                'session_id', 'model_id', 'use_ct', 'is_delete',
                'status', 'created_at', 'updated_at'], 'integer'],
            [['user_model_prop'], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\UserModels::find();
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

        return $dataProvider;
    }
}