<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class UserKnowledge extends \common\models\gii\UserKnowledge
{

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getSession(){
        return $this->hasOne('common\models\Session', ['id' => 'session_id']);
    }

    public function getKnowledge(){
        return $this->hasOne('common\models\Knowledge', ['id' => 'knowledge_id']);
    }

    public function getUser() {
        return $this->hasOne('common\models\User', ['id' => 'user_id']);
    }


}