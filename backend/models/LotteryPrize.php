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

class Lottery extends \common\models\Lottery
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [['lottery_name', ], 'string'],
            [['story_id', 'status'], 'integer'],
            [[ 'created_at', 'updated_at',], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\Lottery::find();
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
            'like', 'lottery_name', $this->lottery_name
        ]);

        $query->andFilterWhere([
            'story_id' => $this->story_id,
        ]);

        return $dataProvider;
    }
}