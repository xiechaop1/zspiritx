<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Models extends \common\models\gii\Models
{
    const IS_ACTIVE_YES = 1;    // 是动画
    const IS_ACTIVE_NO = 0;     // 不是动画

    public static $isActive2Name = [
        self::IS_ACTIVE_YES => '是动画',
        self::IS_ACTIVE_NO => '不是动画',
    ];

    const MODEL_TYPE_NORMAL = 1;    // 普通
    const MODEL_TYPE_PARTICLE = 2;  // 粒子
    const MODEL_TYPE_SOUND = 3;     // 音效

    public static $modelType2Name = [
        self::MODEL_TYPE_NORMAL => '普通',
        self::MODEL_TYPE_PARTICLE => '粒子',
        self::MODEL_TYPE_SOUND => '音效',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

}