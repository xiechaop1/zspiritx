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

class LotteryPrize extends \common\models\LotteryPrize
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [['prize_name', 'prize_level_name', 'prize_option', 'image', 'thumbnail' ], 'string'],
            [[
                'prize_method', 'total_ct', 'interval_ct', 'interval_type', 'rate', 'prize_type',
                'story_model_id', 'prize_level', 'lottery_id', 'prize_status', 'story_id', 'status'], 'integer'],
            [[ 'created_at', 'updated_at',], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\LotteryPrize::find();
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


        $query->andFilterWhere([
            'like', 'prize_name', $this->prize_name
        ]);

        $query->andFilterWhere([
            'lottery_id' => $this->lottery_id,
        ]);

        $query->andFilterWhere([
            'prize_level' => $this->prize_level,
        ]);

        $query->andFilterWhere([
            'story_id' => $this->story_id,
        ]);

        return $dataProvider;
    }
}