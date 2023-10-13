<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class Actions extends Component
{

    public function add($sessionId, $toUser, $actDetail, $actType = \common\models\Actions::ACTION_TYPE_MSG, $expireTime = 0, $senderId = 0) {
        $model = \common\models\Actions::find()
        ->where([
            'session_id' => $sessionId,
            'sender_id' => $senderId,
            'to_user' => $toUser,
            'action_type' => $actType,
            'action_detail' => $actDetail,
            'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL,
        ])
            ->andFilterWhere([
                '<', 'expire_time', time()
            ])
            ->one();

        if (empty($model)) {
            $model = new \common\models\Actions();
            $model->session_id = $sessionId;
            $model->to_user = $toUser;
            $model->sender_id = $senderId;
            $model->action_type = $actType;
            $model->action_detail = $actDetail;
            $model->expire_time = $expireTime;


            try {
                $r = $model->save();
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
            }
        }

        return $model;
    }

}