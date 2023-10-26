<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:37 PM
 */

namespace common\models;


use common\definitions\Common;

class Order extends \common\models\gii\Order
{

    const ORDER_STATUS_ALL          = -1;   // 全部
    const ORDER_STATUS_WAIT         = 0;    // 待支付
    const ORDER_STATUS_PAIED        = 1;    // 已支付
    const ORDER_STATUS_COMPLETED    = 2;    // 已结束
    const ORDER_STATUS_CANCELED     = 3;    // 已取消

    public static $orderStatus = [
        self::ORDER_STATUS_ALL          => '全部',
        self::ORDER_STATUS_WAIT         => '待支付',
        self::ORDER_STATUS_PAIED        => '购买完成',
        self::ORDER_STATUS_COMPLETED    => '已结束',
        self::ORDER_STATUS_CANCELED     => '已取消',
    ];

    const PAY_METHOD_WECHAT = 1; // 微信支付
    const PAY_METHOD_ALIPAY = 2; // 支付宝支付

    public static $payMethod2Name = [
        self::PAY_METHOD_WECHAT => '微信支付',
        self::PAY_METHOD_ALIPAY => '支付宝支付',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    // 获取用户信息
    public function getUser()
    {
        return $this->hasOne('common\models\User', ['id' => 'user_id']);
    }

    // 获取音乐信息
    public function getStory()
    {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }

}