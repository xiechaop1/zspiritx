<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class ShopWares extends \common\models\gii\ShopWares
{

    const SHOP_WARE_TYPE_GAME_ITEM = 1;
    const SHOP_WARE_TYPE_VITUAL_WARE = 2;
    const SHOP_WARE_TYPE_REAL_WARE = 3;

    public static $shopWareType2Name = [
        self::SHOP_WARE_TYPE_GAME_ITEM => '游戏道具',
        self::SHOP_WARE_TYPE_VITUAL_WARE => '虚拟商品',
        self::SHOP_WARE_TYPE_REAL_WARE => '实物商品',
    ];

    const LINK_TYPE_STORY_MODEL = 1;

    public static $linkType2Name = [
        self::LINK_TYPE_STORY_MODEL => '模型',
    ];

    const SHOP_WARE_STATUS_NORMAL = 1;

    public static $shopWareStatus2Name = [
        self::SHOP_WARE_STATUS_NORMAL => '正常',
    ];

    const PAY_WAY_SCORE = 1;
    const PAY_WAY_MONEY = 2;

    public static $payWay2Name = [
        self::PAY_WAY_SCORE => '积分',
        self::PAY_WAY_MONEY => '现金',
    ];


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getStoryModel(){
        return $this->hasOne('common\models\StoryModels',  ['id' => 'link_id']);
    }


}