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

    public function add($sessionId, $sessionStageId, $toUser, $actDetail, $actType = \common\models\Actions::ACTION_TYPE_MSG, $expirationInterval = -1, $senderId = 0) {

        if ($expirationInterval > 0) {
            $expireTime = time() + $expirationInterval;
        } else {
            $expireTime = 0;
        }

        $model = \common\models\Actions::find()
        ->where([
            'session_id' => $sessionId,
            'session_stage_id' => $sessionStageId,
            'sender_id' => $senderId,
            'to_user' => $toUser,
            'action_type' => $actType,
            'action_detail' => $actDetail,
            'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL,
        ])
            ->andFilterWhere([
                'or',
                ['expire_time' => (int)0],
                ['>=', 'expire_time', time()],
            ])
            ->andFilterWhere([
                'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL
            ])
            ->one();

        if (empty($model)) {
            $model = new \common\models\Actions();
            $model->session_id = $sessionId;
            $model->session_stage_id = $sessionStageId;
            $model->to_user = $toUser;
            $model->sender_id = $senderId;
            $model->action_type = $actType;
            $model->action_detail = $actDetail;
            $model->expire_time = $expireTime;


            try {
                $r = $model->save();
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
                throw $e;
            }
        }

        return $model;
    }

    public function read($sessionId, $sessionStageId, $toUser, $actType = 0, $senderId = 0) {

        $models = \common\models\Actions::find()
            ->where([
                'session_id' => $sessionId,
                'sender_id' => $senderId,
                'to_user' => $toUser,
                'session_stage_id' => $sessionStageId,
                'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL,
            ])
            ->andFilterWhere([
                'or',
                ['expire_time' => (int)0],
                ['>=', 'expire_time', time()],
            ])
            ->andFilterWhere([
                'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL
            ]);

        if (!empty($models)) {
            $models->andFilterWhere([
                'action_type' => $actType,
            ]);
        }

        $models = $models->all();

        if (empty($models)) {

            try {
                foreach ($models as $model) {
                    $model->action_status = \common\models\Actions::ACTION_STATUS_READ;
                    $r = $model->save();
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
                throw $e;
            }
        }

        return $model;
    }

}