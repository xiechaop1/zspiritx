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

class StoryExtend extends \common\models\StoryExtend
{


    public function rules()
    {
        return [
            [['price', 'curr_price', ], 'number'],
            [['story_id', 'buy_ct', 'com_ct', 'open_ct', 'status'], 'integer'],
            [[ 'created_at', 'updated_at',], 'integer'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryExtend::find();
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