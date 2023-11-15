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
    const SCAN_IMAGE_TYPE_RANDOM_PLANE_AFTER_SCAN = 11; // 扫描后随机平面
    const SCAN_IMAGE_TYPE_FIX_PLANE_AFTER_SCAN = 12; // 扫描后固定平面
    const SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_AFTER_SCAN     = 14;   // 扫描随机在用户周围放置
    const SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG = 21; // 经纬度随机平面
    const SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG = 22; // 经纬度固定平面
    const SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_LATLNG = 24; // 经纬度随机在用户周围放置
    const SCAN_IMAGE_TYPE_FOLLOW_USER = 31; // 跟随用户

    public static $scanImageType2Name = [
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE => '随机平面',
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE_AFTER_SCAN => '扫描后随机平面',
        self::SCAN_IMAGE_TYPE_FIX_PLANE_AFTER_SCAN => '扫描后固定平面',
        self::SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_AFTER_SCAN => '扫描后随机在用户周围放置',
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG => '经纬度随机平面',
        self::SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG => '经纬度固定平面',
        self::SCAN_IMAGE_TYPE_RANDOM_AROUND_USER_LATLNG => '经纬度随机在用户周围放置',
        self::SCAN_IMAGE_TYPE_FOLLOW_USER => '跟随用户',
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
    public static $activeType2Name = [
        self::ACTIVE_TYPE_CHAT => '聊天',
        self::ACTIVE_TYPE_BUFF => 'BUFF',
        self::ACTIVE_TYPE_MODEL => '对模型',
    ];

    const VISIBLE_SHOW        = 0;    // 显示
    const VISIBLE_HIDE        = 1;    // 隐藏

    public static $visible2Name = [
        self::VISIBLE_SHOW => '显示',
        self::VISIBLE_HIDE => '隐藏',
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