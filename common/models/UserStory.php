<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


use common\definitions\Common;

class UserStory extends \common\models\gii\UserStory
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

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['story_id' => 'story_id']);
    }

    public function getTeam(){
        return $this->hasOne('common\models\Team',  ['team_id' => 'team_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['user_id' => 'user_id']);
    }

    public function getUserLoc() {
        return $this->hasOne('common\models\UserLoc', ['user_id' => 'user_id']);
    }

}