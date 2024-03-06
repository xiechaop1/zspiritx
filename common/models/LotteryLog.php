<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:37 PM
 */

namespace common\models;


class LotteryLog extends \common\models\gii\LotteryLog
{


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    // 获取用户信息
    public function getUser()
    {
        return $this->hasOne('common\models\User', ['id' => 'user_id']);
    }

    public function getStory()
    {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }

    public function getPrize()
    {
        return $this->hasOne('common\models\LotteryPrize', ['id' => 'prize_id']);
    }


}