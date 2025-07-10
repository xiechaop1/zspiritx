<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserBook extends \common\models\gii\UserBook
{

    public static $storyList = [
        1 => [
            'id' => 1,
            'story' => 'Story1',
            'poi' => 1,
        ],
    ];

    public static $poiList = [
        1 => [
            'id' => 1,
            'poi_name' => '篮球场',
            'lnglat' => '116.404,39.915',
            'story' => '',
            'tips' => '请在篮球场打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在篮球场打卡',
            'resources' => [
                'img1' => '',
                'img2' => '',
                'img3' => '',
            ],
        ],
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getUser(){
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getSession(){
        return $this->hasOne('common\models\Session',  ['id' => 'session_id']);
    }

    public function getStory() {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }

}