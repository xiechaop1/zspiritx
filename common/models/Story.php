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
        return $this->hasOne('common\models\StoryExtend',  ['id' => 'story_id']);
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