<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:37 PM
 */

namespace common\models;


class PrizeLog extends \common\models\gii\PrizeLog
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
    public function getUser()
    {
        return $this->hasOne('common\models\User', ['id' => 'user_id']);
    }



}