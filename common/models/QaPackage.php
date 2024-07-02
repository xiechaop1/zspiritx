<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class QaPackage extends \common\models\gii\QaPackage
{

    const PACKAGE_STATUS_NORMAL = 1; // 正常
    const PACKAGE_STATUS_OFFLINE = 2; // 下架

    public static $packageStatus2Name = [
        self::PACKAGE_STATUS_NORMAL => '正常',
        self::PACKAGE_STATUS_OFFLINE => '下架',
    ];

    const PACKAGE_TYPE_NORMAL = 1; // 普通题包

    public static $packageType2Name = [
        self::PACKAGE_TYPE_NORMAL => '普通题包',
    ];

    const PACKAGE_CLASS_NORMAL        = 1; // 普通
    const PACKAGE_CLASS_MATH          = 20; // 数学
    const PACKAGE_CLASS_ENGLISH       = 30; // 英语
    const PACKAGE_CLASS_CHINESE       = 40; // 语文
    const PACKAGE_CLASS_POEM          = 41; // 诗词
    const PACKAGE_CLASS_POEM_IDIOM    = 42; // 成语
    const PACKAGE_CLASS_HISTORY       = 50; // 历史
    const PACKAGE_CLASS_PHYSICS       = 60; // 物理

    public static $packageClass2Name = [
        self::PACKAGE_CLASS_NORMAL => '普通',
        self::PACKAGE_CLASS_MATH => '数学',
        self::PACKAGE_CLASS_ENGLISH => '英语',
        self::PACKAGE_CLASS_CHINESE => '语文',
        self::PACKAGE_CLASS_POEM => '诗词',
        self::PACKAGE_CLASS_POEM_IDIOM => '成语',
        self::PACKAGE_CLASS_HISTORY => '历史',
        self::PACKAGE_CLASS_PHYSICS => '物理',
    ];


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }


}