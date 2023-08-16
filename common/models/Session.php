<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Session extends \common\models\gii\Session
{

    const SESSION_STATUS_INIT   = 1;    // 初始化
    const SESSION_STATUS_READY  = 2;    // 准备
    const SESSION_STATUS_START  = 3;    // 开始
    const SESSION_STATUS_END    = 4;    // 结束

    public static $sessionStats2Name = [
        self::SESSION_STATUS_INIT   => '初始化',
        self::SESSION_STATUS_START  => '开始',
        self::SESSION_STATUS_END    => '结束',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function fields()
    {
        return [
            'id',
            'team_name',
        ];
    }

    public function attributeLabels()
    {
        return [
            'session_name' => 'Session Name',
        ];
    }
}