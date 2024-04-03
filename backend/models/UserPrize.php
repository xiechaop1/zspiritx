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

class UserPrize extends \common\models\UserPrize
{
    public $date_range;

    public function rules()
    {
        return [
            [[
                'user_id', 'prize_id', 'prize_type', 'award_method', 'prize_type', 'expire_time',
                'lottery_id', 'user_prize_status', 'story_id', 'session_id', 'channel_id', 'status'], 'integer'],
            [['extend_info','user_prize_no', ] , 'string'],
            [[ 'created_at', 'updated_at',], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\UserPrize::find();
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

        if (!empty($this->lottery_id)) {
            $query->andWhere(['lottery_id' => $this->lottery_id]);
        }

        if (!empty($this->story_id)) {
            $query->andWhere(['story_id' => $this->story_id]);
        }

        if (!empty($this->session_id)) {
            $query->andWhere(['session_id' => $this->session_id]);
        }

        if (!empty($this->user_prize_no)) {
            $query->andWhere(['like', 'user_prize_no', $this->user_prize_no]);

        }

        if (!empty($this->prize_id)) {
            $query->andWhere(['prize_id' => $this->prize_id]);

        }

        if (!empty($this->user_prize_status)) {
            $query->andWhere(['user_prize_status' => $this->user_prize_status]);
        }


        return $dataProvider;
    }
}