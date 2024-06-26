<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryMatchPlayer extends \common\models\gii\StoryMatchPlayer
{

    const STORY_MATCH_PLAYER_STATUS_PREPARE = 1; // 准备
    const STORY_MATCH_PLAYER_STATUS_MATCHING = 2; // 匹配中
    const STORY_MATCH_PLAYER_STATUS_PLAYING = 3; // 游戏中
    const STORY_MATCH_PLAYER_STATUS_END = 4; // 结束
    const STORY_MATCH_PLAYER_STATUS_CANCEL = 5; // 取消
    const STORY_MATCH_PLAYER_STATUS_TIMEOUT = 6; // 超时
    const STORY_MATCH_PLAYER_STATUS_INJURED = 7; // 受伤
    const STORY_MATCH_PLAYER_STATUS_LOST = 8; // 失败

    public static $storyMatchPlayerStatus2Name = [
        self::STORY_MATCH_PLAYER_STATUS_PREPARE => '准备',
        self::STORY_MATCH_PLAYER_STATUS_MATCHING => '匹配中',
        self::STORY_MATCH_PLAYER_STATUS_PLAYING => '游戏中',
        self::STORY_MATCH_PLAYER_STATUS_END => '结束',
        self::STORY_MATCH_PLAYER_STATUS_CANCEL => '取消',
        self::STORY_MATCH_PLAYER_STATUS_TIMEOUT => '超时',
        self::STORY_MATCH_PLAYER_STATUS_INJURED => '受伤',
        self::STORY_MATCH_PLAYER_STATUS_LOST => '失败',
    ];

    const STORY_MATCH_RESULT_WIN = 1; // 胜利
    const STORY_MATCH_RESULT_LOSE = 2; // 失败
    const STORY_MATCH_RESULT_DRAW = 3; // 平局
    const STORY_MATCH_RESULT_WAITTING = 4; // 等待

    public static $storyMatchResult2Name = [
        self::STORY_MATCH_RESULT_WIN => '胜利',
        self::STORY_MATCH_RESULT_LOSE => '失败',
        self::STORY_MATCH_RESULT_DRAW => '平局',
        self::STORY_MATCH_RESULT_WAITTING => '等待',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getStoryModel(){
        return $this->hasOne('common\models\StoryModels',  ['id' => 'm_story_model_id']);
    }

    public function getUserModel(){
        return $this->hasOne('common\models\UserModels',  ['id' => 'user_model_id']);
    }

    public function getUserModelLoc(){
        return $this->hasOne('common\models\UserModelLoc',  ['id' => 'user_model_id']);
    }

    public function getMatch(){
        return $this->hasOne('common\models\StoryMatch',  ['id' => 'match_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }


}