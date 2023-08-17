<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\process;


use common\definitions\ErrorCode;
use common\models\Log;
use common\models\Order;
use common\models\Session;
use common\models\SessionModels;
use common\models\Story;
use common\models\StoryExtend;
use common\models\StoryModels;
use common\models\StoryRole;
use common\models\User;
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

    private $_sessionInfo;

    private $_buildingId;

    public function run()
    {

        try {
            $this->valToken();

            $this->_get = Yii::$app->request->get();

            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

            $this->_buildingId = !empty($this->_get['building_id']) ? $this->_get['building_id'] : 0;

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

            $this->_sessionInfo = Session::find()
                ->where([
                    'user_id' => (int)$this->_userId,
                    'story_id' => (int)$this->_storyId,
                    'session_status' => [
                        Session::SESSION_STATUS_INIT,
                        Session::SESSION_STATUS_READY,
                        Session::SESSION_STATUS_START,
                    ],
                ])
                ->one();

            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'init':
                    $ret = $this->initdata();
                    break;
                case 'join':
                    $ret = $this->join();
                    break;
                case 'get_session_models':
                    $ret = $this->getSessionModels();
                    break;
                case 'pickup':
                    $ret = $this->pickupModels();
                    break;
                case 'get_baggage_models':
                    $ret = $this->getBaggageModels();
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

            if (empty($this->_sessionInfo)) {
                $sessionObj = new Session();
                $sessionObj->session_name = $this->_userInfo['user_name'] . ' 创建 ' . $this->_storyInfo['title'] . ' ' . ' 场次';
                $sessionObj->user_id = $this->_userId;
                $sessionObj->story_id = $this->_storyId;
                $sessionObj->session_status = Session::SESSION_STATUS_INIT;
                $ret = $sessionObj->save();

                $sessionId = Yii::$app->db->getLastInsertID();
                $sessionObj['id'] = $sessionId;
                $this->_sessionInfo = $sessionObj;
            }

            $storyModels = StoryModels::find()
                ->where(['story_id' => (int)$this->_storyId]);
            if (!empty($this->_buildingId)) {
                $storyModels->andFilterWhere(['building_id' => (int)$this->_buildingId]);
            }
            $storyModels = $storyModels->all();
            foreach ($storyModels as $storyModel) {
                $sessionModel = new SessionModels();
                foreach ($storyModel as $key => $value) {
                    if (in_array($key, ['id', 'story_id'])) {
                        continue;
                    }
                    $sessionModel->$key = $value;
                }
                $sessionModel->story_model_id = $storyModel->id;
                $sessionModel->session_id = $this->_sessionInfo['id'];
                $sessionModel->snapshot = json_encode($storyModel->toArray(), true);
                $sessionModel->is_pickup = 0;
                $sessionModel->save();
            }



            $transaction->commit();
            $ret = true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $ret;
    }

    /**
     * 加入剧本
     */
    public function join() {
        $roleId = !empty($this->_get['role_id']) ? $this->_get['role_id'] : 0;

        if (empty($this->_sessionInfo)) {
            return $this->fail('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        if (empty($roleId)) {
            return $this->fail('请您给出角色信息', ErrorCode::ROLE_NOT_FOUND);
        }

        $userRoleCt = UserStory::find()
            ->where([
                'user_id' => (int)$this->_userId,
                'story_id' => (int)$this->_storyId,
                'session_id' => (int)$this->_sessionId,
                'role_id' => (int)$roleId,
                'building_id' => (int)$this->_buildingId,
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
        try {
            $ret = $userStory->save();

            if ($this->_checkSessionRole()) {
                $this->_sessionInfo->session_status = Session::SESSION_STATUS_START;
            } else {
                $this->_sessionInfo->session_status = Session::SESSION_STATUS_READY;
            }

            $this->_sessionInfo->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }



        return $ret;
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

    public function getSessionModels(){
        $preStoryModelId = !empty($this->_get['pre_story_model_id']) ? $this->_get['pre_story_model_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        if (!empty($preStoryModelId)) {
            $preModel = SessionModels::find()
                ->where([
                    'story_model_id' => (int)$preStoryModelId,
                    'session_id'    => (int)$sessionId,
//                    'story_id'      => (int)$storyId,
                    'is_pickup'     => SessionModels::IS_PICKUP_YES,
                ])
                ->one();
            if (empty($preModel)) {
//                return $this->fail('物品不存在', ErrorCode::DO_PRE_MODELS_NOT_FOUND);
                return [];
            }

        }

        $sessModels = SessionModels::find()
            ->with('model')
            ->where([
                'session_id' => (int)$sessionId,
//                'story_id'  => (int)$storyId,
            ]);
        if (!empty($preStoryModelId)) {
            $sessModels = $sessModels->andFilterWhere([
                'pre_story_model_id' => (int)$preStoryModelId,
            ]);
        } else {
            $sessModels = $sessModels->andFilterWhere([
                'or',
                ['pre_story_model_id' => $preStoryModelId,],
                ['is_set' => SessionModels::IS_SET_YES,]
            ]);
        }
            $sessModels = $sessModels->andFilterWhere([
                'or',
                ['is_unique' => SessionModels::IS_UNIQUE_NO,],
                ['is_pickup' => SessionModels::IS_PICKUP_NO,]
            ]);
//        }

//        var_dump($ret->createCommand()->getRawSql());exit;
        $sessModels = $sessModels->all();

        try {
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($sessModels as $sModel) {
                $sModel->is_set = SessionModels::IS_SET_YES;
                $sModel->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $sessModels;

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
                'is_pickup' => SessionModels::IS_PICKUP_NO,
            ])
            ->one();

        if (empty($sessionModel)) {
            return $this->fail('物品可能已经被拾取', ErrorCode::DO_MODELS_PICK_UP_FAIL);
        }

        $sessionModel->is_pickup = SessionModels::IS_PICKUP_YES;
        try {
            $ret = $sessionModel->save();

            $userModelBaggage = UserModels::find()
                ->where([
                    'user_id'           => (int)$userId,
                    'session_id'        => (int)$sessionId,
                    'model_id'          => $sessionModel->model_id,
                    'story_model_id'    => (int)$storyModelId,
                    'session_model_id'  => $sessionModel->id,
                ])
                ->one();
            if (empty($userModelBaggage)) {
                $userModel = new UserModels();
                $userModel->user_id = $userId;
                $userModel->session_id = $sessionId;
                $userModel->model_id = $sessionModel->model_id;
                $userModel->story_model_id = $storyModelId;
                $userModel->session_model_id = $sessionModel->id;
                $ret = $userModel->save();
            }
            $transaction->commit();

            $this->_get['pre_story_model_id'] = $storyModelId;

            $ret = $this->getSessionModels();

        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $ret;
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
            $roleCt[$us['role_id']]++;
        }

        foreach ($roleCt as $roleId => $ct) {
            if ($ct < $storyRole[$roleId]['role_max_ct']) {
                return false;
            }
        }

        return true;
    }
}