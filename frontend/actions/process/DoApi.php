<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\process;


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
use common\models\UserStory;
use common\models\UserModels;
use frontend\actions\ApiAction;
use frontend\actions\order\Exception;
use Yii;

class DoApi extends ApiAction
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

            $passwordCode = !empty($this->_get['password_code']) ? $this->_get['password_code'] : '';
            $this->_userSessionInfo = Session::find()
                ->where([
                    'user_id' => (int)$this->_userId,
                    'story_id' => (int)$this->_storyId,
                    'session_status' => [
                        Session::SESSION_STATUS_INIT,
                        Session::SESSION_STATUS_READY,
                        Session::SESSION_STATUS_START,
                    ],
                ]);
            if (!empty($passwordCode)) {
                $this->_userSessionInfo = $this->_userSessionInfo->andFilterWhere(['password_code' => $passwordCode]);
            }
            $this->_userSessionInfo = $this->_userSessionInfo->one();

            if (!empty($this->_sessionId)) {
                $this->_sessionInfo = Session::find()
                    ->where(['id' => $this->_sessionId])
                    ->one();
            } else if (!empty($passwordCode)) {
                $this->_sessionInfo = Session::find()
                    ->where(['password_code' => $passwordCode])
                    ->one();
            }

            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'init':
                    $ret = $this->initdata();
                    break;
                case 'join':
                    $ret = $this->join();
                    break;
                case 'get_story':
                    $ret = $this->getStory();
                    break;
                case 'get_story_models':
                    $ret = $this->getStoryModels();
                    break;
                case 'get_session_stages':
                    $ret = $this->getSessionStages();
                    break;
                case 'get_session_models':
                    $ret = $this->getSessionModels();
                    break;
                case 'get_user_model_loc':
                    $ret = $this->getUserModelLoc();
                    break;
                case 'get_session_models_by_stage':
                    $ret = $this->getSessionModelsByStage();
                    break;
                case 'pickup':
                    $ret = $this->pickupModels();
                    break;
                case 'use_model':
                    $ret = $this->useModel();
                    break;
                case 'phone_call':
                    $ret = $this->phoneCall();
                    break;
                case 'get_baggage_models':
                    $ret = $this->getBaggageModels();
                    break;
                case 'get_action_by_user':
                    $ret = $this->getActionByUser();
                    break;
                case 'update_story_model':
                    $ret = $this->updateStoryModel();
                    break;
                case 'finish':
                    $ret = $this->finish();
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
     * 初始化
     * @return array
     * @throws \yii\db\Exception
     */

    public function initdata() {

        $transaction = Yii::$app->db->beginTransaction();

        try {

            if (empty($this->_userSessionInfo)) {
                $sessionObj = new Session();
                $sessionObj->session_name = $this->_userInfo['user_name'] . ' 创建 ' . $this->_storyInfo['title'] . ' ' . ' 场次';
                $sessionObj->user_id = $this->_userId;
                $sessionObj->story_id = $this->_storyId;
                $sessionObj->session_status = Session::SESSION_STATUS_INIT;
                $sessionObj->user_agent = Client::getAgent();
                if (!empty($this->_get['need_code'])) {
                    if (!empty($this->_get['password_code'])) {
                        $sessionObj->password_code = $this->_get['password_code'];
                    } else {
                        $sessionObj->password_code = rand(1000,9999);
                    }
                }
                $ret = $sessionObj->save();

                $sessionId = Yii::$app->db->getLastInsertID();
                $sessionObj['id'] = $sessionId;
                $this->_userSessionInfo = $sessionObj;
            }

            $storyStages = StoryStages::find()
                ->where(['story_id' => (int)$this->_storyId])
                ->orderBy(['sort_by' => SORT_ASC]);
            $storyStages = $storyStages->all();

            foreach ($storyStages as $storyStage) {
                $checkSessionStage = SessionStages::find()
                    ->where([
                        'session_id'    => (int)$this->_userSessionInfo['id'],
                        'story_stage_id'    => (int)$storyStage['id'],
                    ]);
                $checkSessionStage = $checkSessionStage->one();

                if (!empty($checkSessionStage)) {
                    if (empty($firstSessionStageId)) {
                        $firstSessionStageId = $checkSessionStage['id'];
                    }
                    continue;
                }

                $sessionStageObj = new SessionStages();
                $sessionStageObj->story_stage_id = $storyStage['id'];
                $sessionStageObj->session_id = $this->_userSessionInfo['id'];
                $sessionStageObj->story_id = $this->_storyId;
                $sessionStageObj->sort_by = $storyStage['sort_by'];
                $sessionStageObj->snapshot = json_encode($sessionStageObj->toArray(), true);
                $sessionStageObj->save();

                if (empty($firstSessionStageId)) {
                    $firstSessionStageId = Yii::$app->db->getLastInsertID();
                }
            }

            $storyModels = StoryModels::find()
                ->where(['story_id' => (int)$this->_storyId]);
//            if (!empty($this->_buildingId)) {
//                $storyModels->andFilterWhere(['building_id' => (int)$this->_buildingId]);
//            }
            $storyModels = $storyModels->all();
            foreach ($storyModels as $storyModel) {
                $checkSessionModel = SessionModels::find()
                    ->where([
//                        'story_id'  => (int)$this->_storyId,
                        'session_id'    => (int)$this->_userSessionInfo['id'],
                        'story_model_id'    => (int)$storyModel['id'],
                    ]);
//                if (!empty($this->_buildingId)) {
//                    $checkSessionModel->andFilterWhere(['building_id' => (int)$this->_buildingId]);
//                }
                $checkSession = $checkSessionModel->one();

                if (!empty($checkSession)) {
//                    continue;
                    $sessionModel = $checkSession;
                } else {

                    $sessionModel = new SessionModels();
                }
//                foreach ($storyModel as $key => $value) {
//                    if (in_array($key, ['id', 'story_id'])) {
//                        continue;
//                    }
//                    $sessionModel->$key = $value;
//                }

                $sessionModel->story_model_id = $storyModel->id;
                $sessionModel->story_stage_id = $storyModel->story_stage_id;
                $sessionModel->session_id = $this->_userSessionInfo['id'];
                $sessionModel->model_id = $storyModel->model_id;
//                $sessionModel->pre_story_model_id = $storyModel->pre_story_model_id;
                $sessionModel->story_id = $storyModel->story_id;
                $sessionModel->snapshot = json_encode($storyModel->toArray(), true);
//                $sessionModel->is_pickup = 0;
                $sessionModel->save();
            }

            $qaModels = Qa::find()
                ->where([
                    'story_id'  => (int)$this->_storyId,
                ])
                ->all();

            foreach ($qaModels as $qaModel) {
                $checkSessionQa = SessionQa::find()
                    ->where([
                        'story_id'  => (int)$this->_storyId,
                        'session_id'    => (int)$this->_userSessionInfo['id'],
                        'qa_id'    => (int)$qaModel['id'],
                    ])
                    ->one();

                if (!empty($checkSessionQa)) {
                    continue;
                }

                $sessionQa = new SessionQa();
                $sessionQa->qa_id = $qaModel->id;
                $sessionQa->story_id = $qaModel->story_id;
                $sessionQa->qa_type = $qaModel->qa_type;
                $sessionQa->session_id = $this->_userSessionInfo['id'];
                $sessionQa->snapshot = json_encode($qaModel->toArray(), true);
                $sessionQa->save();
            }

//            $knowledge = Knowledge::find()
//                ->where([
//                    'story_id'  => (int)$this->_storyId,
//                    'knowledge_class' => Knowledge::KNOWLEDGE_CLASS_MISSSION
//                ])
//                ->orderBy(['sort_by' => SORT_ASC])
//                ->one();
//
//            $userKnowledge = UserKnowledge::find()
//                ->where([
//                    'session_id'  => (int)$this->_userSessionInfo['id'],
//                    'user_id'   => (int)$this->_userId,
//                    'knowledge_id'  => (int)$knowledge['id'],
//                ])
//                ->one();
//
//            if (!empty($userKnowledge)) {
//                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
//            } else {
//                $userKnowledge = new UserKnowledge();
//                $userKnowledge->knowledge_id = $knowledge['id'];
//                $userKnowledge->session_id = $this->_userSessionInfo['id'];
//                $userKnowledge->user_id = $this->_userId;
//                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
//            }
//            $userKnowledge->save();
//
//            $actions = Yii::$app->act->get($this->_userSessionInfo['id'], $this->_userId);
//            $maxStage = 0;
//            if (!empty($actions)) {
//                foreach ($actions as $act) {
//                    if ($act->session_stage_id > $maxStage) {
//                        $maxStage = $act->session_stage_id;
//                    }
//                }
//            }
//            if ($firstSessionStageId > $maxStage) {
//                Yii::$app->act->add((int)$this->_userSessionInfo['id'], $firstSessionStageId, (int)$this->_storyId, (int)$this->_userId, '开启任务：' . $knowledge['title'], Actions::ACTION_TYPE_MSG);
//            }


            $transaction->commit();
            $ret = true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->_userSessionInfo;
    }

    /**
     * 加入剧本
     */
    public function join() {
        $roleId = !empty($this->_get['role_id']) ? $this->_get['role_id'] : 0;

        $teamId = !empty($this->_get['team_id']) ? $this->_get['team_id'] : 0;

        if (empty($this->_sessionInfo)
            || !in_array($this->_sessionInfo['session_status'], [Session::SESSION_STATUS_INIT, Session::SESSION_STATUS_READY, Session::SESSION_STATUS_START])
        ) {
            return $this->fail('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        if (empty($roleId)) {
            return $this->fail('请您给出角色信息', ErrorCode::ROLE_NOT_FOUND);
        }

        $userStory = UserStory::find()
            ->where([
                'user_id' => (int)$this->_userId,
                'session_id' => (int)$this->_sessionId,
                'story_id'  => (int)$this->_storyId,
//                'building_id' => (int)$this->_buildingId,
//                'role_id' => (int)$roleId,
            ])->one();

        if (!empty($userStory)) {
//            return $this->fail('玩家已经存在', ErrorCode::PLAYER_EXIST);
            $lastStoryStageId = $userStory->last_story_stage_id;
            $lastSessionStageId = $userStory->last_session_stage_id;
            $lastSessionStageUId = $userStory->last_session_stage_u_id;
        } else {

            if (!empty($this->_sessionInfo['password_code'])
                && $this->_sessionInfo['password_code'] != $this->_get['password_code']) {
                return $this->fail('密码错误', ErrorCode::SESSION_PASSWORD_ERROR);
            }

            $userRoleCt = UserStory::find()
                ->where([
//                'user_id' => (int)$this->_userId,
                    'story_id' => (int)$this->_storyId,
                    'session_id' => (int)$this->_sessionId,
                    'role_id' => (int)$roleId,
//                'building_id' => (int)$this->_buildingId,
                ])
                ->count();

            $storyRole = StoryRole::find()
                ->where(['id' => (int)$roleId])
                ->one();

            if ($userRoleCt >= $storyRole['role_max_ct']) {
                return $this->fail('角色已满', ErrorCode::ROLE_FULL);
            }

            $transaction = Yii::$app->db->beginTransaction();
            $userStory = new UserStory();
            $userStory->user_id = $this->_userId;
            $userStory->story_id = $this->_storyId;
            $userStory->session_id = $this->_sessionId;
            $userStory->role_id = $roleId;
//        $userStory->building_id = $this->_buildingId;
            $userStory->team_id = $teamId;
            try {
                $ret = $userStory->save();

                if ($this->_checkSessionRole()) {
                    $this->_sessionInfo->session_status = Session::SESSION_STATUS_START;
                    Yii::$app->act->add($this->_sessionId, 0, $this->_storyId, 0, '游戏开始', Actions::ACTION_TYPE_ACTION);
                } else {
                    $this->_sessionInfo->session_status = Session::SESSION_STATUS_READY;
                    Yii::$app->act->add($this->_sessionId, 0, $this->_storyId, 0, '新玩家加入', Actions::ACTION_TYPE_ACTION);
                }

                $this->_sessionInfo->save();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->fail($e->getMessage(), $e->getCode());
            }
        }

        if (empty($sessionStageUId)) {
            if (empty($lastSessionStageId)
                || empty($lastStoryStageId)
            ) {
                $sessionStage = SessionStages::find()
                    ->where([
                        'story_id' => (int)$this->_storyId,
                        'session_id' => (int)$this->_sessionId,
                    ])
                    ->andFilterWhere(['>', 'sort_by', 0])
                    ->one();

                $lastStoryStageId = $sessionStage->story_stage_id;
                $lastSessionStageId = $sessionStage->id;
            }
            $storyStage = StoryStages::findOne($lastStoryStageId);
            $stageUId = $storyStage['stage_u_id'];
        } else {
            $stageUId = $sessionStageUId;
        }

//        Yii::$app->knowledge->setByItem($lastStoryStageId, ItemKnowledge::ITEM_TYPE_STAGE, (int)$this->_sessionId, $lastSessionStageId, (int)$this->_userId, (int)$this->_storyId);

        $storyStage = StoryStages::findOne($lastStoryStageId);
        $expirationInterval = 60;        // 消息超时时间
        Yii::$app->act->add((int)$this->_sessionId, $lastSessionStageId, (int)$this->_storyId, (int)$this->_userId, $stageUId, Actions::ACTION_TYPE_CHANGE_STAGE, $expirationInterval);

        return $userStory;
    }

    public function finish() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

        $goal = !empty($this->_get['goal']) ? $this->_get['goal'] : '';

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $transaction = Yii::$app->db->beginTransaction();

        $sessionInfo = Session::find()
            ->where([
                'id' => (int)$sessionId,
//                'user_id' => (int)$userId,
            ])
            ->one();

        if (empty($sessionInfo)) {
            return $this->fail('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        try {
            $sessionInfo->session_status = Session::SESSION_STATUS_FINISH;
            $ret = $sessionInfo->save();

            $storyGoals = StoryGoal::find()
                ->where(['story_id' => (int)$this->_storyId])
                ->one();

            $userStory = UserStory::findOne([
                'user_id'       =>  (int)$userId,
                'story_id'      =>  (int)$this->_storyId,
                'session_id'    =>  (int)$sessionId,
            ]);

            if (empty($userStory)) {
                $userStory = new UserStory();
                $userStory->user_id = $userId;
                $userStory->story_id = $this->_storyId;
                $userStory->session_id = $sessionId;
            }
            $userStory->goal = $goal;

            if (!empty($storyGoals)) {
                if ($goal == $storyGoals->goal) {
                    $userStory->goal_correct = '结论正确';
                } else {
                    $userStory->goal_correct = '结论错误，正确结论：' . $storyGoals->goal;
                }
            }
            $ret = $userStory->save();

            Yii::$app->act->add($this->_sessionId, $sessionStageId, $this->_storyId, 0, '游戏结束', Actions::ACTION_TYPE_ACTION);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $userStory;
    }

    public function getBaggageModels(){
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $bagModels = UserModels::find()
            ->with('models', 'sessionModels')
            ->where([
                'session_id' => (int)$sessionId,
                'user_id'   => (int)$userId,
//                'story_id'  => (int)$storyId,
            ]);

//        var_dump($ret->createCommand()->getRawSql());exit;
        $bagModels = $bagModels->all();

        return $bagModels;

    }

    public function getSessionStages() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $isTest = !empty($this->_get['test']) ? $this->_get['test'] : 0;

        if ($storyId == 5 || $isTest == 1) {
            $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
            $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;

//            $setResult = Yii::$app->userModels->setUserModelToLoc($this->_storyId, 0, $userLng, $userLat, StoryModels::STORY_MODEL_CLASS_RIVAL, 1000, 30);
            $setResult = Yii::$app->userModels->setUserModelToLoc($storyId, 0, $userLng, $userLat, 0, 1000, 30);

            $user = User::find()->where(['id' => $userId])->one();
            if (empty($user->home_lng) || empty($user->home_lat)) {
                $user->home_lng = $userLng;
                $user->home_lat = $userLat;
                $user->save();
            }
        }



        $sessionStages = SessionStages::find()
            ->where([
                'session_id' => (int)$sessionId,
//                'user_id'   => (int)$userId,
                'story_id'  => (int)$storyId,
            ])
            ->with('stage')
            ->orderBy([
                'sort_by' => SORT_ASC,
                'id' => SORT_ASC,
            ])
            ->all();

        $ret = [];
        if (!empty($sessionStages)) {
            foreach ($sessionStages as $sessionStage) {
                $hasLoc = 0;
                $sessionStageArray = $sessionStage->toArray();
                $stageArrayTmp = $sessionStage->stage->toArray();

                if (!empty($stageArrayTmp['bgm'])) {
                    $stageArrayTmp['bgm'] = Attachment::completeUrl($stageArrayTmp['bgm'], false);
                }
                if (!empty($setResult)) {

                    foreach ($setResult['result'] as $locId => $userModelLocCollections) {
                        foreach ($userModelLocCollections as $userModelLocRets) {
                            foreach ($userModelLocRets['userModelLoc'] as $userModelLocRet) {

                                if (!empty($userModelLocRets['location'])) {
                                    // 家周围150米之内不出现扩展stage
                                    $dis = \common\helpers\Common::computeDistanceWithLatLng(
                                        $userModelLocRets['location']['lng'], $userModelLocRets['location']['lat'],
                                        $userLng, $userLat, 1, 0);
                                    if ($dis <= 150) {
                                        continue;
                                    }
                                }

                                if ($userModelLocRet->active_class == UserModelLoc::ACTIVE_CLASS_CATCH
                                    || $userModelLocRet->active_class == UserModelLoc::ACTIVE_CLASS_OTHER
                                    || $userModelLocRet->active_class == UserModelLoc::ACTIVE_CLASS_STORY
                                ) {
                                    $stageArray = $stageArrayTmp;
                                    if ($userModelLocRet->story_stage_id == $sessionStage->story_stage_id) {
                                        $hasLoc = 1;
                                        $stageArray['lng'] = $userModelLocRets['location']['lng'];
                                        $stageArray['lat'] = $userModelLocRets['location']['lat'];
                                        $stageArray['scan_type'] = StoryStages::SCAN_TYPE_LATLNG;
                                        if (empty($stageArray['misrange'])) {
                                            $stageArray['misrange'] = 100;
                                        }

                                        $stageArray['stage_u_id'] = str_replace('{$location_id}', $userModelLocRets['location']['id'], $stageArray['stage_u_id']);
                                        $sessionStageArray['stage'] = $stageArray;
                                        $ret[] = $sessionStageArray;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($hasLoc == 0) {

//                var_dump($sessionStage->stage->bgm);exit;
//                if (!empty($sessionStage->stage->bgm)) {
//                    $sessionStage->stage->bgm = Attachment::completeUrl($sessionStage->stage->bgm, false);
//                }

                    $sessionStageArray['stage'] = $stageArrayTmp;
                    $ret[] = $sessionStageArray;
                }
            }
        }

        return $ret;
    }

    public function getSessionModelsByStage() {
        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;
        $stageUId = !empty($this->_get['stage_u_id']) ? $this->_get['stage_u_id'] : '';

        $sessoinStages = SessionStages::find()
            ->where(['id' => $sessionStageId])
            ->with('stage')
            ->all();

        $ret = [];

        $params = [
            'session_id' => $this->_sessionId,
            'user_id' => $this->_userId,
            'story_id' => $this->_storyId,
            'session_stage_id'  => $sessionStageId,
        ];

        // 个性化属性记录在UserModel里，需要取出来进行替换
        $userModels = UserModels::find()
            ->where([
                'session_id' => $this->_sessionId,
                'user_id' => $this->_userId,
            ])
            ->all();
        $userModelProps = [];
        if (!empty($userModels)) {
            foreach ($userModels as $userModel) {
                $userModelProps[$userModel->story_model_id] = $userModel->toArray();
            }
        }

        // Todo: 临时放到这里
        // Todo: 根据位置放置模型（位置从高德取）
        $user = User::find()->where(['id' => $this->_userId])->one();
        if ($this->_storyId == 5) {
            $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
            $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;

            if (empty($user->home_lng) || empty($user->home_lat)) {
                $user->home_lng = $userLng;
                $user->home_lat = $userLat;
                $user->save();
            }

//            $setResult = Yii::$app->userModels->setUserModelToLoc($this->_storyId, 0, $userLng, $userLat, StoryModels::STORY_MODEL_CLASS_RIVAL, 1000, 30);
            $setResult = Yii::$app->userModels->setUserModelToLoc($this->_storyId, 0, $userLng, $userLat, 0, 1000, 30);
        }

        foreach ($sessoinStages as $sessionStage) {
            $stageUIdTemplate = $sessionStage->stage->stage_u_id;
            $sessionModels = $sessionStage->models;
            $models = [];
            if (!empty($sessionModels)) {
//                $models = [];
//                $tmpStoryModelsByStoryModelClass = [];
//                foreach ($sessionModels as $sessionModel) {
//                    $tmpStoryModel = $sessionModel->storymodel;
//                    if (!empty($tmpStoryModel)) {
//                        $tmpStoryModelsByStoryModelClass[$tmpStoryModel->story_model_class][] = $tmpStoryModel;
//                    }
//                }
                foreach ($sessionModels as $sessionModel) {
                    $sessModel = $sessionModel;
//                    $storyModel = json_decode($sessModel['snapshot'], true);
                    $storyModel = $sessionModel->storymodel;

                    if (!empty($storyModel)) {

                        $storyModel = Model::combineStoryModelWithDetail($storyModel);

                        // 判断放置方式，如果当天已经放过，就跳过
                        if (!empty($storyModel->set_type)
                            && $storyModel->set_type == StoryModels::SET_TYPE_ONE_TIME_PER_DAY
                        ) {
                            if (!empty($sessionModel->set_at)
                                && strtotime($sessionModel->set_at) > strtotime(date('Y-m-d 00:00:00'))
                            ) {
                                continue;
                            }
                        }

                        if (!empty($storyModel->set_type)
                            && $storyModel->set_type == StoryModels::SET_TYPE_ONE_TIME
                        ) {
                            if (!empty($sessionModel->set_at)) {
                                continue;
                            }
                        }

//                        $storyModelParams = [];
//                        $params = [];
                        $sModels = [];
                        if ($storyModel->story_model_class == StoryModels::STORY_MODEL_CLASS_STAGE) {

                                if (!empty($setResult)) {
                                    foreach ($setResult['result'] as $locId => $userModelLocs) {
                                        foreach ($userModelLocs as $tmpUserModelLocs) {
                                            foreach ($tmpUserModelLocs['userModelLoc'] as $tmpUserModelLoc) {
                                                if (!empty($tmpUserModelLoc->story_stage_id)) {
                                                    foreach ($sessionStage->stage->nextstage as $nStage) {
                                                        if ($nStage->story_stage_id == $tmpUserModelLoc->story_stage_id) {
                                                            $stageStoryModel = clone $storyModel;
                                                            $stageStoryModel->lng = $tmpUserModelLocs['location']['lng'];
                                                            $stageStoryModel->lat = $tmpUserModelLocs['location']['lat'];
                                                            $stageStoryModel->scan_type = StoryModels::SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG;
                                                            $stageStoryModel->misrange = 50;
                                                            $stageStoryModel->trigger_misrange = 50;
                                                            $stageStoryModel->is_visable = StoryModels::VISIBLE_SHOW;
//                                                            $stageStoryModel->stage_id = $sessionStage->stage->id;

                                                            $params1 = $params;
                                                            $storyModelParams['location_id'] = $tmpUserModelLocs['location']['id'];
                                                            $storyModelParams['stage_id'] = $nStage->story_stage_id;

                                                            $sModels[] = [
                                                                'story_model' => $this->_setStoryModelToStage($stageStoryModel, $storyModelParams, $params1),
                                                                'user_model_loc' => [],
                                                                'location' => $tmpUserModelLocs['location'],
                                                            ];
    //                                                        $models[] = $stageStoryModel;
                                                        }
                                                    }
                                                }
                                            }

                                        }
                                    }
                                } else {
                                    foreach ($sessionStage->stage->nextstage as $nStage) {
                                            $stageStoryModel = clone $storyModel;
                                            $stageStoryModel->lng = $nStage->lng;
                                            $stageStoryModel->lat = $nStage->lat;
                                            $stageStoryModel->scan_type = StoryModels::SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG;
                                            $stageStoryModel->misrange = 50;
                                            $stageStoryModel->trigger_misrange = 50;
                                            $stageStoryModel->is_visable = StoryModels::VISIBLE_SHOW;
                                            $stageStoryModel->stage_id = $sessionStage->stage->id;

                                        $params1 = $params;

                                            $sModels[] = [
                                                'story_model' => $this->_setStoryModelToStage($stageStoryModel, [], $params1),
                                                'user_model_loc' => [],
                                                'location' => [],
                                            ];
//                                                        $models[] = $stageStoryModel;
                                    }
                                }

                        } else {
//                            $tmpSessionStage = $sessionStage->stage->toArray();
                            $tmpSessionStageCloned = clone $sessionStage->stage;
                            $tmpSessionStage = $tmpSessionStageCloned->toArray();
                            $hasLoc = 0;
                            if (!empty($setResult)
                                && !empty($setResult['storyModelsResult'][$storyModel->id])
                            ) {
                                foreach ($setResult['storyModelsResult'][$storyModel->id] as $userModelLocRet) {
//                                foreach ($userModelLocRets as $userModelLocRet) {
                                    $storyModelParams = [];
                                    $params1 = $params;

                                    $storyModel2 = clone $storyModel;
                                    $location = [];
                                    if (!empty($userModelLocRet['location'])
                                        && $hasLoc == 0
                                    ) {
                                        $tmpSessionStageUId = $stageUIdTemplate;
                                        $tmpStageUId = str_replace('{$location_id}', $userModelLocRet['location']['id'], $tmpSessionStageUId);
                                        if ($tmpStageUId != $stageUId) {
                                            continue;
                                        }

                                        $storyModel2->lng = $userModelLocRet['location']['lng'];
                                        $storyModel2->lat = $userModelLocRet['location']['lat'];
                                        $storyModel2->scan_type = StoryModels::SCAN_IMAGE_TYPE_FIX_PLANE_LATLNG;
                                        $storyModel2->misrange = 2;
                                        $storyModel2->trigger_misrange = 10;
//                                    $storyModelParams['lng'] = $userModelLocRet['location']['lng'];
//                                    $storyModelParams['lat'] = $userModelLocRet['location']['lat'];

                                        $storyModelParams['location_id'] = $userModelLocRet['location']['id'];
                                        $params1['location_id'] = $userModelLocRet['location']['id'];

                                        $sessionStage->stage->stage_u_id = $tmpStageUId;
                                        $sessionStage->stage->lat = $userModelLocRet['location']['lat'];
                                        $sessionStage->stage->lng = $userModelLocRet['location']['lng'];
                                        $sessionStage->stage->misrange = 50;
                                        $sessionStage->stage->scan_type = StoryStages::SCAN_TYPE_LATLNG;
                                        $hasLoc = 1;

                                        if ($storyModel2->story_model_class == StoryModels::STORY_MODEL_CLASS_RIVAL) {
                                            $storyModel2->is_visable = StoryModels::VISIBLE_HIDE;
                                        }

                                    }
                                    if (!empty($userModelLocRet['userModelLoc']->active_class)
                                        &&
                                        ($userModelLocRet['userModelLoc']->active_class == UserModelLoc::ACTIVE_CLASS_CATCH
                                            || $userModelLocRet['userModelLoc']->active_class == UserModelLoc::ACTIVE_CLASS_OTHER
                                        )
                                    ) {
                                        $sModels[] = [
                                            'story_model' => $this->_setStoryModelToStage($storyModel2, $storyModelParams, $params1),
                                            'user_model_loc' => $userModelLocRet['userModelLoc'],
                                            'location' => $location,
                                        ];
                                    }
//                                }
                                }
                            } else {
                                $storyModelParams = [];
                                if ($storyModel->story_model_class == StoryModels::STORY_MODEL_CLASS_PET
                                    || $storyModel->story_model_class == StoryModels::STORY_MODEL_CLASS_PET_ITEM
                                ) {
                                    $storyModel->lng = $user->home_lng;
                                    $storyModel->lat = $user->home_lat;
                                    $storyModel->scan_type = StoryModels::SCAN_IMAGE_TYPE_RANDOM_PLANE_LATLNG;
                                    $storyModel->misrange = empty($storyModel->misrange) ? 3 : $storyModel->misrange;
                                    $storyModel->trigger_misrange = empty($storyModel->trigger_misrange) ? 7 : $storyModel->trigger_misrange;
                                }
                                $sModels[] = [
                                    'story_model' => $this->_setStoryModelToStage($storyModel, $storyModelParams, $params),
                                    'user_model_loc' => [],
                                    'location' => [],
                                ];

                            }
                        }

                        if (!empty($sModels)) {
                            foreach ($sModels as $sModelCols) {
                                $sModelItems = $sModelCols['story_model'];
                                $sModelLoc = $sModelCols['user_model_loc'];
                                foreach ($sModelItems as $sModel) {
                                    $models[] = [
                                        'session_model' => $sessionModel,
                                        'story_model' => $sModel,
                                        'user_model_loc' => $sModelLoc,
                                        'model' => $storyModel->model,
                                        'location' => $sModelCols['location'],
                                    ];
                                }
                            }
                        }

//                        $maxCount = 1;
//                        if (!empty($storyModel->story_model_prop)) {
//                            $storyModelProp = json_decode($storyModel->story_model_prop, true);
//                            if (!empty($storyModelProp['repeat'])) {
//                                $maxCount = $storyModelProp['repeat'];
//                            }
//                        }
////                        $preStoryModel = $storyModel;
////                        $oldParams = [
////                            'story_model_name' => (string)$storyModel->story_model_name,
////                            'dialog' => (string)$storyModel->dialog,
////                            'model_inst_u_id' => (string)$storyModel->model_inst_u_id,
////                        ];
//                        for ($i=0; $i<$maxCount; $i++) {
////                            $storyModel = $preStoryModel;
//                            $storyModel1 = clone $storyModel;
//                            // 判断概率
//                            if (!empty($storyModel1->rate)) {
//                                $seed = rand(1, 100);
//                                if ($seed > $storyModel1->rate) {
//                                    continue;
//                                }
//                            }
//
////                            foreach ($oldParams as $col => $val) {
////                                $storyModel->$col = $val;
////                            }
//
//                            $storyModelParams['i'] = $i;
//                            $storyModel1 = Model::formatStoryModel($storyModel1, $storyModelParams);
//
//                            if (!empty($storyModel1->dialog)) {
//                                $params['story_model_id'] = $storyModel1->id;
//                                $params['model_id'] = $storyModel1->model_id;
//                                $params['story_model_detail_id'] = $storyModel1->story_model_detail_id;
//                                $params['model_inst_u_id'] = $storyModel1->model_inst_u_id;
//                                $params['i'] = $i;
//
//                                // 个性化属性替换
//                                if (!empty($userModelProps[$storyModel1->id])) {
//                                    $params['show_name'] = !empty($userModelProps[$storyModel1->id]['user_model_prop']['show_name'])
//                                        ? $userModelProps[$storyModel1->id]['user_model_prop']['show_name'] : $storyModel1->story_model_name;
//                                }
//
//                                $storyModel1->dialog = Model::formatDialog($storyModel1, $params);
//                            }
//
//
//                            $models[] = [
//                                'session_model' => $sessionModel,
//                                'story_model' => $storyModel1,
//                                'model' => $storyModel->model,
//                            ];
//                        }
                    }
                }
            }
            $stage = $sessionStage->stage;
            if (!empty($stage->bgm)) {
                $stage->bgm = Attachment::completeUrl($stage->bgm, false);
            }

            $nextStage = $sessionStage->stage->nextstage;
            $nStage = [];
            if (!empty($nextStage)) {
                foreach ($nextStage as $ns) {
                    if (empty($ns->storystage)) {
                        continue;
                    }
                    $nStage[] = $ns->storystage;
                }
            }
            $ret[] = [
                'session_stage' => $sessionStage,
                'stage' => $stage,
                'next_stage' => $nStage,
                'session_models' => $models,
            ];
        }

        return $ret;
    }

    public function _setStoryModelToStage($storyModel, $storyModelParams = [], $params = []) {

        $storyModels = [];

        $maxCount = 1;
        if (!empty($storyModel->story_model_prop)) {
            $storyModelProp = json_decode($storyModel->story_model_prop, true);
            if (!empty($storyModelProp['repeat'])) {
                $maxCount = $storyModelProp['repeat'];
            }
            if (!empty($storyModels['random_repeat'])) {
                $maxCount = rand($storyModels['random_repeat']['min'], $storyModels['random_repeat']['max']);
            }
        }
//                        $preStoryModel = $storyModel;
//                        $oldParams = [
//                            'story_model_name' => (string)$storyModel->story_model_name,
//                            'dialog' => (string)$storyModel->dialog,
//                            'model_inst_u_id' => (string)$storyModel->model_inst_u_id,
//                        ];
        for ($i=0; $i<$maxCount; $i++) {
//                            $storyModel = $preStoryModel;
            $storyModel1 = clone $storyModel;
            // 判断概率
            if (!empty($storyModel1->rate)) {
                $seed = rand(1, 100);
                if ($seed > $storyModel1->rate) {
                    continue;
                }
            }

//                            foreach ($oldParams as $col => $val) {
//                                $storyModel->$col = $val;
//                            }

            $storyModelParams['i'] = $i;
            $storyModel1 = Model::formatStoryModel($storyModel1, $storyModelParams);

            $storyModel1->icon = Attachment::completeUrl($storyModel1->icon, true);

            if (!empty($storyModel1->dialog)) {
                if (!empty($storyModelProp['qa_random'])) {
                    $qaRandom = $storyModelProp['qa_random'];

                    $qaOne = Qa::find()->where([
                        'qa_mode' => Qa::QA_MODE_RANDOM,
                    ]);
                    if (is_array($qaRandom) || is_int($qaRandom)) {
                        $qaOne = $qaOne->andFilterWhere([
                            'id' => $qaRandom,
                        ]);
                    }
                    $qaOne = $qaOne->orderBy('rand()')->one();

                    $params['qa_id'] = $qaOne->id;
                }

                $params['story_model_id'] = $storyModel1->id;
                $params['model_id'] = $storyModel1->model_id;
                $params['story_model_detail_id'] = $storyModel1->story_model_detail_id;
                $params['model_inst_u_id'] = $storyModel1->model_inst_u_id;
                $params['i'] = $i;
                $params['ts'] = time() . rand(1000,9999);

                // 个性化属性替换
                if (!empty($userModelProps[$storyModel1->id])) {
                    $params['show_name'] = !empty($userModelProps[$storyModel1->id]['user_model_prop']['show_name'])
                        ? $userModelProps[$storyModel1->id]['user_model_prop']['show_name'] : $storyModel1->story_model_name;
                }

                $storyModel1->dialog = Model::formatDialog($storyModel1, $params);
            }

            $storyModels[] = $storyModel1;
//            $models[] = [
//                'session_model' => $sessionModel,
//                'story_model' => $storyModel1,
//                'model' => $storyModel->model,
//            ];
        }

        return $storyModels;
    }

    public function getUserModelLoc() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;

        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;

        $userModelLoc = Yii::$app->userModels->getUserModelLocByUserId($userId, UserModelLoc::USER_MODEL_LOC_STATUS_LIVE);

        $ret = [];

        if (!empty($userModelLoc)) {
            foreach ($userModelLoc as $userModelLocItem) {
                $temp = Yii::$app->userModels->setUserModelToLoc($storyId, 0,
                    $userModelLocItem->lng, $userModelLocItem->lat);
                $ret += $temp['result'];
            }
        } else {
            $temp = Yii::$app->userModels->setUserModelToLoc($storyId, 0, $userLng, $userLat);
            $ret = $temp['result'];
        }

        if (!empty($ret)) {
            foreach ($ret as &$row) {
                foreach ($row as &$row1) {
                    foreach ($row1['userModelLoc'] as &$row2) {
                        $tmp = $row2->toArray();
                        $tmp['storyModel']  = $row2->storyModel->toArray();
                        $tmp['storyModel']['icon'] = Attachment::completeUrl($tmp['storyModel']['icon'], true);

                        if ($row2->active_class == UserModelLoc::ACTIVE_CLASS_BATTLE) {
                            if ($row2->user_id != $userId) {
                                $tmp['link_url'] = 'https://h5.zspiritx.com.cn/baggageh5/all?user_id=' . $userId . '&session_id=' . $sessionId . '&story_id=' . $storyId . '&bag_version=2&story_model_class=3&target_story_model_id=' . $tmp['storyModel']['id'] . '&target_user_model_loc_id=' . $row2['id'];
                                $tmp['link_text'] = '战斗';
                            }
                        } elseif ($row2->active_class == UserModelLoc::ACTIVE_CLASS_CATCH) {
                            $tmp['link_url'] = '';
                            $tmp['link_text'] = '捕捉';
                        } elseif ($row2->active_class == UserModelLoc::ACTIVE_CLASS_OTHER) {
                            $tmp['link_url'] = '';
                            $tmp['link_text'] = '探寻';
                        }

                        $tmp['loc_color'] = '';
                        if (!empty($row2->user_id)) {
                            if ($row2->user_id == $userId) {
                                $tmp['loc_color'] = Attachment::completeUrl('img/map/loc_color_red.png', true);
                            } else {
                                $tmp['loc_color'] = Attachment::completeUrl('img/map/loc_color_blue.png', true);
                            }
                        } else {
                            $tmp['loc_color'] = Attachment::completeUrl('img/map/loc_color_blue.png', true);
                        }

                        if (!empty($row1['location'])) {
                            $location = $row1['location'];
                            $locationProp = !empty($location['amap_prop']) ? json_decode($location['amap_prop'], true) : [];
                            if (!empty($locationProp['geofence'])) {
                                $propTmp = [];
                                if ($row2->user_id == $this->_userId) {
                                    $propTmp = [
                                        'fillColor' => '#0b3452',
                                        'fillOpacity' => 0.4,
                                        'borderWeight' => 1,
                                        'strokeColor' => '#0c84ff',
                                        'strokeStyle' => 'solid',
                                        'strokeOpacity' => 0.8,
                                        'strokeWeight' => 2,
                                    ];
                                } elseif ($row2->user_id != 0) {
                                    $propTmp = [
                                        'fillColor' => '#a83800',
                                        'fillOpacity' => 0.4,
                                        'borderWeight' => 1,
                                        'strokeColor' => '#a80057',
                                        'strokeStyle' => 'solid',
                                        'strokeOpacity' => 0.8,
                                        'strokeWeight' => 2,
                                    ];
                                } else {
                                    $propTmp = [
                                        'fillColor' => '#DAFFCE',
                                        'fillOpacity' => 0.4,
                                        'borderWeight' => 1,
                                        'strokeColor' => '#DAFC70',
                                        'strokeStyle' => 'solid',
                                        'strokeOpacity' => 0.8,
                                        'strokeWeight' => 2,
                                    ];
                                }

                                if (!empty($locationProp['geofence']['circle'])) {
                                    $locationProp['geofence']['circle'] = array_merge($propTmp, $locationProp['geofence']['circle']);
                                } elseif (!empty($locationProp['geofence']['polygon'])) {
                                    $locationProp['geofence']['polygon'] = array_merge($propTmp, $locationProp['geofence']['polygon']);
                                } elseif (!empty($locationProp['geofence']['rectangle'])) {
                                    $locationProp['geofence']['rectangle'] = array_merge($propTmp, $locationProp['geofence']['rectangle']);
                                }
                            }
                                $location['amap_prop'] = json_encode($locationProp);
                                $row1['location'] = $location;

                        }

                        $row2 = $tmp;
                    }

                }
            }
        }


        return $ret;
    }

    public function getStoryModels(){
        $preStoryModelId = !empty($this->_get['pre_story_model_id']) ? $this->_get['pre_story_model_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $storyStageId = !empty($this->_get['story_stage_id']) ? $this->_get['story_stage_id'] : 0;

        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
        $disRange = !empty($this->_get['dis_range']) ? $this->_get['dis_range'] : 0;

        if (!empty($preStoryModelId)) {
            $preModel = SessionModels::find()
                ->where([
                    'story_model_id' => (int)$preStoryModelId,
                    'session_id'    => (int)$sessionId,
//                    'story_id'      => (int)$storyId,
//                    'is_pickup'     => SessionModels::IS_PICKUP_YES,
                ])
                ->one();
            if (empty($preModel)) {
//                return $this->fail('物品不存在', ErrorCode::DO_PRE_MODELS_NOT_FOUND);
                return [];
            }

        }

        if ($disRange > 0) {
            $sql = 'SELECT *, st_distance(point(o_story_model.lng, o_story_model.lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 as dist FROM o_session_model left join o_story_model on o_session_model.story_model_id = o_story_model.id WHERE o_session_model.session_id = ' . $sessionId;
            $sql .= ' AND st_distance(point(o_story_model.lng, o_story_model.lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 < ' . $disRange;
            $sql .= ' AND (o_session_model.is_unique = ' . SessionModels::IS_UNIQUE_NO . ' OR o_session_model.session_model_status = ' . SessionModels::SESSION_MODEL_STATUS_READY . ' OR o_session_model.session_model_status = ' . SessionModels::SESSION_MODEL_STATUS_SET . ')';
            if (!empty($storyStageId)) {
                $sql .= ' AND o_session_model.story_stage_id = ' . $storyStageId;
            }
            $sql .= ' ORDER BY dist ASC;';
//            var_dump($sql);
            $storyModels = Yii::$app->db->createCommand($sql)->queryAll();
        } else {


            $storyModels = StoryModels::find()
                ->with('sessionModel, model')
                ->where([
                    'story_id'  => (int)$storyId,
                ]);
            if (!empty($storyStageId)) {
                $storyModels = $storyModels->andFilterWhere([
                    'story_stage_id'  => $storyStageId,
                ]);
            }


            $storyModels = $storyModels->all();
        }


        return $storyModels;

    }

    public function getSessionModels(){
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $storyStageId = !empty($this->_get['story_stage_id']) ? $this->_get['story_stage_id'] : 0;

        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
        $disRange = !empty($this->_get['dis_range']) ? $this->_get['dis_range'] : 0;

        if ($disRange > 0) {
            $sql = 'SELECT *, st_distance(point(o_story_model.lng, o_story_model.lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 as dist FROM o_story_model WHERE story_id = ' . $storyId;
            $sql .= ' AND st_distance(point(o_story_model.lng, o_story_model.lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 < ' . $disRange;
            if (!empty($storyStageId)) {
                $sql .= ' AND story_stage_id = ' . $storyStageId;
            }
            $sql .= ' ORDER BY dist ASC;';
//            var_dump($sql);
            $sessModels = Yii::$app->db->createCommand($sql)->queryAll();
        } else {


            $sessModels = SessionModels::find()
                ->with('model')
                ->where([
                    'session_id' => (int)$sessionId,
//                'story_id'  => (int)$storyId,
                ]);
            if (!empty($storyStageId)) {
                $sessModels = $sessModels->andFilterWhere([
                    'story_stage_id'  => $storyStageId,
                ]);
            }
//            if (!empty($preStoryModelId)) {
//                $sessModels = $sessModels->andFilterWhere([
//                    'pre_story_model_id' => (int)$preStoryModelId,
//                    'session_model_status' => [
//                        SessionModels::SESSION_MODEL_STATUS_READY,
//                        SessionModels::SESSION_MODEL_STATUS_SET,
//                        SessionModels::SESSION_MODEL_STATUS_OPERATING
//                    ]
//                ]);
//            } else {
//                $sessModels = $sessModels->andFilterWhere([
//                    'or',
//                    ['is_unique' => SessionModels::IS_UNIQUE_NO,],
//                    ['session_model_status' => [
//                            SessionModels::SESSION_MODEL_STATUS_SET,
//                            SessionModels::SESSION_MODEL_STATUS_READY,
//                            SessionModels::SESSION_MODEL_STATUS_OPERATING
//                        ]
//                    ]
//                ]);
//            }
//            $sessModels = $sessModels->andFilterWhere([
//                'or',
//                ['is_unique' => SessionModels::IS_UNIQUE_NO,],
//                ['is_pickup' => SessionModels::IS_PICKUP_NO,]
//            ]);
//        }

//        var_dump($ret->createCommand()->getRawSql());exit;

            $sessModels = $sessModels->all();
        }

        // Todo: 暂时不做记录，性能考虑
//        try {
//            $transaction = Yii::$app->db->beginTransaction();
//            foreach ($sessModels as $sModel) {
//                $sModel->is_set = SessionModels::IS_SET_YES;
//                $sModel->session_model_status = SessionModels::SESSION_MODEL_STATUS_SET;
//                $sModel->save();
//            }
//            $transaction->commit();
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            return $this->fail($e->getMessage(), $e->getCode());
//        }

        return $sessModels;

    }

    public function updateStoryModel() {
        $storyModelDetailId = !empty($this->_get['story_model_detail_id']) ? $this->_get['story_model_detail_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;

        // 清理兜底中模型
        if (!empty($storyModelDetailId)
        || !empty($storyModelId)
        ) {
            Yii::$app->models->removeUndertakeModelFromCookie($storyModelDetailId, $storyModelId);
        }
        return true;
    }

    public function getActionByUser() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;

        // 保活
//        $userKeepAlive = Cookie::getCookie(Cookies::USER_KEEP_ALIVE);
//        if (!empty($userKeepAlive)) {
//            Cookie::setCookie(Cookies::USER_KEEP_ALIVE, $userKeepAlive + 1, 10);
//        } else {
//            Cookie::setCookie(Cookies::USER_KEEP_ALIVE, 1, 10);
//        }
        Yii::$app->models->setKeepAlive();

        $underTakeIds = Yii::$app->models->setUndertakeAction($sessionId, $userId);

//
//
//        // 兜底策略
//        $stageCookieJson = Cookie::getCookie(Cookies::UPDATE_STAGE_TIME);
//        //Todo 这个需要放到配置中
//        $execTime = 5 * 60; // 执行兜底，5分钟
//        $keepAlive = 60; // 保活时间，1分钟
//        $isUndertake = false;
//        Yii::info('Undertake stageCookie: ' . $stageCookieJson);
//        if (!empty($stageCookieJson)) {
//            $stageCookie = json_decode($stageCookieJson, true);
//            $ts = $stageCookie['ts'];
//            $cookieStoryStageId = $stageCookie['story_stage_id'];
//            $cookieSessionStageId = $stageCookie['session_stage_id'];
//            $cookieStoryId = $stageCookie['story_id'];
//            Yii::info('Undertake userKeepAlive ii: ' . (time() - $ts));
//            if ((time() - $ts) > $execTime) {
//                $userKeepAlive = Cookie::getCookie(Cookies::USER_KEEP_ALIVE);
//                Yii::info('Undertake userKeepAlive: ' . $userKeepAlive);
//                if ($userKeepAlive > $keepAlive) {
////                    $sessModels = SessionModels::find()
////                        ->joinWith('storymodel')
////                        ->where([
////                            'session_id' => (int)$sessionId,
////                            'story_stage_id'  => $stageCookie['story_stage_id'],
////                        ])
////                        ->andFilterWhere([
////                            'o_story_model.is_undertake' => StoryModels::IS_UNDERTAKE_YES
////                        ])
////                        ->all();
//                    $sessModels = Cookie::getCookie(Cookies::UNDERTAKE_MODEL);
//
//                    if (!empty($sessModels)) {
////                        $sessModels = json_decode($sessModelJson, true);
//                        $underTakeIds = [];
//                        foreach ($sessModels as $sessModel) {
//                            if (!empty($sessModel['is_ready']) && $sessModel['is_ready'] == true) {
//                                $ret = Yii::$app->act->add($sessionId, $cookieSessionStageId, $cookieStoryId, $userId, $sessModel['model_inst_u_id'], Actions::ACTION_TYPE_MODEL_DISPLAY);
//                                $underTakeIds[] = $ret->id;
//                                $isUndertake = true;
//                            }
//                        }
//                    }
//                }
//            }
//        }

        $actions = Yii::$app->act->get($sessionId, $userId, $actionStatus = \common\models\Actions::ACTION_STATUS_NORMAL, 1);

        Yii::$app->models->readUndertakeActionAndUnsetCookie($underTakeIds);

//        if (!empty($underTakeIds)) {
//            foreach ($underTakeIds as $actId) {
//                Yii::$app->act->readOne($actId);
//            }
//            Cookie::unsetCookie(Cookies::UPDATE_STAGE_TIME);
//            Cookie::unsetCookie(Cookies::UNDERTAKE_MODEL);
//        }

//        $actions = Actions::find()
//            ->where([
//                'session_id' => (int)$sessionId,
//            ])
//            ->andFilterWhere([
//                'or',
//                ['to_user' => (int)$userId],
//                ['to_user' => 0],
//            ])
//            ->andFilterWhere([
//                'or',
//                ['expire_time' => (int)0],
//                ['>=', 'expire_time', time()],
//            ])
//            ->andFilterWhere([
//                'action_status' => Actions::ACTION_STATUS_NORMAL
//            ])
////            ->createCommand()->getRawSql();
////        var_dump($actions);exit;
//            ->all();

//        try {
//            foreach ($actions as $tempAct) {
//                if (in_array($tempAct->action_type, [
//                    Actions::ACTION_TYPE_MSG,
//                    Actions::ACTION_TYPE_ACTION,
//                ])) {
//                    $tempAct->action_status = Actions::ACTION_STATUS_READ;
//                    $ret = $tempAct->save();
//                }
//            }
//        } catch (\Exception $e) {
////            return $this->fail($e->getMessage(), $e->getCode());
//        }

        return $actions;
    }

    public function phoneCall() {

        $qaId = !empty($this->_get['qa_id']) ? $this->_get['qa_id'] : 0;

        $qa = Qa::find()
            ->where([
                'id' => (int)$qaId,
                'qa_type' => Qa::QA_TYPE_PHONE,
            ])
            ->one();

        if (!empty($qa)) {
            $selected = $qa->selected;
            $selected = json_decode($selected, true);
            $whiteList = $selected;
        } else {
            $whiteList = [
                '65104101' => [
                    'voice' => '/voice/phone/wait_call.mp3',
                    'value' => 2,
                ],
            ];
        }
        
        $phone = !empty($this->_get['phone']) ? $this->_get['phone'] : '';

        if (!isset($whiteList[$phone])) {
            if (!empty($whiteList['wrong'])) {
                $ret = $whiteList['wrong'];
            }
            if (empty($ret['voice'])) {
                $ret['voice'] = '/voice/phone/no_phone_number.mp3';
            }
            if (empty($ret['value'])) {
                $ret['value'] = isset($whiteList['wrong']) ? sizeof($whiteList) : sizeof($whiteList) + 1;
            }
        } else {
            $ret = $whiteList[$phone];
        }

        if (!is_array($ret['voice'])) {
            $returnVoice = Attachment::completeUrl($ret['voice'], false);
            $ret['voices'] = [$returnVoice];
        } else {
            $returnVoice = [];
            foreach ($ret['voice'] as $voice) {
                $returnVoice[] = Attachment::completeUrl($voice, false);
            }
            $ret['voices'] = $returnVoice;
        }
        unset($ret['voice']);


        return $ret;
    }

    public function useModel() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $act = !empty($this->_get['act']) ? $this->_get['act'] : 1;     // 1 - 使用；2 - 组合；3 - 丢弃

        $userModelId = !empty($this->_get['user_model_id']) ? $this->_get['user_model_id'] : 0;

        if ($act == 2) {
            $umIds = explode(',', $userModelId);
            if (sizeof($umIds) < 2) {
                throw new \yii\db\Exception('您需要至少选择两个物品', ErrorCode::USER_MODEL_NOT_FOUND);
            }
//            ksort($umIds);
            $userModelId = $umIds[0];

            $combineGroup = !empty($this->_get['combine_group']) ? $this->_get['combine_group'] : '';

//            $userModelId = $umIds[0];
//            $userModelId2 = $umIds[1];
        } else {

            $targetStoryModelId = !empty($this->_get['target_story_model_id']) ? $this->_get['target_story_model_id'] : 0;
            $targetStoryModelDetailId = !empty($this->_get['target_story_model_detail_id']) ? $this->_get['target_story_model_detail_id'] : 0;
        }


        if (empty($userModelId)) {
            throw new \yii\db\Exception('您没有选择任何物品', ErrorCode::USER_MODEL_NOT_FOUND);
        }

        $bagageModel = UserModels::find()
            ->where([
                'id' => (int)$userModelId,
                'is_delete' => Common::STATUS_NORMAL,
//                'user_id' => (int)$userId,
//                'session_id' => (int)$sessionId,
            ]);

        $bagageModel = $bagageModel->one();

        if (empty($bagageModel)) {
            throw new \yii\db\Exception('背包中没有该道具', ErrorCode::USER_MODEL_NOT_FOUND);
        }

        if ($bagageModel->use_ct <= 0) {
            throw new \yii\db\Exception('该道具使用次数已用完', ErrorCode::USER_MODEL_NOT_ENOUGH);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storyModel = $bagageModel->storyModel;
            if (empty($storyModel)) {
                throw new \yii\db\Exception('没有找到该道具', ErrorCode::USER_MODEL_NOT_FOUND);
            }

            if ($storyModel->use_allow == StoryModels::USE_ALLOW_NOT) {
                throw new \yii\db\Exception('该道具不允许使用', ErrorCode::USER_MODEL_NOT_ALLOW);
            }

            if ($act == 2 && $storyModel->active_type != StoryModels::ACTIVE_TYPE_COMBINE) {
                throw new \yii\base\Exception('您的使用没有任何效果', ErrorCode::USER_MODEL_NO_EFFECT);
            }

//            $activeArray = \common\helpers\Model::decodeActive($storyModel->activeNext);

            // Todo: 临时处理物品使用以后减少数量逻辑
            // 理论上应该放在StoryDetailModel中做一个配置
            // 然后其他模块诸如StoryModelLink作为是否发生的开关
            // 然后再进行效果验证
            // 因为开发时间太少，先写死
            $minCt = 1;      // 允许使用以后被减少的个数
            switch ($storyModel->active_type)
            {
                case StoryModels::ACTIVE_TYPE_BUFF:
                    $userStory = UserStory::findOne([
                        'user_id' => (int)$userId,
                        'session_id' => (int)$sessionId,
                        'story_id' => (int)$storyId,
                    ]);

                    if (!empty($userStory)
                        && !empty($storyModel->buff)
                    ) {
                        $userStory->buff = $storyModel->buff->id;
                        $userStory->buff_expiretime = time() + $storyModel->buff->expire_time;
                        $userStory->save();
                    } else {
                        throw new \yii\base\Exception('执行出现问题', ErrorCode::USER_MODEL_BUFF_NOT_FOUND);
                    }
                    $res = '';
                    break;
                case StoryModels::ACTIVE_TYPE_COMBINE:

                    $userModels = UserModels::find()
                        ->where([
                            'id' => $umIds,
                        ])
                        ->andFilterWhere(['>', 'use_ct', 0])
                        ->all();

                    if (empty($userModels)) {
                        throw new \yii\db\Exception('您没有选择任何物品', ErrorCode::USER_MODEL_NOT_FOUND);
                    }

                    $storyModelIds = [];
                    $storyModelDetailIds = [];
                    $storyModels = [];
                    $userModelIds = [];
                    foreach ($userModels as $userModel2) {
                        $storyModel2 = $userModel2->storyModel;

                        if (empty($storyModel2)) {
                            throw new \yii\base\Exception('没有找到该道具', ErrorCode::USER_MODEL_NOT_FOUND);
                        }

                        if ($storyModel2->use_allow == StoryModels::USE_ALLOW_NOT) {
                            throw new \yii\base\Exception('该道具不允许使用', ErrorCode::USER_MODEL_NOT_ALLOW);
                        }

                        if (!empty($storyModel2->story_model_detail_id)) {
                            $storyModelDetailIds[] = $storyModel2->story_model_detail_id;
                        }
                        $storyModelIds[] = $storyModel2->id;

                        $storyModels[] = $storyModel2;
                        $userModelIds[] = $userModel2->id;
                    }

                    if (empty($combineGroup)) {
                        $groupStoryModel = StoryModelsLink::find()
                            ->where([
                                'story_id' => $storyId,
                                'eff_type' => [
                                    StoryModelsLink::EFF_TYPE_MODEL,
                                    StoryModelsLink::EFF_TYPE_MODEL_AND_DISPLAY,
                                    StoryModelsLink::EFF_TYPE_INCLUDE_MODEL_AND_DISPLAY,
                                    ],
                            ])
                            ->andFilterWhere([
                                'or', ['story_model_id' => $storyModelIds],
                                ['story_model_detail_id' => $storyModelDetailIds],
                            ])
                            ->andFilterWhere([
                                'is_tag' => StoryModelsLink::IS_TAG_YES
                            ]);

//                        if (!empty($storyModel->story_model_detail_id)) {
//                            $groupStoryModel = $groupStoryModel->andFilterWhere([
//                                'story_model_detail_id' => $storyModel->story_model_detail_id,
//                            ]);
//                        } else {
//                            $groupStoryModel = $groupStoryModel->andFilterWhere([
//                                'story_model_id' => $storyModel->id,
//                            ]);
//                        }
                        $groupStoryModel = $groupStoryModel->one();
                        if (!empty($groupStoryModel)) {
                            $combineGroup = $groupStoryModel->group_name;
                        } else {
                            throw new \yii\base\Exception('没有找到必须的合成物品', ErrorCode::USER_MODEL_NO_EFFECT);
                        }
                    }

                    $storyModelLinks = StoryModelsLink::find()
                        ->where([
                            'group_name'        => $combineGroup,
                            'story_id'          => $storyId,
                            'eff_type'          => [
                                StoryModelsLink::EFF_TYPE_MODEL,
                                StoryModelsLink::EFF_TYPE_MODEL_AND_DISPLAY,
                                StoryModelsLink::EFF_TYPE_INCLUDE_MODEL_AND_DISPLAY,
                            ],
//                            'story_model_id'    => $storyModel->id,
//                            'story_model_id2'   => $storyModel1->id,
                        ])
                        ->orderBy(['story_model_id' => SORT_ASC])
                        ->all();

                    $linkStoryModelIds = [];
                    $linkStoryModelDetailIds = [];
                    $linkStoryModelTagIds = [];
                    $linkStoryModelTagDetailIds = [];
                    $linkExecArr = [];
                    $linkStoryModels = [];
                    if (!empty($storyModelLinks)) {

                        foreach ($storyModelLinks as $storyModelLink) {
                            if (!empty($storyModelLink->story_model_detail_id)) {
                                $linkStoryModelDetailIds[] = $storyModelLink->story_model_detail_id;
                            }
                            $linkStoryModelIds[] = $storyModelLink->story_model_id;
                            $linkStoryModels[$storyModelLink->story_model_id] = $storyModelLink;

                            if ($storyModelLink->is_tag == StoryModelsLink::IS_TAG_YES) {
                                if (!empty($storyModelLink->story_model_detail_id)) {
                                    $linkStoryModelTagDetailIds[] = $storyModelLink->story_model_detail_id;
                                }
                                $linkStoryModelTagIds[] = $storyModelLink->story_model_id;
                            }

                            $tmpExec = $storyModelLink->eff_exec;
                            if (\common\helpers\Common::isJson($tmpExec)) {
                                $tmpExecArr = json_decode($tmpExec, true);
                                if (is_array($tmpExecArr)) {
                                    $newStoryModelId = !empty($tmpExecArr['target_story_model_id']) ? $tmpExecArr['target_story_model_id'] : 0;
                                } else {
                                    $newStoryModelId = $tmpExecArr;
                                }
                                $linkExecArr[$storyModelLink->story_model_id] = $tmpExecArr;
                            } else {
                                $newStoryModelId = $tmpExec;
                            }
                            $type = $storyModelLink->eff_type;
                        }
                    } else {
                        throw new \yii\base\Exception('您的使用没有关联效果', ErrorCode::USER_MODEL_NO_EFFECT);
                    }

                    if ($type == StoryModelsLink::EFF_TYPE_INCLUDE_MODEL_AND_DISPLAY) {
//                        if (\common\helpers\Common::arrayContains($storyModelIds, $linkStoryModelIds)) {
//                    else {
//                            throw new \yii\base\Exception('您的组合没有任何效果', ErrorCode::USER_MODEL_NO_EFFECT);
//                        }
                        $checkContains = Yii::$app->models->checkStoryModelWithLinkStoryModel($storyModelIds, $linkStoryModelIds, $linkStoryModelTagIds);
                        if ( $checkContains !== true ) {
                            if ($checkContains == false) {
                                throw new \yii\base\Exception('您的组合选择没有效果', ErrorCode::USER_MODEL_NO_EFFECT);
                            } else {
                                $storyModelStrArray = [];
                                foreach ($checkContains as $diffStoryModelId) {
                                    $storyModelStrArray[] = !empty($linkStoryModels[$diffStoryModelId]) ? $linkStoryModels[$diffStoryModelId]->storyModel->story_model_name : '';
                                }
                                $storyModelStr = implode(',', $storyModelStrArray);
                                throw new \yii\base\Exception('您欠缺必要物品：' . $storyModelStr, ErrorCode::USER_MODEL_NO_EFFECT);
                            }
                        } else {
                            sort($storyModelIds);
                            $userModelStr = implode(',', $storyModelIds);
                            $linkStoryModelStr = implode(',', $storyModelIds);
                        }
//
                    } else {
                        sort($storyModelIds);
                        $userModelStr = implode(',', $storyModelIds);
                        sort($linkStoryModelIds);
                        $linkStoryModelStr = implode(',', $linkStoryModelIds);
                    }

                    if ($userModelStr == $linkStoryModelStr) {

                        $newStoryModel = StoryModels::find()->where(['id' => $newStoryModelId])->one();
                        // Todo:处理需要修改，变成取出之前组合的所有物品，然后计算新的物品，如果没有组合物品，用新的物品
                        $newUserModelForCombine = [];
                        if (in_array($newStoryModelId, $storyModelIds)) {
                            $newUserModelForCombine = UserModels::find()
                                ->where([
                                    'story_model_id' => $newStoryModelId,
                                    'story_id' => $storyId,
                                    'session_id' => $sessionId,
                                    'user_id' => $userId,
                                ])
                                ->one();
                        }

//                        $newModelProp = Yii::$app->models->computeStoryModelPropWithFormula($storyModels, $newStoryModel, $newUserModelForCombine, $storyModelIds);
                        $newModelProp = Yii::$app->models->computeStoryModelLinkPropWithFormula($linkExecArr, $newStoryModel, $newUserModelForCombine, $storyModelIds);

                        if ($type == StoryModelsLink::EFF_TYPE_MODEL) {
                            $newUserModel = Yii::$app->baggage->pickup($storyId, $sessionId, $newStoryModelId, $userId, 0, $newModelProp);
                        }
                        if (
                            $type == StoryModelsLink::EFF_TYPE_MODEL_AND_DISPLAY
                        || $type == StoryModelsLink::EFF_TYPE_INCLUDE_MODEL_AND_DISPLAY
                        ) {
                            // 组合模型并显示出来
                            $newUserModel = Yii::$app->baggage->pickup($storyId, $sessionId, $newStoryModelId, $userId, 0, $newModelProp);
                            $newStoryUID = !empty($newUserModel->storyModel->model_inst_u_id) ? $newUserModel->storyModel->model_inst_u_id : 0;
                            if (!empty($newStoryUID) ) {
                                $expirationInterval = 3600;
                                Yii::$app->act->add((int)$this->_sessionId, 0, (int)$this->_storyId, (int)$this->_userId, $newStoryUID, Actions::ACTION_TYPE_MODEL_DISPLAY, $expirationInterval);
                            }
                        }

                        $minCt = $storyModelLink->min_ct;
                        if ($minCt > 0) {
                            foreach ($userModels as $userModel2) {
                                $userModel2->use_ct -= $minCt;

                                if ($userModel2->use_ct == 0) {
                                    $userModel2->is_delete = Common::STATUS_DELETED;
                                }
                                $userModel2->save();

                            }
                        }
                        $minCt = 0;

                        $retUserModel = $newUserModel;
                        if (!empty($newUserModel)) {
                            $retUserModel = $newUserModel->toArray();
                            if (!empty($newUserModel->storyModel)) {
                                $retUserModel['story_model'] = $newUserModel->storyModel->toArray();
                                $retUserModel['story_model']['icon'] = Attachment::completeUrl($retUserModel['story_model']['icon'], true);
                                $showRet['icon'] = $retUserModel['story_model']['icon'];
                                $showRet['story_model_name'] = $newUserModel->storyModel->story_model_name;
                            }
                        }

                        $res = [
                            'user_model' => $bagageModel,
                            'user_model2' => $userModel2,
                            'new_user_model' => $retUserModel,
                            'show' => $showRet,
                        ];
                    } else {
                        throw new \yii\base\Exception('您的使用打开方式不对！', ErrorCode::USER_MODEL_NO_EFFECT);
                    }

//                    if (!empty($storyModel->story_model_detail_id)) {
//                        $storyModelLinks = $storyModelLinks->andFilterWhere([
//                            'story_model_detail_id' => $storyModel->story_model_detail_id,
//                        ]);
//                    } else {
//                        $storyModelLinks = $storyModelLinks->andFilterWhere([
//                            'story_model_id' => $storyModel->id,
//                        ]);
//                    }
//
//                    if (!empty($storyModel2->story_model_detail_id)) {
//                        $storyModelLinks = $storyModelLinks->andFilterWhere([
//                            'story_model_detail_id2' => $storyModel2->story_model_detail_id,
//                        ]);
//                    } else {
//                        $storyModelLinks = $storyModelLinks->andFilterWhere([
//                            'story_model_id2' => $storyModel2->id,
//                        ]);
//                    }

//                    $storyModelLink = $storyModelLinks->orderBy(['story_model_id' => SORT_ASC])
//                        ->one();

//                    if (!empty($storyModelLink)) {
//                        $newStoryModelId = $storyModelLink->eff_exec;
//                        $type = $storyModelLink->eff_type;
//
//                        if ($type == StoryModelsLink::EFF_TYPE_MODEL) {
//                            $newUserModel = Yii::$app->baggage->pickup($storyId, $sessionId, $newStoryModelId, $userId, 0);
//                        }
//
//                        $minCt = $storyModelLink->min_ct;
//                        if ($minCt > 0) {
//                            $userModel2->use_ct -= $minCt;
//                            $userModel2->save();
//                        }
//
//                        $retUserModel = $newUserModel;
//                        if (!empty($newUserModel)) {
//                            $retUserModel = $newUserModel->toArray();
//                            if (!empty($newUserModel->storyModel)) {
//                                $retUserModel['story_model'] = $newUserModel->storyModel->toArray();
//                                $retUserModel['story_model']['icon'] = Attachment::completeUrl($retUserModel['story_model']['icon'], true);
//                                $showRet['icon'] = $retUserModel['story_model']['icon'];
//                                $showRet['story_model_name'] = $newUserModel->storyModel->story_model_name;
//                            }
//                        }
//
//                        $res = [
//                            'user_model' => $bagageModel,
//                            'user_model2' => $userModel2,
//                            'new_user_model' => $retUserModel,
//                            'show' => $showRet,
//                        ];
//                    } else {
//                        throw new \yii\base\Exception('您的使用没有任何效果', ErrorCode::USER_MODEL_NO_EFFECT);
//                    }

                    break;
                case StoryModels::ACTIVE_TYPE_MODEL_UNIQUE:
                    // 宠物喂食
                    if ($storyModel->use_allow == StoryModels::USE_ALLOW_NEED_TARGET
                        && empty($targetStoryModelId)
                    ) {
                        throw new \yii\base\Exception('您需要选择一个对象', ErrorCode::USER_MODEL_NO_TARGET);
                    }

                    $targetStoryModel = StoryModels::find()
                        ->where([
                            'id' => (int)$targetStoryModelId,
                        ])
                        ->one();

                    // Todo: 临时处理，用镜像将使用对象强制放到另外一个模型上
                    if (!empty($targetStoryModel->story_model_prop)) {
                        $targetStoryModelProp = json_decode($targetStoryModel->story_model_prop, true);
                        if (!empty($targetStoryModelProp['mirror_story_model_id'])) {
                            $targetStoryModelId = $targetStoryModelProp['mirror_story_model_id'];
                            $targetStoryModel = StoryModels::find()
                                ->where([
                                    'id' => (int)$targetStoryModelId,
                                ])
                                ->one();
                        }
                    }

                    $storyModelLinks = StoryModelsLink::find()
                        ->where([
                            'story_id'          => (int)$storyId,
                            'story_model_id'    => $storyModel->id,
//                            'story_model_id2'   => $targetStoryModelId,
                        ]);
                    if (!empty($targetStoryModel->story_model_detail_id)) {
                        $storyModelLinks = $storyModelLinks->andFilterWhere([
                            'story_model_detail_id2' => $targetStoryModel->story_model_detail_id,
                        ]);
                    } else {
                        $storyModelLinks = $storyModelLinks->andFilterWhere([
                            'story_model_id2' => $targetStoryModel->id,
                        ]);
                    }
                    if (!empty($storyModel->use_group_name)) {
                        $storyModelLinks = $storyModelLinks->andFilterWhere([
                            'group_name' => $storyModel->use_group_name,
                        ]);
                    }
//                    var_dump($storyModelLinks->createCommand()->getRawSql());exit;
                    $storyModelLinks = $storyModelLinks
                        ->orderBy(['story_model_id' => SORT_ASC])
                        ->one();

                    if (empty($storyModelLinks)) {

                        throw new \yii\base\Exception('您的使用没有任何效果', ErrorCode::USER_MODEL_NO_EFFECT);
                    } else {

                        $ret = $storyModelLinks->eff_exec;
                        $type = $storyModelLinks->eff_type;
                    }

                    $res['code'] = 3;       // 完全匹配

                    if ($type == StoryModelsLink::EFF_TYPE_DIALOG) {
                        $res['type']  = $type;
                        $res['ret']   = $ret;
                    } elseif ($type == StoryModelsLink::EFF_TYPE_PROP_AND_DIALOG) {
                        $tarUserModel = UserModels::find()
                            ->where([
                                'story_model_id' => $targetStoryModelId,
                                'story_id' => $storyId,
                                'session_id' => $sessionId,
                                'user_id' => $userId,
                            ])
                            ->one();

                        // 按公式计算新属性
                        $tmpRes = Yii::$app->models->computeAddStoryModelLinkPropWithFormula([$ret], $tarUserModel);
                        if (!empty($tmpRes['data'])) {
                            $propRes = $tmpRes['data'];
                        } else {
                            $propRes = [];
                        }

                        $res['title'] = '喂食成功';
                        $res['html'] = '宠物吃得饱饱，';

                        if (!empty($tmpRes['up'])) {
                            foreach ($tmpRes['up'] as $k => $up) {
                                $res['html'] .= $up['title'] . '提升了' . $up['value'] . '点，';
                            }
                        }
                        $res['html'] .= '吃的可开心了。';
                        $dialogTag = 'normal';

                        if (!empty($propRes['prop'])) {
                            $tarUserModelProp = !empty($tarUserModel->user_model_prop) ? json_decode($tarUserModel->user_model_prop, true) : [];
                            $tarUserModelProp['prop'] = $propRes['prop'];

                            // 检查是否升级
                            $tmpUserModelProp = Yii::$app->models->checkLevel($tarUserModelProp);
                            $tarUserModelProp = $tmpUserModelProp['data'];
                            $tarUserModel->user_model_prop = json_encode($tarUserModelProp, true);
                            $tarUserModel->save();

                            if (!empty($tmpUserModelProp['isUp'])
                                && $tmpUserModelProp['isUp']
                            ) {
                                $res['title'] = '恭喜升级';
                                $res['html'] = '您成功升级至 ' . $tmpUserModelProp['data']['prop']['level'] . ' 级，属性提升！';
                                $dialogTag = 'update';
                            }
                        }

                        $retJson = json_decode($ret, true);
                        if (!empty($retJson['dialog'])) {
                            $res['type']  = StoryModelsLink::EFF_TYPE_DIALOG;
                            $res['ret']   = json_encode($retJson['dialog'][$dialogTag]);
                        } else {
                            $res['type']  = $type;
                            $res['ret']   = $ret;
                        }
                    }
                    $minCt = !empty($checkRet['min_ct']) ? $checkRet['min_ct'] : 0;
                    break;
                case StoryModels::ACTIVE_TYPE_MODEL:
                    if ($storyModel->use_allow == StoryModels::USE_ALLOW_NEED_TARGET
                        && empty($targetStoryModelId)
                    ) {
                        throw new \yii\base\Exception('您需要选择一个对象', ErrorCode::USER_MODEL_NO_TARGET);
                    }

                    $targetStoryModel = StoryModels::find()
                        ->where([
                            'id' => (int)$targetStoryModelId,
                        ])
                        ->one();

                    $storyModelLinks = StoryModelsLink::find()
                        ->where([
                            'story_id'          => (int)$storyId,
//                            'story_model_id'    => $storyModel->id,
//                            'story_model_id2'   => $targetStoryModelId,
                        ]);
                    if (!empty($targetStoryModel->story_model_detail_id)) {
                        $storyModelLinks = $storyModelLinks->andFilterWhere([
                            'story_model_detail_id2' => $targetStoryModel->story_model_detail_id,
                        ]);
                    } else {
                        $storyModelLinks = $storyModelLinks->andFilterWhere([
                            'story_model_id2' => $targetStoryModel->id,
                        ]);
                    }
                    if (!empty($storyModel->use_group_name)) {
                        $storyModelLinks = $storyModelLinks->andFilterWhere([
                            'group_name' => $storyModel->use_group_name,
                        ]);
                    }
//                    var_dump($storyModelLinks->createCommand()->getRawSql());exit;
                    $storyModelLinks = $storyModelLinks
                        ->orderBy(['story_model_id' => SORT_ASC])
                        ->all();

                        if (empty($storyModelLinks)) {
//                            $ret = json_encode([
//                                'WebViewOff'    => 1,
//                                'GotoAction'    => 'dialog-empty',
//                            ]);
//                            $type = StoryModelsLink::EFF_TYPE_DIALOG;

                            throw new \yii\base\Exception('您的使用没有任何效果', ErrorCode::USER_MODEL_NO_EFFECT);
                        } else {

                            $ret = '';
//                            foreach ($storyModelLinks as $storyModelLink) {
//                                if ($storyModelLink->story_model_id == '-1') {
//                                    // 如果完全没找到
//                                    $noFoundRet = $storyModelLink->eff_exec;
//                                    $noFoundType = $storyModelLink->eff_type;
//                                } else if ($storyModelLink->story_model_id == '-2') {
//                                    // 如果是部分完成
//                                    $partlyFoundRet = $storyModelLink->eff_exec;
//                                    $partlyFoundType = $storyModelLink->eff_type;
//                                } else if (
//                                    (!empty($storyModel->story_model_detail_id) && $storyModelLink->story_model_detail_id == $storyModel->story_model_detail_id)
//                                    ||
//                                    (!empty($storyModel->id) && $storyModelLink->story_model_id == $storyModel->id)
//                                ) {
//                                    $ret = $storyModelLink->eff_exec;
//                                    $type = $storyModelLink->eff_type;
//                                    break;
//                                }
//                            }
                            $checkRet = Yii::$app->models->checkUserModelUsedByStoryModel($storyModel, $targetStoryModel, $userId, $storyId, $sessionId, $userModelId);

//                            $userModelsUsedData = Yii::$app->models->getUserModelUsedByTarget($targetStoryModel->story_model_detail_id, $targetStoryModel->id, $userId, $storyId, $sessionId);
//
//                            $checkRet = Yii::$app->models->checkUserModelUsedByModels($storyModel, $storyModelLinks, $userModelsUsedData, $userId, $storyId, $sessionId);
//
//                            if ($checkRet['code'] == 0) {
////                                $ret = $noFoundRet;
////                                $type = $noFoundType;
//                                $minCt = 0;
//                            }
//
//                            if ($checkRet['code'] == 2) {
//                                // 使用并产出效果
//                                Yii::$app->models->addUserModelUsedByStoryModel($storyModel, $targetStoryModel, $userId, $storyId, $sessionId, UserModelsUsed::USE_STATUS_COMPLETED, $checkRet['group_name'], $checkRet['eff_exec'], $checkRet['eff_type']);
//                                Yii::$app->models->updateUserModelUsedByTargetStoryModel($targetStoryModel, $userId, $storyId, $sessionId, UserModelsUsed::USE_STATUS_COMPLETED);
//                                $minCt = 1;
//                            } elseif ($checkRet['code'] == 4) {
//                                // 使用，不完全，产出部分效果
//                                Yii::$app->models->addUserModelUsedByStoryModel($storyModel, $targetStoryModel, $userId, $storyId, $sessionId, UserModelsUsed::USE_STATUS_COMPLETED_PARTLY, $checkRet['group_name'], $checkRet['eff_exec'], $checkRet['eff_type']);
//                                $minCt = 1;
//                            } else {
//                                // 使用失败，没有效果
//                                $minCt = 0;
//                            }
                            
                            $ret = $checkRet['eff_exec'];
                            $type = $checkRet['eff_type'];
                        }
                        $res = $checkRet;

                        if ($type == StoryModelsLink::EFF_TYPE_DIALOG) {
                            $res['type']  = $type;
                            $res['ret']   = $ret;
                        } elseif ($type == StoryModelsLink::EFF_TYPE_PROP_AND_DIALOG) {
                            $tarUserModel = UserModels::find()
                                ->where([
                                    'story_model_id' => $targetStoryModelId,
                                    'story_id' => $storyId,
                                    'session_id' => $sessionId,
                                    'user_id' => $userId,
                                ])
                                ->one();

                            // 按公式计算新属性
                            $tmpRes = Yii::$app->models->computeAddStoryModelLinkPropWithFormula([$ret], $tarUserModel);
                            $propRes = $tmpRes['data'];

                            $res['title'] = '喂食成功';
                            $res['html'] = '宠物吃得饱饱，';

                            if (!empty($tmpRes['up'])) {
                                foreach ($tmpRes['up'] as $k => $up) {
                                    $res['html'] .= $up['title'] . '提升了' . $up['value'] . '点，';
                                }
                            }
                            $res['html'] .= '心满意足的睡着了。';

                            if (!empty($propRes['prop'])) {
                                $tarUserModelProp = !empty($tarUserModel->user_model_prop) ? json_decode($tarUserModel->user_model_prop, true) : [];
                                $tarUserModelProp['prop'] = $propRes['prop'];

                                // 检查是否升级
                                $tmpUserModelProp = Yii::$app->models->checkLevel($tarUserModelProp);
                                $tarUserModelProp = $tmpUserModelProp['data'];
                                $tarUserModel->user_model_prop = json_encode($tarUserModelProp, true);
                                $tarUserModel->save();

                                if (!empty($tmpUserModelProp['isUp'])
                                    && $tmpUserModelProp['isUp']
                                ) {
                                    $res['title'] = '恭喜升级';
                                    $res['html'] = '您成功升级至 ' . $tmpUserModelProp['data']['prop']['level'] . ' 级，属性提升！';
                                }
                            }

                            if (!empty($ret['dialog'])) {
                                $res['type']  = StoryModelsLink::EFF_TYPE_DIALOG;
                                $res['ret']   = $ret['dialog'];
                            } else {
                                $res['type']  = $type;
                                $res['ret']   = $ret;
                            }
                        }
                        $minCt = !empty($checkRet['min_ct']) ? $checkRet['min_ct'] : 0;
                    break;
                case StoryModels::ACTIVE_TYPE_MODEL_DISPLAY:
                    if ($this->_userInfo->is_new == 0) {
                        // 新用户
                        if ($storyModel->id == 442) {
                            $storyModel = StoryModels::find()->where(['id' => 443])->one();
                        }
                    }
                    if (empty($storyModel->active_model_inst_u_id)) {
                        $modelUId = $storyModel->model_inst_u_id;
                    } else {
                        $modelUId = $storyModel->active_model_inst_u_id;
                    }
                    $expirationInterval = 3600;
                    Yii::$app->act->add((int)$this->_sessionId, 0, (int)$this->_storyId, (int)$this->_userId, $modelUId, Actions::ACTION_TYPE_MODEL_DISPLAY, $expirationInterval);
                    $minCt = 0;
                    $res = [
                        'code' => 0,
                        'msg'  => 'success',
                    ];
                    break;
                case StoryModels::ACTIVE_TYPE_HTML:
                    $title = $storyModel->story_model_name;
                    $smHtml = $storyModel->story_model_html;
                    $desc = $storyModel->story_model_desc;

                    $html = '';
                    if (!empty($smHtml)) {
                        if (\common\helpers\Common::isJson($smHtml)) {
                            $smHtmlJson = json_decode($smHtml, true);
                            if (!empty($smHtmlJson['url'])) {

                                $params = [
                                    'user_id' => $userId,
                                    'story_id' => $storyId,
                                    'session_id' => $sessionId,
                                    'story_model_id' => $storyModel->id,
                                ];

                                $smHtmlJson['url'] = \common\helpers\Common::formatUrlParams($smHtmlJson['url'], $params);
                                // iframe
                                $html = '<iframe src="' . $smHtmlJson['url'] . '" frameborder=”no” border=”0″ marginwidth=”0″ marginheight=”0″ scrolling=”no” allowtransparency=”yes”';
                                if (!empty($smHtmlJson['height'])) {
                                    $html .= ' height="' . $smHtmlJson['height'] . '"';
                                }
                                if (!empty($smHtmlJson['width'])) {
                                    $html .= ' width="' . $smHtmlJson['width'] . '"';
                                }
                                $html .= '></iframe>';
                            }
                        } else {
                            $html = $smHtml;
                        }
                    }
//                    $html .= '<div style="padding: 10px;">';
//                    $html .= $desc;
//                    $html .= '</div>';

                    $minCt = 0;
                    $res = [
                        'code' => 0,
                        'type' => $storyModel->active_type,
                        'title' => $title,
                        'html' => $html,
                        'desc' => $desc,
                    ];
                    break;
                case StoryModels::ACTIVE_TYPE_SHOW:
                    $minCt = 0;

                    $title = $storyModel->story_model_name;
                    $image = $storyModel->story_model_image;
                    $desc = $storyModel->story_model_desc;

                    $html = '';
                    if (!empty($image)) {
                        $html = '<img src="' . Attachment::completeUrl($image) . '" style="width: 100%; height: auto;"/>';
                    }
//                    $html .= '<div style="padding: 10px;">';
//                    $html .= $desc;
//                    $html .= '</div>';

                    $minCt = 0;
                    $res = [
                        'code' => 0,
                        'type' => $storyModel->active_type,
                        'title' => $title,
                        'html' => $html,
                        'desc' => $desc,
                    ];
                    break;
                default:
                    break;
            }

            if ($minCt != 0) {
                $bagageModel->use_ct -= $minCt;
                if ($bagageModel->use_ct <= 0) {
                    $bagageModel->is_delete = Common::STATUS_DELETED;
                }
                $bagageModel->save();
            }
            $transaction->commit();

            return $res;

        } catch (\Exception $e) {
//            var_dump($e);
            $transaction->rollBack();
            throw $e;
//            return $this->fail($e->getMessage(), $e->getCode());
        }

    }

    public function pickupModels() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;
        $lockCt = !empty($this->_get['lock_ct']) ? $this->_get['lock_ct'] : 0;

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
            return $this->fail('没有找到物品', ErrorCode::DO_MODELS_PICK_UP_FAIL);
        }

        if (!empty($sessionModel)) {

            if ($sessionModel->last_operator_id != $userId
                && $sessionModel->session_model_status == SessionModels::SESSION_MODEL_STATUS_OPERATING
            ) {
                return $this->fail('物品正被他人拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
            } elseif ($sessionModel->is_unique == SessionModels::IS_UNIQUE_YES && $sessionModel->session_model_status == SessionModels::SESSION_MODEL_STATUS_PICKUP) {
                return $this->fail('物品可能已经被拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
            }
        }


//        $sessionModel->is_pickup = SessionModels::IS_PICKUP_YES;
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
                $userModelBaggage->model_id = $sessionModel->model_id;
                $userModelBaggage->story_model_id = $storyModelId;
                $userModelBaggage->story_model_detail_id = $storyModelDetailId;
                $userModelBaggage->session_model_id = $sessionModel->id;
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

            $result['data'] = $this->getSessionModels();

            $storyModel = StoryModels::find()
                ->with('buff')
                ->where(['id' => (int)$storyModelId])
                ->one();

//            if ($storyModel->active_next)

            $result['msg'] = '获取成功';

        } catch (\Exception $e) {
//            var_dump($e);
            $transaction->rollBack();
            throw $e;
//            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * 检查场次角色是不是已经全都满了
     * @return bool
     *
     */
    private function _checkSessionRole() {
        $storyRole = StoryRole::find()
            ->where(['story_id' => (int)$this->_storyId])
            ->all();

        $userStory = UserStory::find()
            ->where(['story_id' => (int)$this->_storyId, 'session_id' => (int)$this->_sessionId])
            ->all();

        $roleCt = [];
        foreach ($userStory as $us) {
            if (empty($roleCt[$us['role_id']])) {
                $roleCt[$us['role_id']] = 1;
            } else {
                $roleCt[$us['role_id']]++;
            }
        }

        $sRole = [];
        foreach ($storyRole as $rr) {
            $sRole[$rr['id']] = $rr['role_max_ct'];
        }

        foreach ($roleCt as $roleId => $ct) {
            if (!empty($sRole[$roleId]) && $ct < $sRole[$roleId]) {
                return false;
            }
        }

        return true;
    }
}