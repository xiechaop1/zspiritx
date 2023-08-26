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

class UserQa extends \common\models\UserQa
{

    public function rules()
    {
        return [
            [['user_id', 'session_id', 'story_id','qa_id', 'is_right', 'created_at', 'updated_at'], 'integer'],
            [['answer', ], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\UserQa::find();
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


        return $dataProvider;
    }
}