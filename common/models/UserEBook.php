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
            'lnglat' => '114.132221,22.28951',
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
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],

            ],
        ],
        2 => [
            'id' => 1,
            'poi_name' => '叮叮车总站',
            'lnglat' => '114.13103,22.28023',
            'story' => 'Story2',
//            'storyId' => 1,
            'tips' => '请在打卡点2打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在打卡点2打卡',
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
                        'story' => 'Page Story 2 - 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 2 - 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 2 - 3',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],

            ],
        ],
        3 => [
            'id' => 1,
            'poi_name' => 'HONGKONG BE HAPPY',
            'lnglat' => '114.130847,22.280945',
            'story' => 'Story2',
//            'storyId' => 1,
            'tips' => '请在打卡点3打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在打卡点3打卡',
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
                        'story' => 'Page Story 3 - 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 3 - 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 3 - 3',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],

            ],
        ],
        4 => [
            'id' => 1,
            'poi_name' => '我在坚尼地城等你',
            'lnglat' => '114.131626,22.281086',
            'story' => 'Story4',
//            'storyId' => 1,
            'tips' => '请在打卡点4打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在打卡点2打卡',
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
                        'story' => 'Page Story 4 - 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 4 - 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 4 - 3',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],

            ],
        ],
        5 => [
            'id' => 1,
            'poi_name' => '海滨打卡点',
            'lnglat' => '114.133587,22.282015',
            'story' => 'Story2',
//            'storyId' => 1,
            'tips' => '请在打卡点2打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在打卡点2打卡',
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
                        'story' => 'Page Story 5 - 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 5 - 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 5 - 3',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],

            ],
        ],
        6 => [
            'id' => 1,
            'poi_name' => '卑路乍湾公园打卡点',
            'lnglat' => '114.134633,22.281892',
            'story' => 'Story2',
//            'storyId' => 1,
            'tips' => '请在打卡点2打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在打卡点2打卡',
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
                        'story' => 'Page Story 6 - 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 6 - 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 6 - 3',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],

            ],
        ],
        7 => [
            'id' => 1,
            'poi_name' => '临时打卡点',
            'lnglat' => '114.132587,22.212015',
            'story' => 'Story2',
//            'storyId' => 1,
            'tips' => '请在打卡点7打卡',
            'story_model_id' => 10,
            'video_prompt' => '请在打卡点7打卡',
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
                        'story' => 'Page Story 7 - 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 7 - 2',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 7 - 3',
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