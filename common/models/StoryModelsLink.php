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
    const EFF_TYPE_MODEL = 2; // 模型
    const EFF_TYPE_MODEL_AND_DISPLAY = 3; // 模型并显示
    const EFF_TYPE_INCLUDE_MODEL_AND_DISPLAY = 4; // 包含模型并显示
    const EFF_TYPE_PROP_AND_DIALOG = 5; // 属性和对话


    public static $effType2Name = [
        self::EFF_TYPE_DIALOG => '对话',
        self::EFF_TYPE_MODEL => '模型',
        self::EFF_TYPE_MODEL_AND_DISPLAY => '模型并显示',
        self::EFF_TYPE_INCLUDE_MODEL_AND_DISPLAY => '包含模型并显示',
        self::EFF_TYPE_PROP_AND_DIALOG => '属性和对话',
    ];

    const IS_TAG_YES = 1;
    const IS_TAG_NO = 0;

    public static $isTag2Name = [
        self::IS_TAG_YES => '是',
        self::IS_TAG_NO => '否',
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