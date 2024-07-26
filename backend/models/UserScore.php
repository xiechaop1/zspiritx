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

class UserScore extends \common\models\UserScore
{
    public $date_range;

    public $user_name;
    public $mobile;

    public function rules()
    {
        return [
            [['user_id', 'story_id', 'session_id', 'team_id', 'score', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\UserScore::find();
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

        if (!empty($this->user_id)) {
            $query->andWhere(['user_id' => $this->user_id]);
        }


        if (!empty($this->story_id)) {
            $query->andWhere(['story_id' => $this->story_id]);
        }


        return $dataProvider;
    }
}