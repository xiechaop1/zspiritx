<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\process;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Active;
use common\helpers\Attachment;
use common\helpers\Model;
use common\models\Actions;
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
use common\models\StoryRole;
use common\models\StoryStages;
use common\models\User;
use common\models\UserKnowledge;
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
                case 'get_baggage_models':
                    $ret = $this->getBaggageModels();
                    break;
                case 'get_action_by_user':
                    $ret = $this->getActionByUser();
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
                ->where(['story_id' => (int)$this->_storyId]);
            $storyStages = $storyStages->all();

            foreach ($storyStages as $storyStage) {
                $checkSessionStage = SessionStages::find()
                    ->where([
                        'session_id'    => (int)$this->_userSessionInfo['id'],
                        'story_stage_id'    => (int)$storyStage['id'],
                    ]);
                $checkSessionStage = $checkSessionStage->one();

                if (!empty($checkSessionStage)) {
                    continue;
                }

                $sessionStageObj = new SessionStages();
                $sessionStageObj->story_stage_id = $storyStage['id'];
                $sessionStageObj->session_id = $this->_userSessionInfo['id'];
                $sessionStageObj->story_id = $this->_storyId;
                $sessionStageObj->snapshot = json_encode($sessionStageObj->toArray(), true);
                $sessionStageObj->save();
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
                    continue;
                }

                $sessionModel = new SessionModels();
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

            $knowledge = Knowledge::find()
                ->where([
                    'story_id'  => (int)$this->_storyId,
                    'knowledge_class' => Knowledge::KNOWLEDGE_CLASS_MISSSION
                ])
                ->orderBy(['sort_by' => SORT_ASC])
                ->one();

            $userKnowledge = UserKnowledge::find()
                ->where([
                    'session_id'  => (int)$this->_userSessionInfo['id'],
                    'user_id'   => (int)$this->_userId,
                    'knowledge_id'  => (int)$knowledge['id'],
                ])
                ->one();

            if (!empty($userKnowledge)) {
                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
            } else {
                $userKnowledge = new UserKnowledge();
                $userKnowledge->knowledge_id = $knowledge['id'];
                $userKnowledge->session_id = $this->_userSessionInfo['id'];
                $userKnowledge->user_id = $this->_userId;
                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
            }
            $userKnowledge->save();

            Yii::$app->act->add((int)$this->_userSessionInfo['id'], (int)$this->_userId, '开启任务：' . $knowledge['title'], Actions::ACTION_TYPE_MSG);


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

        $userSession = UserStory::find()
            ->where([
                'user_id' => (int)$this->_userId,
                'session_id' => (int)$this->_sessionId,
                'story_id'  => (int)$this->_storyId,
//                'building_id' => (int)$this->_buildingId,
//                'role_id' => (int)$roleId,
            ])->one();

        if (!empty($userSession)) {
            return $this->fail('玩家已经存在', ErrorCode::PLAYER_EXIST);
        }

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
                Yii::$app->act->add($this->_sessionId, 0, '游戏开始', Actions::ACTION_TYPE_ACTION);
            } else {
                $this->_sessionInfo->session_status = Session::SESSION_STATUS_READY;
                Yii::$app->act->add($this->_sessionId, 0, '新玩家加入', Actions::ACTION_TYPE_ACTION);
            }

            $this->_sessionInfo->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }



        return $userStory;
    }

    public function finish() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

        $goal = !empty($this->_get['goal']) ? $this->_get['goal'] : '';

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

            Yii::$app->act->add($this->_sessionId, 0, '游戏结束', Actions::ACTION_TYPE_ACTION);

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
                    $storyModel->dialog = Model::formatDialog($storyModel->dialog, $params);
                    $models[] = [
                        'session_model' => $sessionModel,
                        'story_model' => $storyModel,
                        'model' => $storyModel->model,
                    ];
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

    public function getActionByUser() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;

        $actions = Actions::find()
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
                'action_status' => Actions::ACTION_STATUS_NORMAL
            ])
//            ->createCommand()->getRawSql();
//        var_dump($actions);exit;
            ->all();

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

    public function useModel() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;
        $userModelId = !empty($this->_get['user_model_id']) ? $this->_get['user_model_id'] : 0;

        $bagageModel = UserModels::find()
            ->where([
                'id' => (int)$userModelId,
                'is_delete' => Common::STATUS_NORMAL,
//                'user_id' => (int)$userId,
//                'session_id' => (int)$sessionId,
            ])
            ->one();

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

//            $activeArray = \common\helpers\Model::decodeActive($storyModel->activeNext);
            if ($storyModel->active_type == StoryModels::ACTIVE_TYPE_BUFF) {
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
            }

            $bagageModel->use_ct -= 1;
            if ($bagageModel->use_ct <= 0) {
                $bagageModel->is_delete = Common::STATUS_DELETED;
            }
            $bagageModel->save();
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }

    }

    public function pickupModels() {
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
            return $this->fail($e->getMessage(), $e->getCode());
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