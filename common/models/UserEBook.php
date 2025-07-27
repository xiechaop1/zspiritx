<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class UserEBook extends \common\models\gii\UserEbook
{

    public $is_show;

    const USER_EBOOK_STATUS_DEFAULT = 0; // 默认
    const USER_EBOOK_STATUS_PLAYING = 1; // 进行中
    const USER_EBOOK_STATUS_COMPLETED = 10; // 完成
    public static $userEbookStatus2Name = [
        self::USER_EBOOK_STATUS_DEFAULT => '默认',
        self::USER_EBOOK_STATUS_PLAYING => '进行中',
        self::USER_EBOOK_STATUS_COMPLETED => '完成',
    ];

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
            'story' => 'Story1',
//            'storyId' => 1,
            'tips' => '请在篮球场打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在篮球场打卡',
            'resources' => [
                'img1' => '',
                'img2' => '',
                'img3' => '',
            ],
            'pois' => [
                1 => [
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],
            ],
        ],
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getUser() {
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getEbookRes() {
        return $this->hasMany('common\models\UserEBookRes', ['user_ebook_id' => 'id']);
    }


}