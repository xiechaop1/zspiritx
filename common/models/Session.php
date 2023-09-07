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
    const SESSION_STATUS_FINISH = 4;    // 结束
    const SESSION_STATUS_CANCEL = 9;    // 取消

    public static $sessionStats2Name = [
        self::SESSION_STATUS_INIT   => '初始化',
        self::SESSION_STATUS_READY  => '准备',
        self::SESSION_STATUS_START  => '开始',
        self::SESSION_STATUS_FINISH    => '结束',
        self::SESSION_STATUS_CANCEL    => '取消',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getUsers(){
        return $this->hasMany('common\models\UserStory',  ['session_id' => 'id']);
    }

    public function getTeams(){
        return $this->hasMany('common\models\Team',  ['session_id' => 'id']);
    }

    public function getStory(){
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }
    
    public function getCreator() {
        return $this->hasOne('common\models\User', ['id' => 'user_id']);
    }

}