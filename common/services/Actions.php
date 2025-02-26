<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\models\Session;
use common\models\SessionStages;
use common\models\StoryStages;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class Actions extends Component
{

    public function get($sessionId, $userId, $actionStatus = \common\models\Actions::ACTION_STATUS_NORMAL, $isRead = 0) {
        $actions = \common\models\Actions::find()
            ->where([
                'session_id' => (int)$sessionId,
            ])
            ->andFilterWhere([
                'or',
                ['to_user' => (int)$userId],
                ['to_user' => 0],
            ])
            ->andFilterWhere([
                'or',
                ['expire_time' => (int)0],
                ['>=', 'expire_time', time()],
            ])
            ->andFilterWhere([
                'action_status' => $actionStatus
            ])
            ->orderBy([
                'updated_at' => SORT_DESC,
                'id' => SORT_ASC,
            ])
//            ->createCommand()->getRawSql();
//        var_dump($actions);exit;
            ->all();

        if ($isRead == 1) {
            foreach ($actions as $act) {
                $act->action_status = \common\models\Actions::ACTION_STATUS_READ;
                $act->save();

                if ( \common\helpers\Common::isJson($act->action_detail) ) {
                    $act->action_detail = json_decode($act->action_detail, true);
                }
            }
        }

        return $actions;
    }

    public function add($sessionId, $sessionStageId, $storyId, $toUser, $actDetail, $actType = \common\models\Actions::ACTION_TYPE_MSG, $expirationInterval = -1, $senderId = 0) {

        // 如果类型是转场
        // $sessionStageId 获取为新StageID
        if ($actType == \common\models\Actions::ACTION_TYPE_CHANGE_STAGE) {
            $stageUId = $actDetail;

//            $session = Session::find()
//                ->where(['id' => $sessionId])
//                ->one();
//
//            if (!empty($session)) {
                $storyStage = StoryStages::find()
                    ->where([
                        'story_id' => $storyId,
                        'stage_u_id' => $stageUId,
                    ])
                    ->one();
                if (!empty($storyStage)) {
                    $sessionStage = SessionStages::find()
                        ->where([
                            'session_id' => $sessionId,
                            'story_stage_id' => $storyStage->id,
                            'story_id' => $storyId,
                        ])
                        ->one();

                    if (!empty($sessionStage)) {
                        $sessionStageId = $sessionStage->id;
                    }
                }
//            }

        }
        if ($actType == \common\models\Actions::ACTION_TYPE_MODEL_DISPLAY) {
            if (strpos($actDetail, ',') !== false) {
                $actDetail = explode(',', $actDetail);
            } else {
                $actDetail = [$actDetail];
            }
            $actDetail = json_encode(['showModels' => $actDetail ]);
        }

        $model = $this->_exec($sessionId, $sessionStageId, $storyId, $toUser,
            $actDetail, $actType, $expirationInterval, $senderId);


        return $model;
    }

    public function addWithTag($sessionId, $sessionStageId, $storyId, $toUser, $actDetail, $actType = \common\models\Actions::ACTION_TYPE_MSG, $expirationInterval = -1, $senderId = 0, $tag = '') {

        if (!is_array($actDetail)
            && \common\helpers\Common::isJson($actDetail)
        ) {
            $actDetail = json_decode($actDetail, true);
        }
        $actDetail = [
            $tag => $actDetail
        ];
        $actDetail = json_encode($actDetail, JSON_UNESCAPED_UNICODE);

        $model = $this->addWithoutTag($sessionId, $sessionStageId, $storyId, $toUser, $actDetail, $actType, $expirationInterval, $senderId);

        return $model;
    }

    public function addWithoutTag($sessionId, $sessionStageId, $storyId, $toUser, $actDetail, $actType = \common\models\Actions::ACTION_TYPE_MSG, $expirationInterval = -1, $senderId = 0)
    {
        if (is_array($actDetail)) {
            $actDetail = json_encode($actDetail, JSON_UNESCAPED_UNICODE);
        }
        return $this->_exec($sessionId, $sessionStageId, $storyId, $toUser,
            $actDetail, $actType, $expirationInterval, $senderId);
    }

    public function _exec($sessionId, $sessionStageId, $storyId, $toUser, $actDetail, $actType = \common\models\Actions::ACTION_TYPE_MSG, $expirationInterval = -1, $senderId = 0) {

        if ($expirationInterval > 0) {
            $expireTime = time() + $expirationInterval;
        } else {
            $expireTime = 0;
        }

        // 如果类型是转场
        // $sessionStageId 获取为新StageID
        if ($actType == \common\models\Actions::ACTION_TYPE_CHANGE_STAGE) {
            $stageUId = $actDetail;

//            $session = Session::find()
//                ->where(['id' => $sessionId])
//                ->one();
//
//            if (!empty($session)) {
            $storyStage = StoryStages::find()
                ->where([
                    'story_id' => $storyId,
                    'stage_u_id' => $stageUId,
                ])
                ->one();
            if (!empty($storyStage)) {
                $sessionStage = SessionStages::find()
                    ->where([
                        'session_id' => $sessionId,
                        'story_stage_id' => $storyStage->id,
                        'story_id' => $storyId,
                    ])
                    ->one();

                if (!empty($sessionStage)) {
                    $sessionStageId = $sessionStage->id;
                }
            }
//            }

        }
//        if ($actType == \common\models\Actions::ACTION_TYPE_MODEL_DISPLAY) {
//            if (strpos($actDetail, ',') !== false) {
//                $actDetail = explode(',', $actDetail);
//            } else {
//                $actDetail = [$actDetail];
//            }
//            $actDetail = json_encode(['showModels' => $actDetail ]);
//        }

        $model = \common\models\Actions::find()
            ->where([
                'session_id' => $sessionId,
                'session_stage_id' => $sessionStageId,
                'sender_id' => $senderId,
                'to_user' => $toUser,
                'action_type' => $actType,
                'action_detail' => $actDetail,
//            'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL,
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
        }

        try {
            $r = $model->save();

            $model->id = Yii::$app->db->getLastInsertID();
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            throw $e;
        }


        return $model;
    }

    public function hideModels($sessionId, $storyId, $modelUId) {

        if (is_array($modelUId)) {
            $modelUIds = $modelUId;
        } else {
            $modelUIds = [$modelUId];
        }

        $msg = json_encode([
            'hideModels' => $modelUIds,
        ]);
        return $this->_exec((int)$sessionId, 0,
            $storyId, 0, $msg, \common\models\Actions::ACTION_TYPE_MODEL_DISPLAY);
    }

    public function naviModel($sessionId, $storyId, $modelUId, $userId = 0) {

            $msg = json_encode([
                'naviModel' => $modelUId,
            ]);
            return $this->_exec((int)$sessionId, 0,
                $storyId, $userId, $msg, \common\models\Actions::ACTION_TYPE_MODEL_DISPLAY);
    }

    public function readOne($actionId) {
        $model = \common\models\Actions::find()
            ->where([
                'id' => $actionId,
            ])
            ->one();

        if (!empty($model)) {
            $model->action_status = \common\models\Actions::ACTION_STATUS_READ;
            $r = $model->save();
        }

        return $model;
    }

    public function read($sessionId, $sessionStageId, $toUser, $actType = 0, $senderId = 0) {

        $models = \common\models\Actions::find()
            ->where([
                'session_id' => $sessionId,
                'sender_id' => $senderId,
                'to_user' => $toUser,
//                'session_stage_id' => $sessionStageId,
//                'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL,
            ])
            ->andFilterWhere([
                'or',
                ['expire_time' => (int)0],
                ['>=', 'expire_time', time()],
            ])
            ->andFilterWhere([
                'action_status' => \common\models\Actions::ACTION_STATUS_NORMAL
            ]);

        $noSessionStage = false;
        if (!empty($sessionStageId)) {
            $models = $models->andFilterWhere([
                'session_stage_id'  => $sessionStageId,
            ]);
        } else {
            $noSessionStage = true;
        }

        if (!empty($actType)) {
            $models = $models->andFilterWhere([
                'action_type' => $actType,
            ]);
        }

        $models = $models->all();

        if (!empty($models)) {

            try {
                foreach ($models as $model) {
//                    if ($noSessionStage && $model->action_type == \common\models\Actions::ACTION_TYPE_CHANGE_STAGE) {
//                        continue;
//                    }
                    $model->action_status = \common\models\Actions::ACTION_STATUS_READ;
                    $r = $model->save();
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
                throw $e;
            }
        }

        return $models;
    }

}