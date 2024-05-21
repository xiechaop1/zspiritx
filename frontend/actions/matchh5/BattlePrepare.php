<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\matchh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryModels;
use common\models\User;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class BattlePrepare extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $matchName = !empty($_GET['match_name']) ? $_GET['match_name'] : '';
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;
        $poiId = !empty($_GET['poi_id']) ? $_GET['poi_id'] : 0;
        $rivalUserModelIds = !empty($_GET['rival_user_model_ids']) ? $_GET['rival_user_model_ids'] : 0;
        $rivalUserId = !empty($_GET['rival_user_id']) ? $_GET['rival_user_id'] : 0;
        $rivalStoryModelIds = !empty($_GET['rival_story_model_ids']) ? $_GET['rival_story_model_ids'] : 0;
        $rivalLocationId = !empty($_GET['rival_location_id']) ? $_GET['rival_location_id'] : 0;

        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;
        $storyModelDetailId = !empty($_GET['story_model_detail_id']) ? $_GET['story_model_detail_id'] : 0;
//        $rivalStoryModelDetailId = !empty($_GET['rival_story_model_detail_id']) ? $_GET['rival_story_model_detail_id'] : 0;
        $userModelIds = !empty($_GET['user_model_ids']) ? $_GET['user_model_ids'] : 0;

        if (empty($userId) || empty($sessionId) || empty($storyId)) {
//            throw new Exception('参数错误', ErrorCode::PARAMS_ERROR);
            return $this->renderErr('参数错误');
        }

        $userInfo = User::find()
            ->where(['id' => $userId])
            ->one();

        if (empty($matchId) && empty($matchName)) {
            $matchName = date('Y-m-d H:i:s') . ' ' . $userInfo->user_name . '发起对战';
        }

        if (!empty($matchId)) {
            $storyMatch = StoryMatch::find()
                ->where([
                    'id' => $matchId,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'story_id' => $storyId,
                    'story_match_status' => StoryMatch::STORY_MATCH_STATUS_PREPARE,
                ])
                ->one();

            if (empty($storyMatch)) {
//                throw new Exception('对战不存在', ErrorCode::STORY_MATCH_NOT_READY);
                return $this->renderErr('对战不存在');
            }
            $matchId = $storyMatch->id;
        } else {
//            $storyMatch = StoryMatch ::find()
//                ->where([
//                    'user_id' => $userId,
//                    'session_id' => $sessionId,
//                    'story_id' => $storyId,
//                    'm_story_model_id' => $rivalStoryModelIds,
//                    'story_match_status' => StoryMatch::STORY_MATCH_STATUS_PREPARE,
            $storyMatch = new StoryMatch();
            $storyMatch->story_id = $storyId;
            $storyMatch->user_id = $userId;
            $storyMatch->session_id = $sessionId;
            $storyMatch->match_name = $matchName;
            $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PREPARE;
//            $storyMatch->match_id = time() . rand(1000, 9999);
            $storyMatch->save();
            $matchId = Yii::$app->db->getLastInsertID();
        }


        if (!empty($userModelIds)) {
            if (strpos($userModelIds, ',') !== false) {
                $userModelIdArray = explode(',', $userModelIds);
            } else {
                $userModelIdArray = [$userModelIds];
            }
        } else {
            $userModelIdArray = [];
        }



        $storyMatchPlayers = StoryMatchPlayer::find()
            ->where(['match_id' => $matchId])
            ->all();

        if (!empty($storyMatchPlayers)) {
            foreach ($storyMatchPlayers as $player) {
//                if (
//                    in_array($match->user_model_id, $userModelIdArray)
//                    || in_array($match->user_model_id, $rivalUserModelIdArray)
//                ) {
//                    continue;
//                }
                $player->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_CANCEL;
                $player->save();
            }
        }
            if (empty($userModelIds)) {
                $userModels = UserModels::find()
                    ->where([
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'story_id' => $storyId,
//                'story_model_id' => $storyModelId,
                    ]);
                if (!empty($storyModelDetailId)) {
                    $userModels->andWhere(['story_model_detail_id' => $storyModelDetailId]);
                } else {
                    $userModels->andWhere(['story_model_id' => $storyModelId]);
                }

                $userModels = $userModels->all();
            } else {
                $userModels = UserModels::find()
                    ->where([
                        'id' => $userModelIdArray,
                    ])
                    ->all();

            }

        if (!empty($rivalUserModelIds)) {
            if (strpos($rivalUserModelIds, ',') !== false) {
                $rivalUserModelIdArray = explode(',', $rivalUserModelIds);
            } else {
                $rivalUserModelIdArray = [$rivalUserModelIds];
            }

            $rivalUserModels = UserModelLoc::find()
                ->where([
                    'id' => $rivalUserModelIdArray,
                    'user_model_loc_status' => UserModelLoc::USER_MODEL_LOC_STATUS_LIVE,
                ])
                ->all();
        } else {
            $rivalUserModelIdArray = [];
        }

        if (empty($rivalUserModels)
            && !empty($rivalLocationId)
        ) {
            $rivalUserModels = UserModelLoc::find()
                ->where([
                    'location_id' => $rivalLocationId,
                    'story_id' => $storyId,
                    'user_model_loc_status' => UserModelLoc::USER_MODEL_LOC_STATUS_LIVE,
                ])
                ->all();
        }

        if (empty($rivalUserModels)
            && !empty($rivalStoryModelIds)
        ) {
            if (strpos($rivalStoryModelIds, ',') !== false) {
                $rivalStoryModelIdArray = explode(',', $rivalStoryModelIds);
            } else {
                $rivalStoryModelIdArray = [$rivalStoryModelIds];
            }
            $rivalStoryModels = StoryModels::find()
                ->where([
                    'id' => $rivalStoryModelIdArray,
                ])
                ->all();
        }


            if (empty($userModels)) {
//                throw new Exception('您没有选择宠物出战，请重新选择！', ErrorCode::STORY_MATCH_NOT_MODEL_READY);
                return $this->renderErr('您没有选择宠物出战，请重新选择！');
            }

            $ct = 0;

//        if (!empty($userModel)) {
//            $storyModelId = $userModel->story_model_id;
//            $storyModelDetailId = $userModel->story_model_detail_id;
//        } else {
//            $storyModelId = 0;
//            $storyModelDetailId = 0;
//        }

//        $matchId = time() . rand(1000, 9999);

        $ct = 0;
        $showRivalStoryModel = [];
        if (!empty($rivalUserModels)) {
            foreach ($rivalUserModels as $rivalUserModel) {
                $userProp = Model::getUserModelProp($rivalUserModel);
                if (empty($userProp)) {
                    continue;
                }

                $matchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE;
                if (!empty($userProp['prop']['hp'])
                    && $userProp['prop']['hp'] <= 20
                ) {
                    $matchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_INJURED;
                }

                $storyMatchPlayer = new StoryMatchPlayer();
                $storyMatchPlayer->user_id = $rivalUserId;
                $storyMatchPlayer->team_id = 2;
                $storyMatchPlayer->match_id = $matchId;
                $storyMatchPlayer->match_player_status = $matchPlayerStatus;
                $storyMatchPlayer->user_model_id = $rivalUserModel->id;
                $storyMatchPlayer->m_story_model_id = !empty($rivalUserModel->storyModel->id) ? $rivalUserModel->storyModel->id : 0;
                $storyMatchPlayer->m_story_model_detail_id = !empty($rivalUserModel->storyModelDetail->id) ? $rivalUserModel->storyModelDetail->id : 0;
                $storyMatchPlayer->m_user_model_prop = $rivalUserModel->user_model_prop;
                $storyMatchPlayer->save();

//                if (empty($showRivalStoryModel)) {
                    $showRivalStoryModel[] = $rivalUserModel->storyModel;
//                }

                if ($matchPlayerStatus == StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE) {
                    $ct++;
                }
            }
        } else if (!empty($rivalStoryModels)) {
            foreach ($rivalStoryModels as $rivalStoryModel) {
                if ($rivalStoryModel->story_model_class != StoryModels::STORY_MODEL_CLASS_PET) {
                    return $this->renderErr('你选择的对手不能参加战斗！');
                }

                $userPropTmp = Model::getUserModelProp($rivalStoryModel, 'story_model_prop');
                $userProp = [];
                if (!empty($userPropTmp['prop'])) {
                    $userProp['prop'] = $userPropTmp['prop'];
                } elseif (!empty($userPropTmp['init_formula'])) {
                    eval($userPropTmp['init_formula'] . ';');
                    $userProp['prop'] = $ret;
                }

                if (empty($userProp)) {
                    continue;
                }

                $matchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE;
//                if (!empty($userProp['prop']['hp'])
//                    && $userProp['prop']['hp'] <= 20
//                ) {
//                    $matchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_INJURED;
//                }

                $storyMatchPlayer = new StoryMatchPlayer();
                $storyMatchPlayer->user_id = 0;
                $storyMatchPlayer->team_id = 2;
                $storyMatchPlayer->match_id = $matchId;
                $storyMatchPlayer->match_player_status = $matchPlayerStatus;
                $storyMatchPlayer->user_model_id = 0;
                $storyMatchPlayer->m_story_model_id = !empty($rivalStoryModel->id) ? $rivalStoryModel->id : 0;
                $storyMatchPlayer->m_story_model_detail_id = !empty($rivalStoryModel->story_model_detail_id) ? $rivalStoryModel->story_model_detail_id : 0;
                $storyMatchPlayer->m_user_model_prop = json_encode($userProp);
                $storyMatchPlayer->save();

//                if (empty($showRivalStoryModel)) {
                    $showRivalStoryModel[] = $rivalStoryModel;
//                }

                if ($matchPlayerStatus == StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE) {
                    $ct++;
                }

            }
        }

        if ($ct == 0) {
            $storyMatchStatus = StoryMatch::STORY_MATCH_STATUS_END;
            $storyMatchRet = StoryMatch::STORY_MATCH_RESULT_WIN;
            $storyMatchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END;
            $storyMatchPlayerRet = StoryMatchPlayer::STORY_MATCH_RESULT_WIN;
        } else {
            $storyMatchStatus = StoryMatch::STORY_MATCH_STATUS_PREPARE;
//            $storyMatchRet = StoryMatch::STORY_MATCH_RESULT_WAITTING;
            $storyMatchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE;
            $storyMatchPlayerRet = StoryMatchPlayer::STORY_MATCH_RESULT_WAITTING;
        }


        $showStoryModel = [];
            $ct = 0;
            foreach ($userModels as $userModel) {

                $userProp = Model::getUserModelProp($userModel);
//                var_dump($userProp);exit;
                if (empty($userProp)) {
                    continue;
                }

                $matchPlayerStatus = $storyMatchPlayerStatus;
                if (!empty($userProp['hp'])
                    && $userProp['hp'] <= 20
                ) {
                    $matchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_INJURED;
                } else {
                    $matchPlayerStatus = $storyMatchPlayerStatus;

                }
//                var_dump($userProp);exit;

                $storyMatchPlayer = new StoryMatchPlayer();
                $storyMatchPlayer->user_id = $userId;
                $storyMatchPlayer->team_id = 1;
                $storyMatchPlayer->match_id = $matchId;
                $storyMatchPlayer->match_player_status = $matchPlayerStatus;
                $storyMatchPlayer->user_model_id = $userModel->id;
                $storyMatchPlayer->m_story_model_id = !empty($userModel->storyModel->id) ? $userModel->storyModel->id : 0;
                $storyMatchPlayer->m_story_model_detail_id = !empty($userModel->storyModelDetail->id) ? $userModel->storyModelDetail->id : 0;
                $storyMatchPlayer->m_user_model_prop = $userModel->user_model_prop;
                $storyMatchPlayer->save();

//                if (empty($showStoryModel)) {
                    $showStoryModel[] = $userModel->storyModel;
//                }

                if ($matchPlayerStatus == StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE) {
                    $ct++;
                }
            }
            if ($ct == 0) {
//                throw new Exception('您的宠物还在养伤，无法出战！', ErrorCode::STORY_MATCH_NOT_MODEL_READY);
                return $this->renderErr('您的宠物还在养伤，无法出战！');
            }

            if ($storyMatchStatus == StoryMatch::STORY_MATCH_STATUS_END) {
                $storyMatch->story_match_status = $storyMatchStatus;
                $storyMatch->ret = $storyMatchRet;
                $storyMatch->save();

                $msg = '对手宠物都搞挂免战牌，你不战而胜！';
            } else {
                $msg = '您的战斗准备就绪，准备开始吧！';
            }



//            $storyMatch = new StoryMatch();
//            $storyMatch->story_id = $storyId;
//            $storyMatch->user_id = $userId;
//            $storyMatch->session_id = $sessionId;
//            $storyMatch->match_name = $matchName;
//            $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PREPARE;
//            $storyMatch->user_model_id = $userModel->id;
//            $storyMatch->m_story_model_id = $storyModelId;
//            $storyMatch->m_story_model_detail_id = $storyModelDetailId;
//            $storyMatch->save();




        return $this->controller->render('battle_prepare', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'story_match'   => $storyMatch,
            'matchId'      => $matchId,
            'showStoryModel' => $showStoryModel,
            'showRivalStoryModel' => $showRivalStoryModel,
            'answerType'    => 2,
            'msg' => $msg,
            'btnName' => '开始战斗',
        ]);
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
            'answerType' => 1,
        ]);
    }
}