<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class User extends \common\models\gii\User
{

    const USER_STATUS_NORMAL    = 0;    // 正常
    const USER_STATUS_FORBIDDEN = 1;    // 禁用
    const USER_STATUS_DELETED   = 99;   // 删除
//    const USER_STATUS_INVITED   = 2;    // 被邀请

    public static $userStatus = [
        self::USER_STATUS_NORMAL    => '正常',
        self::USER_STATUS_FORBIDDEN => '禁用',
        self::USER_STATUS_DELETED   => '删除',
//        self::USER_STATUS_INVITED   => '未激活',
    ];

    const USER_TYPE_NORMAL = 1; // 普通用户
    const USER_TYPE_INNER  = 2; // 内部用户

    public static $userTypeNameMap = [
        self::USER_TYPE_NORMAL => '普通用户',
        self::USER_TYPE_INNER  => '内部用户',
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

    public function getExtends()
    {
        return $this->hasOne(UserExtends::className(), ['user_id' => 'id']);
    }
}