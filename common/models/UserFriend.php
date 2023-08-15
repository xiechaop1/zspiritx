<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class UserFriend extends \common\models\gii\UserFriend
{

    const USER_FRIEND_STATUS_INVITE     = 0;    // 邀请中
    const USER_FRIEND_STATUS_NORMAL     = 1;    // 正常
//    const USER_FRIEND_STATUS_INVITED    = 2;    // 被邀请
    const USER_FRIEND_STATUS_BLACK      = 10;   // 黑名单
    const USER_FRIEND_STATUS_DELETE     = 20;   // 删除

    public static $userStatus = [
        self::USER_FRIEND_STATUS_INVITE     => '邀请中',
        self::USER_FRIEND_STATUS_NORMAL     => '正常',
        self::USER_FRIEND_STATUS_BLACK      => '黑名单',
    ];
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function exec() {

        $ret = $this->save();
        return $ret;
    }
}