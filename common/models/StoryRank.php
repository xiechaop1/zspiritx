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
        self::STORY_RANK_CLASS_CAR_MATCH => '比赛成绩',
        self::STORY_RANK_CLASS_CAR_SCORE => '评分',
        self::STORY_RANK_CLASS_CAR_VALUABLE => '价值',
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
                'storyModel' => [
                    'name' => '车型',
                ],
                'score' => [
                    'name' => '圈速',
                ],
                'score2' =>
                    ['name' => '总时间',
                    'format' => '\\common\\helpers\\Common::formatTimeToStr(%d)',
                    ],
            ],
            self::STORY_RANK_CLASS_CAR_SCORE => [
                'storyModel' => [
                    'name' => '车型',
                ],
                'score' => [
                    'name' => '分数',
                ],
//                'score2' => '总时间',
            ],
            self::STORY_RANK_CLASS_CAR_VALUABLE => [
                'storyModel' => [
                    'name' => '车型',
                ],
                'score' => [
                    'name' => '价值',
                    'format' => 'number_format(%d/10000)万',
                ],
//                'score2' => '总时间',
            ],
        ],
        2 => [
            self::STORY_RANK_CLASS_CAR_MATCH => [
                'score' => [
                    'name' => '圈速',
                ],
                'score2' =>
                    ['name' => '总时间',
                        'format' => '\\common\\helpers\\Common::formatTimeToStr(%d)',
                    ],
            ],
            self::STORY_RANK_CLASS_CAR_SCORE => [
                'score' => [
                    'name' => '分数',
                ],
//                'score2' => '总时间',
            ],
            self::STORY_RANK_CLASS_CAR_VALUABLE => [
                'score' => [
                    'name' => '价值',
                    'format' => 'number_format(%d)',
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