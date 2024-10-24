<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class GptContent extends \common\models\gii\GptContent
{

    const MSG_TYPE_TEXT = 1; // 文本

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

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getTouser(){
        return $this->hasOne('common\models\User',  ['id' => 'to_user_id']);
    }

    public function getSender(){
        return $this->hasOne('common\models\User',  ['id' => 'sender_id']);
    }

}