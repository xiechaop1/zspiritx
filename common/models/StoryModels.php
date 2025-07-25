<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryModels extends \common\models\gii\StoryModels
{

    const SCAN_IMAGE_TYPE_RANDOM_PLANE = 1; // 随机平面

    const SCAN_IMAGE_TYPE_ROUND_USER   = 2; // 围绕用户
    const SCAN_IMAGE_TYPE_RANDOM_PLANE_AFTER_SCAN = 11; // 扫描后随机平面
    const SCAN_IMAGE_TYPE_FIX_PLANE_AFTER_SCAN = 12; // 扫描后固定平面

    const SCAN_IMAGE_TYPE_FOLLOW_IMAGE      = 13; // 跟随图像
    const SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_AFTER_SCAN     = 14;   // 扫描随机在用户周围放置
    const SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG = 21; // 经纬度随机平面
    const SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG = 22; // 经纬度固定平面
    const SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_LATLNG = 24; // 经纬度随机在用户周围放置
    const SCAN_IMAGE_TYPE_FOLLOW_USER = 31; // 跟随用户
    const SCAN_IMAGE_TYPE_UI_WINDOW = 51; // UI窗口
    const SCAN_IMAGE_TYPE_MODEL_COMBINE = 6; // 模型组合

    public static $scanImageType2Name = [
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE => '随机平面',
        self::SCAN_IMAGE_TYPE_ROUND_USER => '围绕用户',
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE_AFTER_SCAN => '扫描后随机平面',
        self::SCAN_IMAGE_TYPE_FIX_PLANE_AFTER_SCAN => '扫描后固定平面',
        self::SCAN_IMAGE_TYPE_FOLLOW_IMAGE => '跟随图像',
        self::SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_AFTER_SCAN => '扫描后随机在用户周围放置',
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG => '经纬度随机平面',
        self::SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG => '经纬度固定平面',
        self::SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_LATLNG => '经纬度随机在用户周围放置',
        self::SCAN_IMAGE_TYPE_FOLLOW_USER => '跟随用户',
        self::SCAN_IMAGE_TYPE_UI_WINDOW => 'UI窗口',
        self::SCAN_IMAGE_TYPE_MODEL_COMBINE => '模型组合',
    ];

    const STORY_MODEL_CLASS_NORMAL  = 1; // 普通
    const STORY_MODEL_CLASS_ITEM    = 2; // 物品
    const STORY_MODEL_CLASS_PET     = 3; // 宠物
    const STORY_MODEL_CLASS_RIVAL   = 4; // 对手
    const STORY_MODEL_CLASS_PET_OUTSIDE = 5; // 外部宠物（显示地图上）
    const STORY_MODEL_CLASS_PET_ITEM = 6; // 宠物物品
    const STORY_MODEL_CLASS_RANDOM_CLUE = 7; // 随机线索
    const STORY_MODEL_CLASS_STAGE    = 20; // 场景
    const STORY_MODEL_CLASS_POS      = 21; // 位置
    const STORY_MODEL_CLASS_GOODS    = 30; // 商品

    public static $storyModelClass2Name = [
        self::STORY_MODEL_CLASS_NORMAL => '普通',
        self::STORY_MODEL_CLASS_ITEM => '物品',
        self::STORY_MODEL_CLASS_PET => '宠物',
        self::STORY_MODEL_CLASS_PET_ITEM => '宠物物品',
        self::STORY_MODEL_CLASS_RIVAL => '对手',
        self::STORY_MODEL_CLASS_PET_OUTSIDE => '外部宠物（地图上）',
        self::STORY_MODEL_CLASS_RANDOM_CLUE => '随机线索',
        self::STORY_MODEL_CLASS_STAGE => '场景',
        self::STORY_MODEL_CLASS_POS => '位置',
        self::STORY_MODEL_CLASS_GOODS => '商品',
    ];

    public static $storyModelClassRate = [
//        self::STORY_MODEL_CLASS_NORMAL => 0.5,
//        self::STORY_MODEL_CLASS_PET_OUTSIDE => 30,
//        self::STORY_MODEL_CLASS_ITEM => 70,
//        self::STORY_MODEL_CLASS_RIVAL => 100,
    ];

    const DIRECTION_DEFAULT     = 1;        // 朝向默认
    const DIRECTION_TO_USER     = 2;        // 朝向用户

    public static $direction2Name = [
        self::DIRECTION_DEFAULT => '朝向默认',
        self::DIRECTION_TO_USER => '朝向用户',
    ];

    const ACTIVE_TYPE_CHAT      = 1;    // 聊天
    const ACTIVE_TYPE_BUFF      = 2;    // BUFF
    const ACTIVE_TYPE_MODEL     = 3;    // 模型
    const ACTIVE_TYPE_MODEL_DISPLAY = 4;    // 模型展示
    const ACTIVE_TYPE_SHOW    = 5;    // 展示
    const ACTIVE_TYPE_HTML    = 6;    // HTML

    const ACTIVE_TYPE_COMBINE = 7;    // 组合
    const ACTIVE_TYPE_MODEL_UNIQUE = 8;    // 模型唯一

    public static $activeType2Name = [
        self::ACTIVE_TYPE_SHOW => '展示图片',
        self::ACTIVE_TYPE_CHAT => '聊天',
        self::ACTIVE_TYPE_BUFF => 'BUFF',
        self::ACTIVE_TYPE_MODEL => '对模型',
        self::ACTIVE_TYPE_MODEL_DISPLAY => '模型放出来',
        self::ACTIVE_TYPE_HTML => 'HTML',
        self::ACTIVE_TYPE_COMBINE => '组合',
        self::ACTIVE_TYPE_MODEL_UNIQUE => '模型动作独立(无SET)',
    ];

    const VISIBLE_SHOW        = 0;    // 显示
    const VISIBLE_HIDE        = 1;    // 隐藏

    public static $visible2Name = [
        self::VISIBLE_SHOW => '显示',
        self::VISIBLE_HIDE => '隐藏',
    ];

    const PLACING_NOT_HINT      = 0;       // 不显示
    const PLACING_HINT          = 1;       // 显示

    public static $placingHint2Name = [
        self::PLACING_HINT => '显示',
        self::PLACING_NOT_HINT => '不显示',
    ];

    const IS_UNDERTAKE_NOT        = 0;    // 不承接
    const IS_UNDERTAKE_YES        = 1;    // 承接

    public static $isUndertake2Name = [
        self::IS_UNDERTAKE_NOT => '否',
        self::IS_UNDERTAKE_YES => '是',
    ];

    const USE_ALLOW_NOT             = 0;    // 不允许被使用
    const USE_ALLOW_TO_SELF         = 1;    // 允许自己使用
    const USE_ALLOW_NEED_TARGET     = 2;    // 需要目标使用

    public static $useAllow2Name = [
        self::USE_ALLOW_NOT => '不允许被使用',
        self::USE_ALLOW_TO_SELF => '允许自己使用',
        self::USE_ALLOW_NEED_TARGET => '需要目标使用',
    ];

    const SELECTED_PERMISSION_NOT   = 0;    // 不可选
    const SELECTED_PERMISSION_YES   = 1;    // 可选

    public static $selectedPermission2Name = [
        self::SELECTED_PERMISSION_NOT => '不可选',
        self::SELECTED_PERMISSION_YES => '可选',
    ];

    const NAMECARD_DISPLAY_NOT      = 0;    // 不显示
    const NAMECARD_DISPLAY_YES      = 1;    // 显示

    public static $namecardDisplay2Name = [
        self::NAMECARD_DISPLAY_NOT => '不显示',
        self::NAMECARD_DISPLAY_YES => '显示',
    ];

    const IS_RANDOM_YES             = 1; // 可随机出现
    const IS_RANDOM_NOT             = 0; // 不可随机出现

    public static $isRandom2Name = [
        self::IS_RANDOM_NOT => '不可随机出现',
        self::IS_RANDOM_YES => '可随机出现',
    ];


    const SET_TYPE_NORMAL        = 0;    // 正常放置
    const SET_TYPE_ONE_TIME      = 1;     // 一次性
    const SET_TYPE_ONE_TIME_PER_DAY = 2;    // 每天一次

    public static $setType2Name = [
        self::SET_TYPE_NORMAL => '正常放置',
        self::SET_TYPE_ONE_TIME => '一次性',
        self::SET_TYPE_ONE_TIME_PER_DAY => '每天一次',
    ];

    public static $propLevelConfig = [
        [
            'name' => '1级',
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

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getModel(){
        return $this->hasOne('common\models\Models',  ['id' => 'model_id']);
    }

    public function getBuff(){
        return $this->hasOne('common\models\Buff',  ['id' => 'active_next']);
    }

    public function getSessionModel(){
        return $this->hasOne('common\models\SessionModel',  ['story_id' => 'id']);
    }

    public function getDetail(){
        return $this->hasOne('common\models\StoryModelDetail',  ['id' => 'story_model_detail_id']);
    }

    public function getGroupStoryModels(){
        return $this->hasMany('common\models\StoryModels',  ['model_group' => 'model_group', 'story_id' => 'story_id'])->onCondition(['<>', 'model_group', '']);
    }

}