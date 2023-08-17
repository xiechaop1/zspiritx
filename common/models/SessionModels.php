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
        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }
}