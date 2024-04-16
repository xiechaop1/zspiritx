<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryMatch extends \common\models\gii\StoryMatch
{

    const STORY_MATCH_STATUS_PREPARE = 1; // 准备
    const STORY_MATCH_STATUS_MATCHING = 2; // 匹配中
    const STORY_MATCH_STATUS_PLAYING = 3; // 游戏中
    const STORY_MATCH_STATUS_END = 4; // 结束

    public static $storyMatchStatus2Name = [
        self::STORY_MATCH_STATUS_PREPARE => '准备',
        self::STORY_MATCH_STATUS_MATCHING => '匹配中',
        self::STORY_MATCH_STATUS_PLAYING => '游戏中',
        self::STORY_MATCH_STATUS_END => '结束',
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

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getSession(){
        return $this->hasOne('common\models\Session',  ['id' => 'session_id']);
    }


}