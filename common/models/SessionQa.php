<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class SessionQa extends \common\models\gii\SessionQa
{

    const SESSION_QA_STATUS_IS_ANSWER = 1;     // 已答题
    const SESSION_QA_STATUS_NOT_ANSWER = 2;    // 未答题

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getQa(){
        return $this->hasOne('common\models\Qa',  ['id' => 'qa_id']);
    }

    public function getSession(){
        return $this->hasOne('common\models\Session',  ['id' => 'session_id']);
    }

    public function getAnswerUser(){
        return $this->hasOne('common\models\User',  ['id' => 'answer_user_id']);
    }

}