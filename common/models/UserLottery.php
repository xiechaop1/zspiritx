<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserLottery extends \common\models\gii\UserLottery
{

    const USER_LOTTERY_STATUS_WAIT = 0; // 待抽奖
    const USER_LOTTERY_STATUS_USED = 1; // 已抽奖

    const USER_LOTTERY_STATUS_EXPIRED = 3; // 已过期
    const USER_LOTTERY_STATUS_CANCEL = 99; // 已取消

    public static $userLotteryStatus2Name = [
        self::USER_LOTTERY_STATUS_WAIT => '待抽奖',
        self::USER_LOTTERY_STATUS_USED => '已抽奖',
        self::USER_LOTTERY_STATUS_EXPIRED => '已过期',
        self::USER_LOTTERY_STATUS_CANCEL => '已取消',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getLottery(){
        return $this->hasOne('common\models\Lottery',  ['id' => 'lottery_id']);
    }

    public function getStory() {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getSession(){
        return $this->hasOne('common\models\Session',  ['id' => 'session_id']);
    }


}