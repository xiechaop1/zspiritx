<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryRank extends \common\models\gii\StoryRank
{

    const STORY_RANK_CLASS_CAR_MATCH = 1;
    const STORY_RANK_CLASS_CAR_SCORE = 2;
    const STORY_RANK_CLASS_CAR_VALUABLE = 3;

    public static $storyRankClass2Name = [
        self::STORY_RANK_CLASS_CAR_MATCH => '车型匹配',
        self::STORY_RANK_CLASS_CAR_SCORE => '车型评分',
        self::STORY_RANK_CLASS_CAR_VALUABLE => '车型价值',
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
        return $this->hasOne('common\models\StoryModels',  ['id' => 'story_model_id']);
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