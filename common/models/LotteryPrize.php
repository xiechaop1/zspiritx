<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:37 PM
 */

namespace common\models;


class LotteryPrize extends \common\models\gii\LotteryPrize
{
    const PRIZE_TYPE_GOODS = 1; // 实物
    const PRIZE_TYPE_COUPON = 2; // 优惠券
    const PRIZE_TYPE_MONEY = 3; // 现金
    const PRIZE_TYPE_VIRTUALLY = 4; // 虚拟物品
    const PRIZE_TYPE_OTHER = 99; // 其他

    public static $prizeType2Name = [
        self::PRIZE_TYPE_GOODS => '实物',
        self::PRIZE_TYPE_COUPON => '优惠券',
        self::PRIZE_TYPE_MONEY => '现金',
        self::PRIZE_TYPE_VIRTUALLY => '虚拟物品',
        self::PRIZE_TYPE_OTHER => '其他',
    ];

    const PRIZE_STATUS_NORMAL = 1; // 正常
    const PRIZE_STATUS_OFFLINE = 2; // 下线

    public static $lotteryPrizeStatus2Name = [
        self::PRIZE_STATUS_NORMAL => '正常',
        self::PRIZE_STATUS_OFFLINE => '下线',
    ];

    const INTERVAL_TYPE_DAY = 1; // 天
    const INTERVAL_TYPE_HOUR = 2; // 小时

    public static $intervalType2Name = [
        self::INTERVAL_TYPE_DAY => '天',
        self::INTERVAL_TYPE_HOUR => '小时',
    ];

    const PRIZE_METHOD_UNIQUE = 2;    // 唯一
    const PRIZE_METHOD_REPEAT = 1;    // 可重复

    public static $prizeMethod2Name = [
        self::PRIZE_METHOD_REPEAT => '可重复',
        self::PRIZE_METHOD_UNIQUE => '唯一',

    ];


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    // 获取用户信息
    public function getStory()
    {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }

    public function getLottery()
    {
        return $this->hasOne('common\models\Lottery', ['id' => 'lottery_id']);
    }


}