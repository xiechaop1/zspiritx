<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserModels extends \common\models\gii\UserModels
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getModel(){
        return $this->hasOne('common\models\Models',  ['id' => 'model_id']);
    }

    public function getSessionModel(){
        return $this->hasOne('common\models\SessionModels',  ['id' => 'session_model_id']);
    }

    public function getStoryModel(){
        return $this->hasOne('common\models\StoryModels',  ['id' => 'story_model_id']);
    }

    public function getStoryModelDetail(){
        return $this->hasOne('common\models\StoryModelDetail',  ['id' => 'story_model_detail_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getStory() {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);

    }

//    public function getBuff(){
//        return $this->hasOne('common\models\Buff',  ['id' => 'active_next'])->onCondition(['active_type' => StoryModels::ACTIVE_TYPE_BUFF]);
//    }

}