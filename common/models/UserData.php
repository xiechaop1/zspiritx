<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


use common\definitions\Common;

class UserData extends \common\models\gii\UserData
{

    const DATA_TYPE_TOTAL = 10;
    const DATA_TYPE_RIGHT = 11;
    const DATA_TYPE_WRONG = 12;
    const DATA_TYPE_RATE = 13;
    const DATA_TYPE_TODAY_TOTAL = 20;
    const DATA_TYPE_TODAY_RIGHT = 21;
    const DATA_TYPE_TODAY_WRONG = 22;
    const DATA_TYPE_TODAY_RATE = 23;

    public static $dataType2Name = [
        self::DATA_TYPE_TOTAL => '总答题数',
        self::DATA_TYPE_RIGHT => '总正确数',
        self::DATA_TYPE_WRONG => '总错误数',
        self::DATA_TYPE_RATE => '总正确率',
        self::DATA_TYPE_TODAY_TOTAL => '今日答题数',
        self::DATA_TYPE_TODAY_RIGHT => '今日正确数',
        self::DATA_TYPE_TODAY_WRONG => '今日错误数',
        self::DATA_TYPE_TODAY_RATE => '今日正确率',
    ];


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function exec() {

        $ret = $this->save();
        return $ret;
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }


}