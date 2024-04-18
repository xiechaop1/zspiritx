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
        self::STORY_RANK_CLASS_CAR_MATCH => '车型比赛成绩',
        self::STORY_RANK_CLASS_CAR_SCORE => '车型评分',
        self::STORY_RANK_CLASS_CAR_VALUABLE => '车型价值',
    ];

    const STORY_RANK_SORT_ASC = 3;
    const STORY_RANK_SORT_DESC = 4;

    public static $storyRankSort2Name = [
        self::STORY_RANK_SORT_ASC => '升序',
        self::STORY_RANK_SORT_DESC => '降序',
    ];

    public static $storyRankCategories = [
        11 => [
            self::STORY_RANK_CLASS_CAR_MATCH => [
                'score' => [
                    'name' => '圈速',
                    'format' => 'Common::formatTimeToStr',
                ],
                'score2' =>
                    ['name' => '总时间'],
            ],
            self::STORY_RANK_CLASS_CAR_SCORE => [
                'score' => [
                    'name' => '分数'
                ],
//                'score2' => '总时间',
            ],
            self::STORY_RANK_CLASS_CAR_VALUABLE => [
                'score' => [
                    'name' => '价值'
                ],
//                'score2' => '总时间',
            ],
        ],
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