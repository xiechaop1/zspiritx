<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class SessionModels extends \common\models\gii\SessionModels
{
    const IS_PICKUP_YES = 1;
    const IS_PICKUP_NO = 0;

    const IS_UNIQUE_YES = 1;
    const IS_UNIQUE_NO = 0;

    const IS_SET_YES    = 1;

    const IS_SET_NO     = 0;

    const SESSION_MODEL_STATUS_READY        = 0;    // 尚未被放置
    const SESSION_MODEL_STATUS_SET          = 1;    // 被放置
    const SESSION_MODEL_STATUS_OPERATING    = 2;    // 被操作
    const SESSION_MODEL_STATUS_PICKUP       = 3;    // 被拾取

    public static $sessionModelStatus2Name = [
        self::SESSION_MODEL_STATUS_READY      => '未放置',
        self::SESSION_MODEL_STATUS_SET        => '已放置',
        self::SESSION_MODEL_STATUS_OPERATING  => '被操作',
        self::SESSION_MODEL_STATUS_PICKUP     => '被拾取',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getModel(){
        return $this->hasOne('common\models\Models',  ['id' => 'model_id']);
    }
}