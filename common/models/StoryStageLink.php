<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryStageLink extends \common\models\gii\StoryStageLink
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getStorystage() {
        return $this->hasMany('common\models\StoryStages', ['id' => 'story_stage_id']);
    }

    public function getPrestorystage() {
        return $this->hasOne('common\models\StoryStages', ['id' => 'pre_story_stage_id']);
    }


}