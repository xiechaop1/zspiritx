<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserModels extends \common\models\gii\Us\erModels
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getModels(){
        return $this->hasOne('common\models\Models',  ['id' => 'model_id']);
    }

    public function getSessionModels(){
        return $this->hasOne('common\models\SessionModels',  ['id' => 'session_model_id']);
    }

}