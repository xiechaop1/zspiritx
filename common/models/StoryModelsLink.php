<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryModelsLink extends \common\models\gii\StoryModelsLink
{

    const EFF_TYPE_DIALOG = 1; // 对话


    public static $effType2Name = [
        self::EFF_TYPE_DIALOG => '对话',
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

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }


}