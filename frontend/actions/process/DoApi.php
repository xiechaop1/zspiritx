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
use common\models\StoryRole;
use common\models\StoryStages;
use common\models\User;
use common\models\UserKnowledge;
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

        if ( empty($lastSessionStageId)
            || empty($lastStoryStageId)
        ) {
            $sessionStage = SessionStages::find()
                ->where([
                    'story_id'      => (int)$this->_storyId,
                    'session_id'    => (int)$this->_sessionId,
                ])
                ->andFilterWhere(['>', 'sort_by', 0])
                ->one();

            $lastStoryStageId   = $sessionStage->story_stage_id;
            $lastSessionStageId = $sessionStage->id;

        }

//        Yii::$app->knowledge->setByItem($lastStoryStageId, ItemKnowledge::ITEM_TYPE_STAGE, (int)$this->_sessionId, $lastSessionStageId, (int)$this->_userId, (int)$this->_storyId);

        $storyStage = StoryStages::findOne($lastStoryStageId);
        $expirationInterval = 60;        // 消息超时时间
        Yii::$app->act->add((int)$this->_sessionId, $lastSessionStageId, (int)$this->_storyId, (int)$this->_userId, $storyStage['stage_u_id'], Actions::ACTION_TYPE_CHANGE_STAGE, $expirationInterval);

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

        if (!empty($sessionStages)) {
            foreach ($sessionStages as $sessionStage) {
                if (!empty($sessionStage->stage->bgm)) {
                    $sessionStage->stage->bgm = Attachment::completeUrl($sessionStage->stage->bgm, false);
                }
            }
        }

        return $sessionStages;
    }

    public function getSessionModelsByStage() {
        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

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

        foreach ($sessoinStages as $sessionStage) {
            $sessionModels = $sessionStage->models;
            $models = [];
            if (!empty($sessionModels)) {
//                $models = [];
                foreach ($sessionModels as $sessionModel) {
                    $sessModel = $sessionModel;
//                    $storyModel = json_decode($sessModel['snapshot'], true);
                    $storyModel = $sessionModel->storymodel;
                    if (!empty($storyModel)) {
                        $storyModel = Model::combineStoryModelWithDetail($storyModel);
                        if (!empty($storyModel->dialog)) {
                            $params['story_model_id'] = $storyModel->id;
                            $params['model_id'] = $storyModel->model_id;
                            $params['story_model_detail_id'] = $storyModel->story_model_detail_id;
                            $params['model_inst_u_id'] = $storyModel->model_inst_u_id;
                            $storyModel->dialog = Model::formatDialog($storyModel, $params);
                        }
                        $models[] = [
                            'session_model' => $sessionModel,
                            'story_model' => $storyModel,
                            'model' => $storyModel->model,
                        ];
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
                $ret['value'] = 2;
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


        $targetStoryModelId = !empty($this->_get['target_story_model_id']) ? $this->_get['target_story_model_id'] : 0;
        $targetStoryModelDetailId = !empty($this->_get['target_story_model_detail_id']) ? $this->_get['target_story_model_detail_id'] : 0;
        $userModelId = !empty($this->_get['user_model_id']) ? $this->_get['user_model_id'] : 0;


        $bagageModel = UserModels::find()
            ->where([
                'id' => (int)$userModelId,
                'is_delete' => Common::STATUS_NORMAL,
//                'user_id' => (int)$userId,
//                'session_id' => (int)$sessionId,
            ]);

        $bagageModel = $bagageModel->one();

        if (empty($bagageModel)) {
            return $this->fail('背包中没有该道具', ErrorCode::USER_MODEL_NOT_FOUND);
        }

        if ($bagageModel->use_ct <= 0) {
            return $this->fail('该道具使用次数已用完', ErrorCode::USER_MODEL_NOT_ENOUGH);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $storyModel = $bagageModel->storyModel;
            if (empty($storyModel)) {
                return $this->fail('没有找到该道具', ErrorCode::USER_MODEL_NOT_FOUND);
            }

            if ($storyModel->use_allow == StoryModels::USE_ALLOW_NOT) {
                return $this->fail('该道具不允许使用', ErrorCode::USER_MODEL_NOT_ALLOW);
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
                            'story_id'          => $storyId,
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
                        }
                        $minCt = !empty($checkRet['min_ct']) ? $checkRet['min_ct'] : 0;
                    break;
                case StoryModels::ACTIVE_TYPE_MODEL_DISPLAY:
                    $modelUId = $storyModel->model_inst_u_id;
                    $expirationInterval = 3600;
                    Yii::$app->act->add((int)$this->_sessionId, 0, (int)$this->_storyId, (int)$this->_userId, $modelUId, Actions::ACTION_TYPE_MODEL_DISPLAY, $expirationInterval);
                    $minCt = 0;
                    $res = [
                        'code' => 0,
                        'msg'  => 'success',
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