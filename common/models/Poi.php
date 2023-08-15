<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Poi extends \common\models\gii\Poi
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
            'poi_name',
        ];
    }

    public function attributeLabels()
    {
        return [
            'poi_name' => 'POI Name',
        ];
    }
}