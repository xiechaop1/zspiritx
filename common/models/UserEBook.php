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
            'story' => '80年代香港的港剧',
            'desc' => '80-90年香港的《射雕》《上海滩》都是经典',
            'pois' => [
                1 => [
                    [
                        'poi_id' => 1,
                        'page' => 1,
                        'poi_name' => '篮球场',
                        'lnglat' => '114.132221,22.28951',
                        'story' => 'Page Story 1',
                        'prompt' => '',
                        'tips' => '请在篮球场打卡',
                        'story_model_id' => 10,
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                        'duration' => 10,
                    ],
                    [
                        'poi_id' => 2,
                        'page' => 1,
                        'poi_name' => '叮叮车总站',
                        'lnglat' => '114.13103,22.28023',
                        'story' => 'Page Story 2',
                        'prompt' => '',
                        'duration' => 10,
                        'tips' => '请在打卡点2打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 3,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => 'HONGKONG BE HAPPY',
                        'lnglat' => '114.130847,22.280945',
                        'tips' => '请在打卡点3打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点3打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 4,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '我在坚尼地城等你',
                        'lnglat' => '114.131626,22.281086',
                        'tips' => '请在打卡点4打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 5,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '海滨打卡点',
                        'lnglat' => '114.133587,22.282015',
                        'tips' => '请在打卡点2打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 6,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '卑路乍湾公园打卡点',
                        'lnglat' => '114.134633,22.281892',
                        'tips' => '请在打卡点2打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 7,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '临时打卡点',
                        'lnglat' => '114.132587,22.212015',
                        'tips' => '请在打卡点7打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点7打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                ],

            ],
        ],
        2 => [
            'id' => 1,
            'story' => '现代香港',
            'desc' => '完全现代香港的故事',
            'pois' => [
                1 => [
                    [
                        'poi_id' => 1,
                        'page' => 1,
                        'poi_name' => '篮球场',
                        'lnglat' => '114.132221,22.28951',
                        'story' => 'Page Story 1',
                        'prompt' => '',
                        'tips' => '请在篮球场打卡',
                        'story_model_id' => 10,
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                        'duration' => 10,
                    ],
                    [
                        'poi_id' => 2,
                        'page' => 1,
                        'poi_name' => '叮叮车总站',
                        'lnglat' => '114.13103,22.28023',
                        'story' => 'Page Story 2',
                        'prompt' => '',
                        'duration' => 10,
                        'tips' => '请在打卡点2打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 3,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => 'HONGKONG BE HAPPY',
                        'lnglat' => '114.130847,22.280945',
                        'tips' => '请在打卡点3打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点3打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 4,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '我在坚尼地城等你',
                        'lnglat' => '114.131626,22.281086',
                        'tips' => '请在打卡点4打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 5,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '海滨打卡点',
                        'lnglat' => '114.133587,22.282015',
                        'tips' => '请在打卡点2打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 6,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '卑路乍湾公园打卡点',
                        'lnglat' => '114.134633,22.281892',
                        'tips' => '请在打卡点2打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点2打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
                    ],
                    [
                        'poi_id' => 7,
                        'page' => 1,
                        'story' => 'Page Story 3',
                        'prompt' => '',
                        'duration' => 10,
                        'poi_name' => '临时打卡点',
                        'lnglat' => '114.132587,22.212015',
                        'tips' => '请在打卡点7打卡',
                        'story_model_id' => 10,
                        'video_prompt' => '请在打卡点7打卡',
                        'resources' => [
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ],
                            [
                                'img' => '',
                                'video_prompt' => '请在篮球场打卡',
                            ]
                        ],
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