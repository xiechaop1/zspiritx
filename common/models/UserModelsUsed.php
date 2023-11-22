<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class UserModelsUsed extends \common\models\gii\UserModelsUsed
{

    const USE_STATUS_WAITING             = 0; // 等待
    const USE_STATUS_COMPLETED_PARTLY   = 1; // 部分完成
    const USE_STATUS_COMPLETED          = 2; // 全部完成
    const USE_STATUS_TIMEOUT            = 10; // 超时
    const USE_STATUS_CANCEL             = 99; // 取消

    public static $useStatus2Name = [
        self::USE_STATUS_WAITING => '等待',
        self::USE_STATUS_COMPLETED_PARTLY => '部分完成',
        self::USE_STATUS_COMPLETED => '全部完成',
        self::USE_STATUS_TIMEOUT => '超时',
        self::USE_STATUS_CANCEL => '取消',
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

    public function getStoryModel2(){
        return $this->hasOne('common\models\StoryModels',  ['id' => 'story_model_id2']);
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


}