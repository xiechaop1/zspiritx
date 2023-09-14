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

}