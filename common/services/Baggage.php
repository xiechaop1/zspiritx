<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Session;
use common\models\SessionModels;
use common\models\StoryModels;
use common\models\UserKnowledge;
use common\models\UserModels;
use yii\base\Component;
use yii;

class Baggage extends Component
{
    public function pickup($storyId, $sessionId, $storyModelId, $userId, $lockCt = 0, $userModelProp = []){
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

        if (empty($sessionModel)) {
            throw new \Exception('没有找到物品', ErrorCode::DO_MODELS_PICK_UP_FAIL);
        }


        if ($sessionModel->last_operator_id != $userId
            && $sessionModel->session_model_status == SessionModels::SESSION_MODEL_STATUS_OPERATING
        ) {
            throw new \Exception('物品正被他人拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
        } elseif ($sessionModel->is_unique == SessionModels::IS_UNIQUE_YES && $sessionModel->session_model_status == SessionModels::SESSION_MODEL_STATUS_PICKUP) {
            throw new \Exception('物品可能已经被拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
        }


//        $sessionModel->is_pickup = SessionModels::IS_PICKUP_YES;
        $sessionModel->session_model_status = SessionModels::SESSION_MODEL_STATUS_PICKUP;
        $sessionModel->last_operator_id = $userId;
        try {
            $ret = $sessionModel->save();

            $storyModelDetailId = !empty($storyModel->story_model_detail_id) ?
                $storyModel->story_model_detail_id : 0;

            $userModelBaggage = UserModels::find()
                ->where([
                    'user_id'           => (int)$userId,
                    'session_id'        => (int)$sessionId,
//                    'model_id'          => $sessionModel->model_id,
//                    'story_model_id'    => (int)$storyModelId,
//                    'session_model_id'  => $sessionModel->id,
                ]);
            if (!empty($storyModelDetailId)) {
                $userModelBaggage->andFilterWhere([
                    'story_model_detail_id' => $storyModelDetailId,
                ]);
            } else {
                $userModelBaggage->andFilterWhere([
                    'story_model_id' => $storyModelId,
                ]);
            }
            $userModelBaggage = $userModelBaggage->one();

            $userModelProp = !empty($userModelProp) ?
                json_encode($userModelProp, true) : '';

//            $retUserModelProp = [
//                'prop' => $userModelProp,
//            ];

            if (empty($userModelBaggage)) {
                $userModelBaggage = new UserModels();
                $userModelBaggage->user_id = $userId;
                $userModelBaggage->session_id = $sessionId;
                $userModelBaggage->story_id = $storyId;
                $userModelBaggage->model_id = $sessionModel->model_id;
                $userModelBaggage->story_model_id = $storyModelId;
                $userModelBaggage->story_model_detail_id = $storyModelDetailId;
                $userModelBaggage->session_model_id = $sessionModel->id;
                $userModelBaggage->user_model_prop = $userModelProp;
                $userModelBaggage->use_ct = 1;
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            } else {
                if (empty($lockCt)
                    || $userModelBaggage->use_ct < $lockCt
                ) {
                    $userModelBaggage->use_ct = $userModelBaggage->use_ct + 1;
                }
                $userModelBaggage->user_model_prop = $userModelProp;
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            }
            $transaction->commit();

            return $userModelBaggage;

        } catch (\Exception $e) {
//            var_dump($e);
            $transaction->rollBack();
            throw $e;
//            return $this->fail($e->getMessage(), $e->getCode());
        }
    }

}