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

    public function getModels(){
        return $this->hasOne('common\models\Models',  ['id' => 'model_id']);
    }

    public function getSessionModel(){
        return $this->hasOne('common\models\SessionModels',  ['id' => 'session_model_id']);
    }

    public function getStoryModel(){
        return $this->hasOne('common\models\StoryModels',  ['id' => 'story_model_id']);
    }

    public function getBuff(){
        return $this->hasOne('common\models\Buff',  ['id' => 'active_next'])->onCondition(['active_type' => StoryModels::ACTIVE_TYPE_BUFF]);
    }

}