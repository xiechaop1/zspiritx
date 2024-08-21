<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\shop;


use common\definitions\Common;
use common\definitions\Cookies;
use common\definitions\ErrorCode;
use common\helpers\Active;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Knowledge;
use common\models\Log;
use common\models\Order;
use common\models\Qa;
use common\models\Session;
use common\models\SessionModels;
use common\models\SessionQa;
use common\models\SessionStages;
use common\models\ShopWares;
use common\models\Story;
use common\models\StoryExtend;
use common\models\StoryGoal;
use common\models\StoryModels;
use common\models\StoryModelsLink;
use common\models\StoryRank;
use common\models\StoryRole;
use common\models\StoryStages;
use common\models\User;
use common\models\UserKnowledge;
use common\models\UserModelLoc;
use common\models\UserModelsUsed;
use common\models\UserScore;
use common\models\UserStory;
use common\models\UserModels;
use frontend\actions\ApiAction;
use frontend\actions\order\Exception;
use Yii;

class ShopApi extends ApiAction
{
    public $action;
    private $_get;
    private $_userId;

    private $_storyId;
    private $_sessionId;
    private $_storyInfo;
    private $_userInfo;

    private $_userSessionInfo;

    private $_sessionInfo;

    private $_buildingId;

    public function run()
    {

        try {
            $this->valToken();

            $this->_get = Yii::$app->request->get();

            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

            $this->_buildingId = !empty($this->_get['building_id']) ? $this->_get['building_id'] : 0;

            $this->_sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;

            if (empty($this->_userId)) {
                return $this->fail('请您给出用户信息', ErrorCode::USER_NOT_FOUND);
            }

            if (empty($this->_get['story_id'])) {
                return $this->fail('请您给出剧本信息', ErrorCode::STORY_NOT_FOUND);
            } else {
                $this->_storyId = $this->_get['story_id'];

                // 检查剧本是否存在
                $this->_storyInfo = Story::findOne($this->_storyId);
                if (empty($this->_storyInfo)) {
                    return $this->fail('剧本不存在', ErrorCode::STORY_NOT_FOUND);
                }
            }

            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'buy':
                    $ret = $this->buyModels();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
//            var_dump($e);
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->success($ret);
    }

    /**
     * 获取剧本信息
     */
    public function getStory() {

        $this->_storyInfo['resources'] = Model::formatResources($this->_storyInfo['resources']);

        return $this->_storyInfo;
    }

    /**
     * 购买
     */
    public function buyModels() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;
        $lockCt = !empty($this->_get['lock_ct']) ? $this->_get['lock_ct'] : 0;
        $shopWareId = !empty($this->_get['shop_ware_id']) ? $this->_get['shop_ware_id'] : 0;

//        $priceScore = !empty($this->_get['price_score']) ? $this->_get['price_score'] : 0;

        $transaction = Yii::$app->db->beginTransaction();
//        $sessionModel = SessionModels::find()
//            ->where([
//                'session_id' => (int)$sessionId,
////                'story_id'  => (int)$storyId,
//                'story_model_id' => (int)$storyModelId,
////                'is_pickup' => SessionModels::IS_PICKUP_NO,
////                'session_model_status' => SessionModels::SESSION_MODEL_STATUS_PICKUP
//            ])
////            ->andFilterWhere([
////                'session_model_status' => [
////                    SessionModels::SESSION_MODEL_STATUS_PICKUP,
////                    SessionModels::SESSION_MODEL_STATUS_OPERATING,
////                ],
////            ])
//            ->one();
//
//        if (empty($sessionModel)) {
//            throw new \yii\base\Exception('没有找到物品', ErrorCode::DO_MODELS_PICK_UP_FAIL);
//        }

//        if (!empty($sessionModel)) {
//
//            if ($sessionModel->last_operator_id != $userId
//                && $sessionModel->session_model_status == SessionModels::SESSION_MODEL_STATUS_OPERATING
//            ) {
//                return $this->fail('物品正被他人拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
//            } elseif ($sessionModel->is_unique == SessionModels::IS_UNIQUE_YES && $sessionModel->session_model_status == SessionModels::SESSION_MODEL_STATUS_PICKUP) {
//                return $this->fail('物品可能已经被拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
//            }
//        }

        $shopWare = ShopWares::find()
            ->where([
                'id' => $shopWareId,
                'is_delete' => Common::STATUS_NORMAL,
            ])
            ->one();

        if (!empty($shopWare)) {
            if (!empty($shopWare->discount)) {
                $priceScore = $shopWare->discount;
            } else {
                $priceScore = $shopWare->price;
            }

            $storyModelId = $shopWare->link_id;
        } else {
            throw new \yii\base\Exception('商品不存在', ErrorCode::SHOP_WARE_NOT_EXIST);
        }

        if (!empty($priceScore)) {
            $userScore = UserScore::find()
                ->where([
                    'user_id' => $userId,
//                    'session_id' => $sessionId,
                    'story_id' => $storyId,
                ])
                ->one();

            if (!empty($userScore)) {
                    if ($userScore->score < $priceScore) {
                        throw new \yii\base\Exception('金币不足', ErrorCode::SHOP_BUY_NOT_ENOUGH_SCORE);
                    }
                $userScore->score = $userScore->score - $priceScore;
                $userScore->save();
            } else {
                throw new \yii\base\Exception('金币不足', ErrorCode::SHOP_BUY_NOT_ENOUGH_SCORE);
            }
        }

//        $sessionModel->is_pickup = SessionModels::IS_PICKUP_YES;
//        $sessionModel->session_model_status = SessionModels::SESSION_MODEL_STATUS_PICKUP;
//        $sessionModel->last_operator_id = $userId;

        $storyModel = StoryModels::find()
            ->where([
                'id' => $storyModelId,
//                'status' => Common::STATUS_NORMAL,
            ])
            ->one();


        try {
//            $ret = $sessionModel->save();

            $storyModelDetailId = !empty($storyModel->story_model_detail_id) ? $storyModel->story_model_detail_id : 0;

            if (empty($sessionId)) {
                $tmpUserModel = UserModels::find()
                    ->where(['user_id' => $userId])
                    ->one();

                $sessionId = !empty($tmpUserModel->session_id) ? $tmpUserModel->session_id : 0;
            }

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
            if (empty($userModelBaggage)) {
                $userModelBaggage = new UserModels();
                $userModelBaggage->user_id = $userId;
                $userModelBaggage->session_id = $sessionId;
                $userModelBaggage->story_id = $storyId;
                $userModelBaggage->model_id = $storyModel->model_id;
                $userModelBaggage->story_model_id = $storyModelId;
                $userModelBaggage->story_model_detail_id = $storyModelDetailId;
//                $userModelBaggage->session_model_id = $sessionModel->id;
                $userModelBaggage->use_ct = 1;
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            } else {
                if (empty($lockCt)
                    || $userModelBaggage->use_ct < $lockCt
                ) {
                    $userModelBaggage->use_ct = $userModelBaggage->use_ct + 1;
                }
                $userModelBaggage->is_delete = \common\definitions\Common::STATUS_NORMAL;
                $ret = $userModelBaggage->save();
            }
            $transaction->commit();

            $this->_get['pre_story_model_id'] = $storyModelId;

            $result['data'] = $storyModel;
            $result['baggage'] = $userModelBaggage;

//            $storyModel = StoryModels::find()
//                ->with('buff')
//                ->where(['id' => (int)$storyModelId])
//                ->one();

//            if ($storyModel->active_next)

            $userScore->score = $userScore->score - $priceScore;
//            $userScore->save();

            $userScoreRet = $userScore->toArray();
            $userScoreRet['score'] = \common\helpers\Common::formatNumberToStr($userScore->score, true);
            $result['user_score'] = $userScoreRet;


            $result['msg'] = '购买成功';

        } catch (\Exception $e) {
//            var_dump($e);
            $transaction->rollBack();
//            throw $e;
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $result;
    }

}