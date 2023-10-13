<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\processh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\SessionModels;
use common\models\Story;
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

            $userModelBaggage = UserModels::find()
                ->where([
                    'user_id'           => (int)$userId,
                    'session_id'        => (int)$sessionId,
                    'model_id'          => $sessionModel->model_id,
//                    'story_model_id'    => (int)$storyModelId,
//                    'session_model_id'  => $sessionModel->id,
                ])
                ->one();
            if (empty($userModelBaggage)) {
                $userModelBaggage = new UserModels();
                $userModelBaggage->user_id = $userId;
                $userModelBaggage->session_id = $sessionId;
                $userModelBaggage->model_id = $sessionModel->model_id;
                $userModelBaggage->story_model_id = $storyModelId;
                $userModelBaggage->session_model_id = $sessionModel->id;
                $userModelBaggage->use_ct = 1;
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            } else {
                $userModelBaggage->use_ct = $userModelBaggage->use_ct + 1;
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

            $storyModelName = !empty($storyModel->story_model_name) ? $storyModel->story_model_name : '未知物品';
            $msg = '您成功获取了 ' . $storyModelName;

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
        ]);
    }
}