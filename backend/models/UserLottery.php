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

class UserLottery extends \common\models\UserLottery
{
    public $date_range;

    public function rules()
    {
        return [
            [[
                'user_id', 'lottery_id', 'expire_time', 'ct',
                'lottery_id', 'lottery_status', 'story_id', 'session_id', 'channel_id', 'status'], 'integer'],
            [[ 'created_at', 'updated_at',], 'integer'],
            [['lottery_no'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\UserLottery::find();
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

        if (!empty($this->lottery_no)) {
            $query->andWhere(['lottery_no' => $this->lottery_no]);

        }

        if (!empty($this->lottery_status)) {
            $query->andWhere(['lottery_status' => $this->lottery_status]);
        }


        return $dataProvider;
    }
}