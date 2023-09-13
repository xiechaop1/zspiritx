<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryStages extends \common\models\gii\StoryStages
{


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getNextstage() {
        return $this->hasMany('common\models\StoryStages', ['id' => 'pre_stage_id']);
    }


}