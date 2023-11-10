<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class Story extends \common\models\gii\Story
{

    const STORY_TYPE_CHILD      = 1; // 儿童剧本
    const STORY_TYPE_ADULT      = 2; // 成人剧本
    const STORY_TYPE_RUNNING    = 3; // 跑团剧本

    public static $storyType2Name = [
        self::STORY_TYPE_CHILD      => '儿童剧本',
        self::STORY_TYPE_ADULT      => '成人剧本',
        self::STORY_TYPE_RUNNING    => '跑团剧本',
    ];

    const STORY_STATUS_ONLINE  = 1; // 上架
    const STORY_STATUS_OFFLINE = 0; // 下架

    const STORY_STATUS_OPEN_WAIT = 2; // 开放等待

    public static $storyStatus2Name = [
        self::STORY_STATUS_ONLINE  => '上架',
        self::STORY_STATUS_OFFLINE => '下架',
        self::STORY_STATUS_OPEN_WAIT => '开放等待',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function exec() {

        $ret = $this->save();
        return $ret;
    }

    public function getExtend(){
        return $this->hasOne('common\models\StoryExtend',  ['story_id' => 'id']);
    }

    public function getRoles(){
        return $this->hasMany('common\models\StoryRole',  ['id' => 'story_id']);
    }

    public function getTeams(){
        return $this->hasMany('common\models\Team',  ['id' => 'story_id']);
    }

    public function getGoal(){
        return $this->hasMany('common\models\StoryGoal', ['id' => 'story_id']);
    }

    public function getTags(){
        return $this->hasMany('common\models\StoryTag', ['id' => 'story_id']);
    }


}