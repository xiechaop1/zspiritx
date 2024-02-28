<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\processh5;


use common\definitions\ErrorCode;
use common\models\Knowledge;
use common\models\SessionModels;
use common\models\StoryModels;
use common\models\UserModels;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Pickup extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $knowledgeId = !empty($this->_get['knowledge_id']) ? $this->_get['knowledge_id'] : 0;

        $needAction = !empty($this->_get['need_action']) ? $this->_get['need_action'] : 0;
        $actDetail = !empty($this->_get['act_detail']) ? $this->_get['act_detail'] : 0;
        $actType = !empty($this->_get['act_type']) ? $this->_get['act_type'] : \common\models\Actions::ACTION_TYPE_MSG;
        $expirationInterval = !empty($this->_get['expiration_interval']) ? $this->_get['expiration_interval'] : -1;

        $lockCt = !empty($this->get['lock_ct']) ? $this->get['lock_ct'] : 0;

        $transaction = Yii::$app->db->beginTransaction();
        $sessionModel = SessionModels::find()
            ->where([
                'session_id' => (int)$sessionId,
//                'story_id'  => (int)$storyId,
                'story_model_id' => (int)$storyModelId,
//                'is_pickup' => SessionModels::IS_PICKUP_NO,
//                'session_model_status' => SessionModels::SESSION_MODEL_STATUS_PICKUP
            ])
//            ->andFilterWhere([
//                'session_model_status' => [
//                    SessionModels::SESSION_MODEL_STATUS_PICKUP,
//                    SessionModels::SESSION_MODEL_STATUS_OPERATING,
//                ],
//            ])
            ->one();

        $storyModel = StoryModels::find()
            ->where(['id' => (int)$storyModelId])
            ->with('buff')
            ->one();

        $this->_params = [
            'session_id'    => $sessionId,
            'user_id'       => $userId,
            'story_model_id'    => $storyModelId,
            'session_model'     => $sessionModel,
            'story_model'       => $storyModel,
        ];

        $code = 0;
        $msg = '';
        if (empty($storyModel)) {
            $msg = '没有找到物品';
            $code = ErrorCode::DO_MODELS_PICK_UP_FAIL;
        }

        if (empty($sessionModel)) {
            $msg = '没有找到物品';
            $code = ErrorCode::DO_MODELS_PICK_UP_FAIL;
        }

        if ($code != 0) {
            return $this->pickupRender($code, $msg, $this->_params);
        }

        $sessionModel->session_model_status = SessionModels::SESSION_MODEL_STATUS_PICKUP;
        $sessionModel->last_operator_id = $userId;
        try {
            $ret = $sessionModel->save();

            $storyModelDetailId = !empty($storyModel->story_model_detail_id) ? $storyModel->story_model_detail_id : 0;

            $userModelBaggage = UserModels::find()
                ->where([
                    'user_id'           => (int)$userId,
                    'session_id'        => (int)$sessionId,
//                    'model_id'          => $sessionModel->model_id,
//                    'story_model_id'    => (int)$storyModelId,
//                    'session_model_id'  => $sessionModel->id,
                ]);
            if (!empty($storyModelDetailId)) {
                $userModelBaggage->andFilterWhere(['story_model_detail_id' => $storyModelDetailId]);
            } else {
                $userModelBaggage->andFilterWhere(['story_model_id' => (int)$storyModelId]);
            }
                $userModelBaggage = $userModelBaggage->one();
            if (empty($userModelBaggage)) {
                $userModelBaggage = new UserModels();
                $userModelBaggage->user_id = $userId;
                $userModelBaggage->session_id = $sessionId;
                $userModelBaggage->story_id = $storyId;
                $userModelBaggage->model_id = $sessionModel->model_id;
                $userModelBaggage->story_model_id = $storyModelId;
                $userModelBaggage->story_model_detail_id = $storyModelDetailId;
                $userModelBaggage->session_model_id = $sessionModel->id;
                $userModelBaggage->use_ct = 1;
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            } else {
//                $userModelBaggage->use_ct = $userModelBaggage->use_ct + 1;
                if (empty($lockCt)
                    || $userModelBaggage->use_ct < $lockCt
                ) {
                    $userModelBaggage->use_ct = $userModelBaggage->use_ct + 1;
                }
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            }
            $transaction->commit();

//            $this->_get['pre_story_model_id'] = $storyModelId;

//            $result['data'] = $this->getSessionModels();

//            $storyModel = StoryModels::find()
//                ->with('buff')
//                ->where(['id' => (int)$storyModelId])
//                ->one();

//            if ($storyModel->active_next)

            $storyModelName = !empty($storyModel->story_model_name) ? $storyModel->story_model_name : $storyModel->model->model_name;
            $msg = '您成功获取了 <font color=yellow>' . $storyModelName . '</font>';

            $this->_params['knowledge'] = [];
            if (!empty($knowledgeId)) {
                Yii::$app->knowledge->set($knowledgeId, $sessionId, $sessionStageId, $userId, $storyId);
                $knowledge = Knowledge::find()
                    ->where(['id' => $knowledgeId])
                    ->one();

                if ($knowledge->knowledge_class == Knowledge::KNOWLEDGE_CLASS_NORMAL) {
                    $this->_params['knowledge'] = $knowledge;
                }
            }

            if ($needAction == '1') {
                if ($actType == \common\models\Actions::ACTION_TYPE_CHANGE_STAGE) {
                    Yii::$app->act->read($sessionId, 0, $userId, $actType);
                }
                $ret = Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, $actDetail, $actType, $expirationInterval);
            }

        } catch (\Exception $e) {
//            var_dump($e);
            $transaction->rollBack();
            return $this->pickupRender($e->getCode(), $e->getMessage(), $this->_params);
//            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->pickupRender(0, $msg, $this->_params);

    }

    public function pickupRender($code = 0, $msg = '', $params) {
        return $this->controller->render('pickup', [
            'storyModel'            => $params['story_model'],
            'sessionModel'          => $params['session_model'],
            'params'        => $_GET,
            'userId'        => $params['user_id'],
            'sessionId'     => $params['session_id'],
            'msg'           => $msg,
            'knowledge'     => $params['knowledge'],
        ]);
    }
}