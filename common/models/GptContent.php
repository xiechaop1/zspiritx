<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class GptContent extends \common\models\gii\GptContent
{

    const MSG_TYPE_TEXT = 1; // 文本

    const MSG_CLASS_NORMAL = 1; // 普通
    const MSG_CLASS_STORY_CREATOR = 2; // 故事创建者
    const MSG_CLASS_PUZZLE = 3; // 谜题
    const MSG_CLASS_GUESS_BY_DESCRIPTION = 4; // 描述猜东西
    const MSG_CLASS_NISHUOWOCAI_HOST = 51; // 你说我猜
    const MSG_CLASS_NISHUOWOCAI_PLAYER = 52; // 你说我猜玩家

    public static $msgClass2Name = [
        self::MSG_CLASS_NORMAL => '普通',
        self::MSG_CLASS_STORY_CREATOR => '故事创建者',
        self::MSG_CLASS_PUZZLE => '猜谜语',
        self::MSG_CLASS_GUESS_BY_DESCRIPTION => '描述猜东西',
        self::MSG_CLASS_NISHUOWOCAI_HOST => '你说我猜主持人',
        self::MSG_CLASS_NISHUOWOCAI_PLAYER => '你说我猜玩家',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getTouser(){
        return $this->hasOne('common\models\User',  ['id' => 'to_user_id']);
    }

    public function getSender(){
        return $this->hasOne('common\models\User',  ['id' => 'sender_id']);
    }

}