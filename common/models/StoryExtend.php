<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryExtend extends \common\models\gii\StoryExtend
{


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }


}