<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserKnowledge extends \common\models\gii\UserKnowledge
{
    const KNOWLDEGE_STATUS_INIT = 0;        // 初始化
    const KNOWLDEGE_STATUS_PROCESS = 1;     // 进行中
    const KNOWLDEGE_STATUS_COMPLETE = 2;    // 完成

    const KNOWLDEGE_STATUS_REMOVE = 99;     // 删除

    public static $knowledgeStatus2Name = [
        self::KNOWLDEGE_STATUS_INIT => '未开始',
        self::KNOWLDEGE_STATUS_PROCESS => '进行中',
        self::KNOWLDEGE_STATUS_COMPLETE => '已完成',
        self::KNOWLDEGE_STATUS_REMOVE   => '已删除',
    ];

    const KNOWLEDGE_IS_READ_YES = 1;     // 已读
    const KNOWLEDGE_IS_READ_NO = 0;      // 未读

    public static $knowledgeIsRead2Name = [
        self::KNOWLEDGE_IS_READ_YES => '已读',
        self::KNOWLEDGE_IS_READ_NO => '未读',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getSession(){
        return $this->hasOne('common\models\Session', ['id' => 'session_id']);
    }

    public function getKnowledge(){
        return $this->hasOne('common\models\Knowledge', ['id' => 'knowledge_id']);
    }

    public function getUser() {
        return $this->hasOne('common\models\User', ['id' => 'user_id']);
    }


}