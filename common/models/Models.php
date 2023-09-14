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

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

}