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

class UserStory extends \common\models\UserStory
{


    public function rules()
    {
        return [
            [['user_id', 'role_id', 'story_id', 'session_id', 'team_id', 'building_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goal', 'goal_correct'], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\UserStory::find();
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

        return $dataProvider;
    }
}