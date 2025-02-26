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

    const MATCH_TYPE_BATTLE         = 1; // 战斗
    const MATCH_TYPE_CHALLENGE      = 2; // 挑战
    const MATCH_TYPE_CONTEST        = 3; // 比赛
    const MATCH_TYPE_KNOCKOUT       = 4; // 淘汰赛
    const MATCH_TYPE_RACE           = 5; // 竞速

    public static $matchType2Name = [
        self::MATCH_TYPE_BATTLE => '战斗',
        self::MATCH_TYPE_CHALLENGE => '挑战',
        self::MATCH_TYPE_CONTEST => '比赛',
        self::MATCH_TYPE_KNOCKOUT => '淘汰赛',
        self::MATCH_TYPE_RACE => '竞速',
    ];

    const MATCH_CLASS_NORMAL        = 1; // 普通
    const MATCH_CLASS_MATH          = 20; // 数学
    const MATCH_CLASS_ENGLISH       = 30; // 英语
    const MATCH_CLASS_CHINESE       = 40; // 语文
    const MATCH_CLASS_POEM          = 41; // 诗词
    const MATCH_CLASS_POEM_IDIOM    = 42; // 成语
    const MATCH_CLASS_HISTORY       = 50; // 历史
    const MATCH_CLASS_PHYSICS       = 60; // 物理
    const MATCH_CLASS_ANY           = 999;    // 通用

    public static $matchClass2Name = [
        self::MATCH_CLASS_NORMAL => '普通',
        self::MATCH_CLASS_MATH => '数学',
        self::MATCH_CLASS_ENGLISH => '英语',
        self::MATCH_CLASS_CHINESE => '语文',
        self::MATCH_CLASS_POEM => '诗词',
        self::MATCH_CLASS_POEM_IDIOM => '成语',
        self::MATCH_CLASS_HISTORY => '历史',
        self::MATCH_CLASS_PHYSICS => '物理',
        self::MATCH_CLASS_ANY => '通用',
    ];

    public static $matchClass2PackageClass = [
        self::MATCH_CLASS_NORMAL => QaPackage::PACKAGE_CLASS_NORMAL,
        self::MATCH_CLASS_MATH => QaPackage::PACKAGE_CLASS_MATH,
        self::MATCH_CLASS_ENGLISH => QaPackage::PACKAGE_CLASS_ENGLISH,
        self::MATCH_CLASS_CHINESE => QaPackage::PACKAGE_CLASS_CHINESE,
        self::MATCH_CLASS_POEM => QaPackage::PACKAGE_CLASS_POEM,
        self::MATCH_CLASS_POEM_IDIOM => QaPackage::PACKAGE_CLASS_POEM_IDIOM,
        self::MATCH_CLASS_HISTORY => QaPackage::PACKAGE_CLASS_HISTORY,
        self::MATCH_CLASS_PHYSICS => QaPackage::PACKAGE_CLASS_PHYSICS,
        self::MATCH_CLASS_ANY => QaPackage::PACKAGE_CLASS_ANY,
    ];

    public static $matchClass2QaClass = [
        self::MATCH_CLASS_NORMAL => Qa::QA_CLASS_NORMAL,
        self::MATCH_CLASS_MATH => Qa::QA_CLASS_MATH,
        self::MATCH_CLASS_ENGLISH => Qa::QA_CLASS_ENGLISH,
        self::MATCH_CLASS_CHINESE => Qa::QA_CLASS_CHINESE,
        self::MATCH_CLASS_POEM => Qa::QA_CLASS_POEM,
        self::MATCH_CLASS_POEM_IDIOM => Qa::QA_CLASS_POEM_IDIOM,
        self::MATCH_CLASS_HISTORY => Qa::QA_CLASS_HISTORY,
        self::MATCH_CLASS_PHYSICS => Qa::QA_CLASS_PHYSICS,
        self::MATCH_CLASS_ANY => Qa::QA_CLASS_ANY,
    ];

    public static $matchClassRandList = [
        self::MATCH_CLASS_MATH,
//        self::MATCH_CLASS_ENGLISH,
//        self::MATCH_CLASS_CHINESE,
        self::MATCH_CLASS_POEM,
        self::MATCH_CLASS_POEM_IDIOM,
//        self::MATCH_CLASS_HISTORY,
    ];

    const STORY_MATCH_STATUS_PREPARE = 1; // 准备
    const STORY_MATCH_STATUS_MATCHING = 2; // 匹配中
    const STORY_MATCH_STATUS_PLAYING = 3; // 游戏中
    const STORY_MATCH_STATUS_END = 4; // 结束
    const STORY_MATCH_STATUS_CANCEL = 5; // 取消
    const STORY_MATCH_STATUS_TIMEOUT = 6; // 超时

    public static $storyMatchStatus2Name = [
        self::STORY_MATCH_STATUS_PREPARE => '准备',
        self::STORY_MATCH_STATUS_MATCHING => '匹配中',
        self::STORY_MATCH_STATUS_PLAYING => '游戏中',
        self::STORY_MATCH_STATUS_END => '结束',
        self::STORY_MATCH_STATUS_CANCEL => '取消',
        self::STORY_MATCH_STATUS_TIMEOUT => '超时',
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

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getSession(){
        return $this->hasOne('common\models\Session',  ['id' => 'session_id']);
    }

    public function getPlayers() {
        return $this->hasMany('common\models\StoryMatchPlayer', ['match_id' => 'id']);
    }


}