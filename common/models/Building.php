<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Building extends \common\models\gii\Building
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
            'building_name',
        ];
    }

    public function attributeLabels()
    {
        return [
            'building_name' => 'Building Name',
        ];
    }
}