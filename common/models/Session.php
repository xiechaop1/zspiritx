<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Session extends \common\models\gii\Session
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function fields()
    {
        return [
            'id',
            'team_name',
        ];
    }

    public function attributeLabels()
    {
        return [
            'session_name' => 'Session Name',
        ];
    }
}