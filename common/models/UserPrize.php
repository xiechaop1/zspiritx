<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserPrize extends \common\models\gii\UserPrize
{

    const USER_PRIZE_STATUS_PREPARING = 0; // 准备中
    const USER_PRIZE_STATUS_WAIT = 1; // 待领取
    const USER_PRIZE_STATUS_RECEIVED = 2; // 已领取
    const USER_PRIZE_STATUS_EXPIRED = 3; // 已过期
    const USER_PRIZE_STATUS_CANCEL = 99; // 已取消

    public static $userPrizeStatus2Name = [
        self::USER_PRIZE_STATUS_PREPARING => '准备中',
        self::USER_PRIZE_STATUS_WAIT => '待领取',
        self::USER_PRIZE_STATUS_RECEIVED => '已领取',
        self::USER_PRIZE_STATUS_EXPIRED => '已过期',
        self::USER_PRIZE_STATUS_CANCEL => '已取消',
    ];


    public static $normalUserPrizeStatus = [
        self::USER_PRIZE_STATUS_PREPARING,
        self::USER_PRIZE_STATUS_WAIT,
        self::USER_PRIZE_STATUS_RECEIVED,
    ];

    public static $allUserPrizeStatus = [
        self::USER_PRIZE_STATUS_PREPARING,
        self::USER_PRIZE_STATUS_WAIT,
        self::USER_PRIZE_STATUS_RECEIVED,
        self::USER_PRIZE_STATUS_EXPIRED,
        self::USER_PRIZE_STATUS_CANCEL,
    ];


    const USER_PRIZE_AWARD_METHOD_ONLINE = 1; // 线上
    const USER_PRIZE_AWARD_METHOD_OFFLINE = 2; // 线下
    const USER_PRIZE_AWARD_METHOD_SELF = 5; // 自提
    const USER_PRIZE_AWARD_METHOD_EXPRESS = 10; // 快递

    public static $userPrizeAwardMethod2Name = [
        self::USER_PRIZE_AWARD_METHOD_ONLINE => '线上',
        self::USER_PRIZE_AWARD_METHOD_OFFLINE => '线下',
        self::USER_PRIZE_AWARD_METHOD_SELF => '自提',
        self::USER_PRIZE_AWARD_METHOD_EXPRESS => '快递',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getPrize(){
        return $this->hasOne('common\models\LotteryPrize',  ['id' => 'prize_id']);
    }

    public function getLottery(){
        return $this->hasOne('common\models\Lottery',  ['id' => 'lottery_id']);
    }

    public function getUserLottery(){
        return $this->hasOne('common\models\UserLottery',  ['id' => 'user_lottery_id']);
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