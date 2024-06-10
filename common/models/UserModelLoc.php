<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class UserModelLoc extends \common\models\gii\UserModelLoc
{

    public $is_show;

    const USER_MODEL_LOC_STATUS_LIVE = 1;
    const USER_MODEL_LOC_STATUS_DEAD = 2;

    public static $userModelLocStatus2Name = [
        self::USER_MODEL_LOC_STATUS_LIVE => '存活',
        self::USER_MODEL_LOC_STATUS_DEAD => '死亡',
    ];

    const ACTIVE_CLASS_CATCH = 1; // 捕捉
    const ACTIVE_CLASS_BATTLE = 2; // 战斗
    const ACTIVE_CLASS_STORY = 3; // 剧情
    const ACTIVE_CLASS_OTHER = 4; // 其他

    public static $activeClass2Name = [
        self::ACTIVE_CLASS_CATCH => '捕捉',
        self::ACTIVE_CLASS_BATTLE => '战斗',
        self::ACTIVE_CLASS_STORY => '剧情',
        self::ACTIVE_CLASS_OTHER => '其他',
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


    public function getUserModel() {
        return $this->hasOne('common\models\UserModels',  ['id' => 'user_model_id']);
    }

    public function getSession() {
        return $this->hasOne('common\models\Session',  ['id' => 'session_id']);
    }

    public function getUser() {
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getLocation(){
        return $this->hasOne('common\models\Location',  ['id' => 'location_id']);
    }

}