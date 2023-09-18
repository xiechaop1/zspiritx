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
    const SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG = 21; // 经纬度随机平面
    const SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG = 22; // 经纬度固定平面

    public static $scanImageType2Name = [
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE => '随机平面',
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE_AFTER_SCAN => '扫描后随机平面',
        self::SCAN_IMAGE_TYPE_FIX_PLANE_AFTER_SCAN => '扫描后固定平面',
        self::SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG => '经纬度随机平面',
        self::SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG => '经纬度固定平面',
    ];

    const DIRECTION_DEFAULT     = 1;        // 朝向默认
    const DIRECTION_TO_USER     = 2;        // 朝向用户

    public static $direction2Name = [
        self::DIRECTION_DEFAULT => '朝向默认',
        self::DIRECTION_TO_USER => '朝向用户',
    ];

    const ACTIVE_TYPE_CHAT      = 1;    // 聊天
    const ACTIVE_TYPE_BUFF      = 2;    // BUFF
    public static $activeType2Name = [
        self::ACTIVE_TYPE_CHAT => '聊天',
        self::ACTIVE_TYPE_BUFF => 'BUFF',
    ];

    const ACTIVE_BUFF_HIDDEN    = 1;    // 隐身
    const ACTIVE_BUFF_SHIELD    = 2;    // 护盾
    const ACTIVE_BUFF_DETOXIFY  = 3;    // 解毒
    const ACTIVE_BUFF_SHOW_MAP  = 4;    // 显示地图
    const ACTIVE_BUFF_RADARA    = 5;    // 雷达

    public static $activeBuff2Name = [
        self::ACTIVE_BUFF_HIDDEN => '隐身',
        self::ACTIVE_BUFF_SHIELD => '护盾',
        self::ACTIVE_BUFF_DETOXIFY => '解毒',
        self::ACTIVE_BUFF_SHOW_MAP => '显示地图',
        self::ACTIVE_BUFF_RADARA => '雷达',
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

}