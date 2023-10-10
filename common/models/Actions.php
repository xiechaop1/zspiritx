<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Actions extends \common\models\gii\Actions
{

    const ACTION_TYPE_ACTION = 1;   // 动作
    const ACTION_TYPE_MSG    = 2;   // 消息
    const ACTION_TYPE_CHANGE_STAGE = 11; // 切换场景

    public static $actionType2Name = [
        self::ACTION_TYPE_ACTION => '动作',
        self::ACTION_TYPE_MSG    => '消息',
        self::ACTION_TYPE_CHANGE_STAGE => '切换场景',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getSender(){
        return $this->hasMany('common\models\User',  ['sender_id' => 'id']);
    }

    public function getTo(){
        return $this->hasMany('common\models\User',  ['to_user' => 'id']);
    }

    public function getSession(){
        return $this->hasOne('common\models\Session', ['id' => 'session_id']);
    }


}