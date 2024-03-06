<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:37 PM
 */

namespace common\models;


class LotteryPrize extends \common\models\gii\LotteryPrize
{


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    // 获取用户信息
    public function getStory()
    {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }



}