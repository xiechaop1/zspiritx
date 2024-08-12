<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryModelSpecialEff extends \common\models\gii\StoryModelSpecialEff
{

    const EFF_MODE_NORMAL = 1;    // 普通
    const EFF_MODE_OWNER = 2;     // 自有
    const EFF_MODE_RIVAL = 3;     // 对手
    const EFF_MODE_AOE = 4;       // AOE

    public static $effMode2Name = [
        self::EFF_MODE_NORMAL => '普通',
        self::EFF_MODE_OWNER => '自有',
        self::EFF_MODE_RIVAL => '对手',
        self::EFF_MODE_AOE => 'AOE',
    ];

    const EFF_CLASS_NORMAL = 1;    // 普通
    const EFF_CLASS_WINDY = 2;     // 风系
    const EFF_CLASS_FIRE = 3;      // 火系
    const EFF_CLASS_WATER = 4;     // 水系
    const EFF_CLASS_EARTH = 5;     // 土系
    const EFF_CLASS_THUNDER = 6;   // 雷系
    const EFF_CLASS_POISON = 7;    // 毒系
    const EFF_CLASS_LIGHT = 9;     // 光系
    const EFF_CLASS_DARK = 10;      // 暗系

    public static $effClass2Name = [
        self::EFF_CLASS_NORMAL => '普通',
        self::EFF_CLASS_WINDY => '风系',
        self::EFF_CLASS_FIRE => '火系',
        self::EFF_CLASS_WATER => '水系',
        self::EFF_CLASS_EARTH => '土系',
        self::EFF_CLASS_THUNDER => '雷系',
        self::EFF_CLASS_POISON => '毒系',
        self::EFF_CLASS_LIGHT => '光系',
        self::EFF_CLASS_DARK => '暗系',
    ];

    const STATUS_NORMAL = 0;    // 正常
    const STATUS_FORBIT = 2;    // 禁用

    public static $status2Name = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_FORBIT => '禁用',
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