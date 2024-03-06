<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:37 PM
 */

namespace common\models;


class Lottery extends \common\models\gii\Lottery
{

    const LOTTERY_TYPE_TICKET   = 1; // 奖券

    public static $lotteryType2Name = [
        self::LOTTERY_TYPE_TICKET   => '奖券',
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


}